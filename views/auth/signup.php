<?php
$title = 'Create an account';
$pageScripts = ['integration.js'];
require __DIR__ . '/../partials/Header.php';
?>

<main class="container my-4" id="main-content">
  <section class="card court-auth mx-auto" aria-labelledby="signup-title">
    <div class="card-body p-4">
      <h1 class="court-page-title mb-3" id="signup-title">Create an account</h1>
      <?php if (!empty($error)): ?>
        <p class="alert alert-danger" role="alert"><?= courtEscape($error) ?></p>
      <?php endif; ?>
      <form action="<?= courtEscape(courtUrl('index.php?url=auth/signup')) ?>" method="post" id="signup-form" class="d-grid gap-3">
        <div>
          <label for="signup-name" class="form-label">Full name</label>
          <input class="form-control" type="text" id="signup-name" name="name" autocomplete="name" required
                 value="<?= courtEscape(is_string($_POST['name'] ?? null) ? $_POST['name'] : '') ?>">
        </div>
        <div>
          <label for="signup-email" class="form-label">Email</label>
          <input class="form-control" type="email" id="signup-email" name="email" autocomplete="email" maxlength="254" required
                 value="<?= courtEscape(is_string($_POST['email'] ?? null) ? $_POST['email'] : '') ?>">
        </div>
        <div>
          <label for="account-role" class="form-label">Account type</label>
          <select class="form-select" id="account-role" name="userType" required>
            <option value="user">Customer account</option>
            <option value="court_owner" <?= ($_POST['userType'] ?? '') === 'court_owner' ? 'selected' : '' ?>>Venue owner account</option>
          </select>
        </div>
        <div>
          <label for="password" class="form-label">Password</label>
          <input class="form-control" type="password" id="password" name="password" autocomplete="new-password"
                 aria-describedby="password-rules" required>
          <p class="form-text mb-0" id="password-rules">Use at least 8 characters, including an uppercase letter, a lowercase letter, a number and a special character.</p>
        </div>
        <div>
          <label for="confirm-password" class="form-label">Confirm password</label>
          <input class="form-control" type="password" id="confirm-password" name="confirmPassword" autocomplete="new-password"
                 aria-describedby="password-match" required>
          <p id="password-match" class="form-text mb-0 text-danger-emphasis" aria-live="polite"></p>
        </div>
        <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
        <button class="btn btn-primary" type="submit">Create account</button>
      </form>
      <p class="mt-3 mb-0">Already registered? <a href="<?= courtEscape(courtUrl('index.php?url=auth/login')) ?>">Sign in</a></p>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../partials/Footer.php'; ?>
