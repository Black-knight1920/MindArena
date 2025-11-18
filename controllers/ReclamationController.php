<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../models/Reclamation.php';

class ReclamationController
{
    private $model;

    public function __construct() {
        global $pdo;
        $this->model = new Reclamation($pdo);
    }

    // -----------------------------
    // LIST
    // -----------------------------
    public function list() {
        $recs = $this->model->getAll();
        include __DIR__ . '/../views/back/reclamation_list.php';
    }

    // -----------------------------
    // CREATE + VALIDATION
    // -----------------------------
    public function create() {

        // 1. CHECK REQUIRED FIELDS
        if (
            empty($_POST['full_name']) ||
            empty($_POST['email']) ||
            empty($_POST['subject']) ||
            empty($_POST['message'])
        ) {
            $error = "All fields are required.";
            include __DIR__ . '/../views/contact.html';
            return;
        }

        // 2. EMAIL FORMAT
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
            include __DIR__ . '/../views/contact.html';
            return;
        }

        // 3. LENGTH VALIDATION
        if (strlen($_POST['subject']) < 3) {
            $error = "Subject must be at least 3 characters.";
            include __DIR__ . '/../views/contact.html';
            return;
        }

        if (strlen($_POST['message']) < 10) {
            $error = "Message must be at least 10 characters.";
            include __DIR__ . '/../views/contact.html';
            return;
        }

        // 4. CLEAN DATA (SECURITY)
        $full_name = htmlspecialchars(trim($_POST['full_name']));
        $email     = htmlspecialchars(trim($_POST['email']));
        $subject   = htmlspecialchars(trim($_POST['subject']));
        $message   = htmlspecialchars(trim($_POST['message']));

        // 5. INSERT INTO DATABASE
        $this->model->add($full_name, $email, $subject, $message);

        // 6. SUCCESS REDIRECT
        header("Location: index.php?action=contact_success");
        exit;
    }

    // -----------------------------
    // DELETE
    // -----------------------------
    public function delete($id) {
        $this->model->delete($id);
        header("Location: index.php?action=reclamation_list");
        exit;
    }
}
