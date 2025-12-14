<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Forgot Password</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel='stylesheet' type='text/css' media='screen' href='<?= BASE_URL ?>/assets/css/forgot.css'>
</head>
<body>
    <div class="page">
        <div class="form forgot">
      <form action="<?= BASE_URL ?>/index.php?action=forgot" method="POST">
                <h2>Forgot Password?</h2>
                <input type="hidden" name="form_type" value="login">
                <div class="input">
                    <input type="email" required name="email" id="email">
                    <label for="">Email de votre compte</label>
                </div>
                <button type="submit" class="btnn">Continue</button>

                <div class="login">
          <a href="<?= BASE_URL ?>/index.php?action=login">Log In</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/js/forgot.js"></script>
</body>
</html>

