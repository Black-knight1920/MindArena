<?php

try {
    $conn = new PDO("mysql:host=localhost;dbname=projetj;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<script>alert('Erreur de connexion à la base de données');</script>");
}

// signup wala login
$formType = isset($_POST["form_type"]) ? $_POST["form_type"] : "";

// --- LOGIN ---
if ($formType === "login") {
    $usrl = isset($_POST["userl"]) ? $_POST["userl"] : "";
    $mdpl = isset($_POST["mdpl"]) ? $_POST["mdpl"] : "";
    $mdplh = md5($mdpl);

    try {
        //Check user is an admin
        $adminCheck = $conn->prepare("SELECT * FROM admin WHERE name = :name AND mdpa = :mdp");
        $adminCheck->execute(array(':name' => $usrl, ':mdp' => $mdplh));

        if ($adminCheck->rowCount() > 0) {
            // Admin found → redirect to dashboard
            header("Location: http://127.0.0.1/projet-Copie%20-%20Copie/view/back/back.php");
            exit();
        }

        // Check if the user is a regular user
        $req = $conn->prepare("SELECT * FROM user WHERE name = :name AND mdp = :mdp");
        $req->execute(array(':name' => $usrl, ':mdp' => $mdplh));

        if ($req->rowCount() == 0) {
            
            header("Location: login.html?error=user_not_found");
            exit();
        } else {
            // main ken hathret
            header("Location: user_home.php"); 
            exit();
        }

    } catch (PDOException $e) {
        echo "<script>alert('Erreur requête SQL');</script>";
    }
}

// --- SIGNUP ---
elseif ($formType === "signup") {
    $usr = isset($_POST["name"]) ? $_POST["name"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $mdp = isset($_POST["mdp"]) ? $_POST["mdp"] : "";
    $dateNaissance = isset($_POST["date"]) ? $_POST["date"] : "";  //added
    $dateInscrit = date("Y-m-d");  //date ltm tee creation
    $mdph = md5($mdp);

    try {
        $acc = $conn->prepare("INSERT INTO user (name, email, mdp, `date-naissance`, `date-inscrit`) 
                               VALUES (:name, :email, :mdp, :dateN, :dateI)");
        $s = $acc->execute(array(
            ':name' => $usr,
            ':email' => $email,
            ':mdp' => $mdph,
            ':dateN' => $dateNaissance,
            ':dateI' => $dateInscrit
        ));

        if ($s) {
            header("Location: login.html?error=acces");
            exit();
        } else {
            header("Location: login.html?error=e_utiliser");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: login.html?error=e_utiliser");
        exit();
    }
}
?>
