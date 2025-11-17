
<?php
require_once 'config/Database.php';
require_once 'controllers/ForumController.php';
require_once 'controllers/PublicationController.php';

$db = new Database();
$pdo = $db->getConnection();

$forum = new ForumController($pdo);
$pub = new PublicationController($pdo);

$uri = strtok($_SERVER['REQUEST_URI'], '?');

switch ($uri) {

    case '/mindarena_forum':
    case '/mindarena_forum/':
    case '/mindarena_forum/front':
    case '/mindarena_forum/front/':
        $forum->listFront();
        break;

    case '/mindarena_forum/front/forums':
        $forum->listFront();
        break;

    case '/mindarena_forum/front/add-forum':
        $forum->addFront();
        break;

    case '/mindarena_forum/front/edit-forum':
        $forum->editFront($_GET['id']);
        break;

    case '/mindarena_forum/front/delete-forum':
        $forum->deleteFront($_GET['id']);
        break;

    case '/mindarena_forum/front/publications':
        $pub->listFront();
        break;

    case '/mindarena_forum/front/add-publication':
        $pub->addFront();
        break;

    case '/mindarena_forum/front/delete-publication':
        $pub->deleteFront($_GET['id']);
        break;

    default:
        echo "<h1 style='color:red;text-align:center'>404 - Page non trouvée</h1>";
}
