<?php
require_once __DIR__ . '/../models/Forum.php';

class ForumController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Forum($pdo);
    }

    public function listFront() {
    $forums = $this->model->getAllFront();  // Always an array
    include __DIR__ . '/../views/front/forumList.php';
}


    public function addFront() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->model->create($_POST['title'], $_POST['description']);
        header("Location: /mindarena_forum/front/forums");
        exit;
    }

    include __DIR__ . '/../views/front/forumAdd.php';
}


    public function deleteFront($id) {
        $this->model->delete($id);
        header("Location: /mindarena_forum/front/forums");
        exit;
    }
}
