<?php

$upgrade = new FixTrackUpgradeController();

// Daftar pengaduan admin.
$router->get('/admin/complaints', function () use ($upgrade) {
    $upgrade->listing('admin-complaints');
});

// Penugasan dan riwayat admin.
$router->get('/admin/assignments', function () use ($upgrade) {
    $upgrade->listing('admin-assignments');
});

// Riwayat pelapor.
$router->get('/pelapor/complaints', function () use ($upgrade) {
    $upgrade->listing('reporter');
});

// Penugasan petugas.
$router->get('/petugas/assignments', function () use ($upgrade) {
    $upgrade->listing('officer');
});

// Laporan, filter, pagination, dan cetak.
$router->get('/admin/reports', function () use ($upgrade) {
    $upgrade->listing('reports');
});

// Ekspor menggunakan filter yang sama.
$router->get('/admin/reports/export', function () use ($upgrade) {
    $upgrade->export();
});

// Detail dengan pemeriksaan hak akses.
$router->get('/complaints/detail', function () use ($upgrade) {
    $upgrade->detail();
});

// Verifikasi dan penolakan.
$router->post('/admin/complaints/verify', function () use ($upgrade) {
    $upgrade->action('verify');
});

$router->post('/admin/complaints/reject', function () use ($upgrade) {
    $upgrade->action('reject');
});

// Penugasan atomik.
$router->post('/admin/assignments/store', function () use ($upgrade) {
    $upgrade->action('assign');
});

// Progres petugas.
$router->post('/petugas/assignments/accept', function () use ($upgrade) {
    $upgrade->action('accept');
});

$router->post('/petugas/assignments/start', function () use ($upgrade) {
    $upgrade->action('start');
});

$router->post('/petugas/assignments/complete', function () use ($upgrade) {
    $upgrade->action('complete');
});

// Konfirmasi atau permintaan perbaikan ulang.
$router->post('/pelapor/complaints/feedback', function () use ($upgrade) {
    $upgrade->action('feedback');
});