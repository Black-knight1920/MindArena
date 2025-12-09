<?php

require_once __DIR__ . '/../Models/DashboardModel.php';

class DashboardController {

    public function index() {

        $model = new DashboardModel();

        // prepare data array
        $data = array(
    "totalUsers"     => $model->getTotalUsers(),
    "newUsers"       => $model->getNewUsers(),
    "activeSessions" => $model->getActiveSessions(),
    "recentUsers"    => $model->getRecentUsers()
);


require VIEW_PATH . '/backend/dashboard.php';
    }
}
?>

