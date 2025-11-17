<?php

class AdminController {

    private $forum;
    private $publication;

    public function __construct($pdo) {
        require_once __DIR__ . '/../models/Forum.php';
        require_once __DIR__ . '/../models/Publication.php';

        $this->forum = new Forum($pdo);
        $this->publication = new Publication($pdo);
    }

    /* ⭐ FIXED RENDER ENGINE ⭐ */
    private function render($viewName, $pageTitle, $active, $data = []) {

        // EXPORT ALL VARIABLES TO THE VIEW
        extract($data);

        // Correct absolute path to the content view
        $viewFile = __DIR__ . "/../views/admin/" . $viewName;

        // Correct absolute path to layout
        $layoutFile = __DIR__ . "/../views/admin/layout.php";

        if (!file_exists($viewFile)) {
            die("<h1 style='color:red;text-align:center;'>View not found: $viewFile</h1>");
        }

        if (!file_exists($layoutFile)) {
            die("<h1 style='color:red;text-align:center;'>LAYOUT NOT FOUND: $layoutFile</h1>");
        }

        // LOAD LAYOUT (which includes view inside it)
        include $layoutFile;
    }


    /* ===============================
       ========== DASHBOARD ===========
       =============================== */

    public function dashboard() {
        $this->render("dashboard.php", "Dashboard", "dashboard", [
            "totalForums" => $this->forum->count(),
            "totalPubs"   => $this->publication->count()
        ]);
    }


    /* ===============================
       ============ FORUMS ============
       =============================== */

    public function forumList() {
        $this->render("forumList.php", "Forums", "forums", [
            "forums" => $this->forum->getAll()
        ]);
    }

    public function forumAdd() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->forum->create($_POST["title"], $_POST["description"]);
            header("Location: admin.php?action=forums");
            exit;
        }

        $this->render("forumAdd.php", "Ajouter Forum", "forums");
    }

    public function forumEdit() {
        $id = $_GET["id"];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->forum->update($id, $_POST["title"], $_POST["description"]);
            header("Location: admin.php?action=forums");
            exit;
        }

        $this->render("forumEdit.php", "Modifier Forum", "forums", [
            "forum" => $this->forum->getById($id)
        ]);
    }

    public function forumDelete() {
        $this->forum->delete($_GET["id"]);
        header("Location: admin.php?action=forums");
        exit;
    }


    /* ===============================
       ========= PUBLICATIONS =========
       =============================== */

    public function publicationList() {
        $this->render("publicationList.php", "Publications", "publications", [
            "publications" => $this->publication->getAll()
        ]);
    }

    public function publicationAdd() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->publication->create($_POST["forum_id"], $_POST["author"], $_POST["content"]);
            header("Location: admin.php?action=publications");
            exit;
        }

        $this->render("publicationAdd.php", "Ajouter Publication", "publications", [
            "forums" => $this->forum->getAll()
        ]);
    }

    public function publicationEdit() {
        $id = $_GET["id"];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->publication->update($id, $_POST["forum_id"], $_POST["author"], $_POST["content"]);
            header("Location: admin.php?action=publications");
            exit;
        }

        $this->render("publicationEdit.php", "Modifier Publication", "publications", [
            "pub"    => $this->publication->getById($id),
            "forums" => $this->forum->getAll()
        ]);
    }

    public function publicationDelete() {
        $this->publication->delete($_GET["id"]);
        header("Location: admin.php?action=publications");
        exit;
    }
}
