<?php
require_once __DIR__ . '/../models/Publication.php';
require_once __DIR__ . '/../models/Forum.php';

class PublicationController {
    private $model;
    private $forums;

    public function __construct($pdo) {
        $this->model = new Publication($pdo);
        $this->forums = new Forum($pdo);
    }

    public function listFront() {

        // Ensure defaults
        $forum = null;
        $publications = [];

        if (isset($_GET['forum_id'])) {
            $forumId = $_GET['forum_id'];

            // GET THE REAL FORUM ARRAY
            $forum = $this->forums->getById($forumId);

            // If forum not found
            if (!$forum) {
                die("<h2 style='color:white;text-align:center;margin-top:40px;'>❌ Forum introuvable</h2>");
            }

            // Fetch all publications for this forum
            $publications = $this->model->getByForum($forumId);
        }

        // Make $forum available to the view
        include __DIR__ . '/../views/front/publicationList.php';
    }


    public function addFront() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $forum_id = $_POST['forum_id'];
            $author   = trim($_POST['author']);
            $content  = trim($_POST['content']);

            if (!$author) $errors[] = "Nom requis";
            if (!$content) $errors[] = "Contenu requis";

            if (empty($errors)) {
                $this->model->create($forum_id, $author, $content);
                header("Location: /mindarena_forum/front/publications?forum_id=$forum_id");
                exit;
            }
        }

        include __DIR__ . '/../views/front/publicationAdd.php';
    }


    public function deleteFront($id) {

        $pub = $this->model->getById($id);

        if (!$pub) {
            die("Publication introuvable");
        }

        $forumId = $pub['forum_id'];

        $this->model->delete($id);

        header("Location: /mindarena_forum/front/publications?forum_id=$forumId");
        exit;
    }
    
}
