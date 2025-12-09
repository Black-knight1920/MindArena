<?php
// Global bootstrap: defines common paths and autoloads core classes
define('BASE_PATH', dirname(__DIR__));
define('CONTROLLER_PATH', BASE_PATH . '/Controllers');
define('MODEL_PATH', BASE_PATH . '/Models');
define('SERVICE_PATH', BASE_PATH . '/Services');
define('CORE_PATH', BASE_PATH . '/config/Core');
define('VIEW_PATH', BASE_PATH . '/Views');
define('PUBLIC_PATH', VIEW_PATH . '/public');

// Lightweight autoloader for Controllers, Models, Services, Core classes
spl_autoload_register(function ($class) {
    $locations = [
        CONTROLLER_PATH . '/' . $class . '.php',
        MODEL_PATH . '/' . $class . '.php',
        SERVICE_PATH . '/' . $class . '.php',
        CORE_PATH . '/' . $class . '.php',
    ];

    foreach ($locations as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

// Shared config/constants
if (is_file(BASE_PATH . '/config/constants.php')) {
    require_once BASE_PATH . '/config/constants.php';
}

// Core database class (new) and legacy PDO connection ($conn)
require_once CORE_PATH . '/Database.php';

// Legacy PDO connection used by some legacy models (sets $conn global)
if (is_file(MODEL_PATH . '/database.php')) {
    require_once MODEL_PATH . '/database.php';
}
