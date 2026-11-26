<?php
$title = 'Sign in';
require __DIR__ . '/../partials/Header.php';
?>

<main class="container my-4" id="main-content">
  <section class="card court-auth mx-auto" aria-labelledby="login-title">
    <div class="card-body p-4">
      <h1 class="court-page-title mb-3" id="login-title">Sign in</h1>
      <?php if (!empty($error)): ?>
        <p class="alert alert-danger" role="alert"><?= courtEscape($error) ?></p>
      <?php endif; ?>
      <form action="<?= courtEscape(courtUrl('index.php?url=auth/login')) ?>" method="post" class="d-grid gap-3">
        <div>
          <label for="loginType" class="form-label">Account portal</label>
          <select class="form-select" id="loginType" name="loginType" required>
            <option value="customer_portal">Customer / Owner / Admin</option>
            <option value="staff_app" <?= ($_POST['loginType'] ?? '') === 'staff_app' ? 'selected' : '' ?>>Staff</option>
          </select>
        </div>
        <div>
          <label for="login-email" class="form-label">Email</label>
          <input class="form-control" type="email" id="login-email" name="email" autocomplete="username" maxlength="100" required
                 value="<?= courtEscape(is_string($_POST['email'] ?? null) ? $_POST['email'] : '') ?>">
        </div>
        <div>
          <label for="login-password" class="form-label">Password</label>
          <input class="form-control" type="password" id="login-password" name="password" autocomplete="current-password" required>
        </div>
        <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
        <button class="btn btn-primary" type="submit">Sign in</button>
      </form>
      <p class="mt-3 mb-0">New to CourtOne? <a href="<?= courtEscape(courtUrl('index.php?url=auth/signup')) ?>">Create an account</a></p>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../partials/Footer.php'; ?>
