
<?php

session_start();

define('BASE_URL', '/fixtrack/public');

// Root folder project
$root = dirname(__DIR__);

// CORE
require_once $root . '/app/core/Database.php';
require_once $root . '/app/core/Controller.php';
require_once $root . '/app/core/Router.php';

// MIDDLEWARE
require_once $root . '/app/middleware/AuthMiddleware.php';

// MODEL
require_once $root . '/app/models/User.php';
require_once $root . '/app/models/Category.php';
require_once $root . '/app/models/Facility.php';
require_once $root . '/app/models/Complaint.php';
require_once $root . '/app/models/ComplaintAssignment.php';

// CONTROLLER
require_once $root . '/app/controllers/AuthController.php';
require_once $root . '/app/controllers/AdminController.php';
require_once $root . '/app/controllers/ComplaintController.php';
require_once $root . '/app/controllers/AssignmentController.php';

// CSRF TOKEN
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ROUTES
require_once $root . '/routes/web.php';

// MENJALANKAN ROUTER
$router->dispatch();

?>
