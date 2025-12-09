<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset='utf-8'>
  <title>Reset Password</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel='stylesheet' type='text/css' media='screen' href='<?= BASE_URL ?>/assets/css/reset_password.css'>
</head>
<body>
  <div class="page">
    <div class="form forgot">
      <form action="<?= BASE_URL ?>/index.php?action=reset" method="POST">
        <h2>Reset Password</h2>

        <input type="hidden" name="token" id="token" value="<?php echo isset($_GET['token']) ? $_GET['token'] : ''; ?>">


        <div class="input">
          <input type="password" required name="newmdp" id="newmdp">
          <label for="newmdp">New Password</label>
        </div>

        <button type="submit" class="btnn">Reset</button>

        <div class="login">
          <a href="<?= BASE_URL ?>/index.php?action=login">Log In</a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../assets/js/reset_password.js"></script>
</body>
</html>

