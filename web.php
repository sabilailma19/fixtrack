
<?php

// Inisialisasi Router
$router = new Router();

// Inisialisasi Controller
$auth = new AuthController();
$admin = new AdminController();
$assignmentController = new AssignmentController();



// =====================================
// HALAMAN LOGIN
// =====================================

$router->get('/', function () use ($auth) {
    $auth->login();
});

$router->get('/login', function () use ($auth) {
    $auth->login();
});


// =====================================
// PROSES LOGIN
// =====================================

$router->post('/login', function () use ($auth) {
    $auth->authenticate();
});


// =====================================
// LOGOUT
// =====================================

$router->get('/logout', function () use ($auth) {
    $auth->logout();
});


// =====================================
// DASHBOARD ADMIN
// =====================================

$router->get('/admin/dashboard', function () use ($admin) {

    AuthMiddleware::role(['admin']);

    $admin->dashboard();

});


// =====================================
// DATA PENGGUNA
// =====================================

$router->get('/admin/users', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->users();
});

$router->post('/admin/users/save', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->saveUser();
});

$router->post('/admin/users/toggle', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->toggleUser();
});


// =====================================
// KATEGORI KERUSAKAN
// =====================================

$router->get('/admin/categories', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->categories();
});

$router->post('/admin/categories/save', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->saveCategory();
});

$router->post('/admin/categories/toggle', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->toggleCategory();
});


// =====================================
// DATA FASILITAS
// =====================================

$router->get('/admin/facilities', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->facilities();
});

$router->post('/admin/facilities/save', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->saveFacility();
});

$router->post('/admin/facilities/toggle', function () use ($admin) {
    AuthMiddleware::role(['admin']);
    $admin->toggleFacility();
});



// =====================================
// DASHBOARD PETUGAS
// =====================================

$router->get('/petugas/dashboard', function () use ($assignmentController) {
    AuthMiddleware::role(['petugas']);
    $assignmentController->officerIndex();
});


// =====================================
// DASHBOARD PELAPOR
// =====================================

$router->get('/pelapor/dashboard', function () {

    AuthMiddleware::role(['pelapor']);

    $controller = new ComplaintController();
    $controller->reporterDashboard();

});

// =====================================
// COMPLAINT CONTROLLER
// =====================================

$complaintController = new ComplaintController();


// =====================================
// PENGADUAN ADMIN
// =====================================

$router->get('/admin/complaints', function () use ($complaintController) {

    AuthMiddleware::role(['admin']);

    $complaintController->adminIndex();

});

$router->post('/admin/complaints/verify', function () use ($complaintController) {

    AuthMiddleware::role(['admin']);

    $complaintController->verify();

});

$router->post('/admin/complaints/reject', function () use ($complaintController) {

    AuthMiddleware::role(['admin']);

    $complaintController->reject();

});


// =====================================
// PENGADUAN PELAPOR
// =====================================

$router->get('/pelapor/complaints', function () use ($complaintController) {

    AuthMiddleware::role(['pelapor']);

    $complaintController->reporterIndex();

});

$router->get('/pelapor/complaints/create', function () use ($complaintController) {

    AuthMiddleware::role(['pelapor']);

    $complaintController->create();

});

$router->post('/pelapor/complaints/store', function () use ($complaintController) {

    AuthMiddleware::role(['pelapor']);

    $complaintController->store();

});

// =====================================
// PENUGASAN PETUGAS ADMIN
// =====================================


$router->get('/admin/assignments', function () use ($assignmentController) {

    AuthMiddleware::role(['admin']);

    $assignmentController->adminIndex();

});

$router->post('/admin/assignments/store', function () use ($assignmentController) {

    AuthMiddleware::role(['admin']);

    $assignmentController->store();

});

// =====================================
// AKSI PENUGASAN PETUGAS
// =====================================

$router->post('/petugas/assignments/accept', function () use ($assignmentController) {

    AuthMiddleware::role(['petugas']);

    $assignmentController->accept();

});

$router->post('/petugas/assignments/start', function () use ($assignmentController) {

    AuthMiddleware::role(['petugas']);

    $assignmentController->start();

});

$router->post('/petugas/assignments/complete', function () use ($assignmentController) {

    AuthMiddleware::role(['petugas']);

    $assignmentController->complete();

});

$router->get('/petugas/assignments', function () use ($assignmentController) {

    AuthMiddleware::role(['petugas']);

    $assignmentController->officerAssignments();

});

$router->get('/admin/reports', function () use ($admin) {

    AuthMiddleware::role(['admin']);

    $admin->reports();

});

// =====================================
// REGISTRASI PELAPOR
// =====================================

require_once __DIR__ . '/../app/controllers/RegistrationController.php';

$registrationController = new RegistrationController();

$router->get('/register', function () use ($registrationController) {
    $registrationController->index();
});

$router->post('/register', function () use ($registrationController) {
    $registrationController->store();
});