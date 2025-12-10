<?php
// Temporary debug helper — enable errors and include the app entry
// Remove this file when debugging is complete.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Try to include the admin page to reproduce the 500 error and show the trace.
// Use a full path to avoid relative include issues.
require_once __DIR__ . '/admin_index.php';

?>