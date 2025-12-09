<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="<?= BASE_URL ?>/assets/css/login.css">
</head>
<body>
    <?php
    $errorKey = $_GET['error'] ?? '';
    $errorMsg = '';
    switch ($errorKey) {
        case 'user_not_found':
            $errorMsg = 'Utilisateur ou mot de passe incorrect.';
            break;
        case 'e_utiliser':
            $errorMsg = 'Email ou pseudo déjà utilisé.';
            break;
        case 'acces':
            $errorMsg = 'Compte créé, vous pouvez vous connecter.';
            break;
        default:
            $errorMsg = '';
    }
    ?>
    <?php if ($errorMsg): ?>
        <div class="alert" style="background:#fee2e2;color:#b91c1c;padding:10px 14px;margin:10px auto;max-width:480px;border-radius:8px;text-align:center;">
            <?= htmlspecialchars($errorMsg) ?>
        </div>
    <?php endif; ?>
    <div class="page">
        <!-- Signup Form -->
        <div class="form signup">
            <form action="<?= BASE_URL ?>/Auth.php" method="post">
                <h2 class="signup-title">Sign UP</h2>
                <input type="hidden" name="form_type" value="signup">
                <div class="input">
                    <input type="text" required name="name" id="name">
                    <label for="name">Username</label>
                </div>
                <div class="input">
                    <input type="email" required name="email" id="email">
                    <label for="email">Email</label>
                </div> 
                <div class="input">
                    <input type="password" required name="mdp" id="mdp">
                    <label for="mdp">Password</label>
                </div>
                <div class="input">
                    <input type="password" required name="confirm_mdp" id="confirm_mdp">
                    <label for="confirm_mdp">Confirm Password</label>
                </div>
                <div class="input">
                    <input type="text" required name="date" id="date" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                    <label for="date">Date de naissance</label>
                </div>
                <button type="submit" class="btnn">Sign-Up</button>
                <div class="sign">
                    <p class="t">Already have an account?<a href="#" class="signIn">Sign In</a></p>
                </div>
            </form>
        </div>

        <!-- Login Form -->
        <div class="form login">
            <form action="<?= BASE_URL ?>/Auth.php" method="post">
                <h2>Mind Arena</h2>
                <h2>Login</h2>
                <input type="hidden" name="form_type" value="login">
                <div class="input">
                    <input type="text" required name="userl" id="userl">
                    <label for="userl">Username</label>
                </div>
                <div class="input">
                    <input type="password" required name="mdpl" id="mdpl">
                    <label for="mdpl">Password</label>
                </div>
                <div class="mdpoub">
                    <a href="<?= BASE_URL ?>/index.php?action=forgot">Forgot Password?</a>
                </div>
                <button type="submit" class="btnn">Connect</button>
                <div class="sign">
                    <p>Don't have an account?<a href="#" class="signUp">Sign Up</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL ?>/assets/js/login.js"></script>
</body>
<footer>
</footer>
</html>

