<?php

class LoginRateLimiter extends Model {
    private const WINDOW_SECONDS = 15 * 60;
    private const EMAIL_LIMIT = 5;
    private const IP_LIMIT = 30;

    public function attempt(string $email, string $remoteAddress, callable $authenticate): array {
        $ipKey = hash('sha256', $remoteAddress);
        $emailKey = hash('sha256', strtolower($email));
        $this->pruneExpired(time());

        $this->db->beginTransaction();
        try {
            // Hold the IP row lock through authentication so separate sessions cannot spend the same attempt.
            $lock = $this->db->prepare(
                "INSERT INTO login_rate_limits (ip_key, expires_at, failed_attempts, email_failures)
                 VALUES (?, 0, 0, '{}') ON DUPLICATE KEY UPDATE ip_key = ip_key"
            );
            $lock->execute([$ipKey]);

            $load = $this->db->prepare(
                'SELECT expires_at, failed_attempts, email_failures FROM login_rate_limits WHERE ip_key = ?'
            );
            $load->execute([$ipKey]);
            $window = $load->fetch();
            $now = time();
            $expiresAt = (int) $window['expires_at'];
            $ipFailures = 0;
            $emailFailures = [];
            if ($expiresAt > $now) {
                $ipFailures = (int) $window['failed_attempts'];
                $emailFailures = json_decode($window['email_failures'], true, 512, JSON_THROW_ON_ERROR);
            }
            $emailCount = $emailFailures[$emailKey] ?? 0;

            if ($ipFailures >= self::IP_LIMIT || $emailCount >= self::EMAIL_LIMIT) {
                $this->db->commit();
                return ['user' => null, 'retryAfter' => $expiresAt - $now];
            }

            $user = $authenticate();
            if ($user === null) {
                if ($ipFailures === 0) {
                    $expiresAt = $now + self::WINDOW_SECONDS;
                }
                $emailFailures[$emailKey] = $emailCount + 1;
                $this->saveWindow($ipKey, $expiresAt, $ipFailures + 1, $emailFailures);
            } elseif ($ipFailures === 0) {
                $delete = $this->db->prepare('DELETE FROM login_rate_limits WHERE ip_key = ?');
                $delete->execute([$ipKey]);
            } elseif ($emailCount > 0) {
                unset($emailFailures[$emailKey]);
                $this->saveWindow($ipKey, $expiresAt, $ipFailures, $emailFailures);
            }

            $this->db->commit();
            return ['user' => $user, 'retryAfter' => 0];
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    private function saveWindow(string $ipKey, int $expiresAt, int $ipFailures, array $emailFailures): void {
        $update = $this->db->prepare(
            'UPDATE login_rate_limits SET expires_at = ?, failed_attempts = ?, email_failures = ? WHERE ip_key = ?'
        );
        $update->execute([$expiresAt, $ipFailures, json_encode((object) $emailFailures, JSON_THROW_ON_ERROR), $ipKey]);
    }

    private function pruneExpired(int $now): void {
        $expired = $this->db->prepare(
            'SELECT ip_key FROM login_rate_limits WHERE expires_at <= ? ORDER BY expires_at LIMIT 20'
        );
        $expired->execute([$now]);
        $keys = $expired->fetchAll(PDO::FETCH_COLUMN);
        if (!$keys) {
            return;
        }

        // Recheck expiry when deleting in case another request has already started a new window.
        $delete = $this->db->prepare('DELETE FROM login_rate_limits WHERE ip_key = ? AND expires_at <= ?');
        foreach ($keys as $key) {
            $delete->execute([$key, $now]);
        }
    }
}
