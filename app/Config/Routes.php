<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Admin::login');


// routes untuk login admin 
$routes->get('/admin/login-admin', 'Admin::login');
$routes->get('/admin/dashboard-admin', 'Admin::dashboard');
$routes->post('/admin/autentikasi-login', 'Admin::autentikasi');
$routes->get('/admin/logout', 'Admin::logout');


//routes untuk module admin
$routes->get('/admin/master-data-admin', 'Admin::master_data_admin');
$routes->get('/admin/input-data-admin', 'Admin::input_data_admin');
$routes->post('/admin/simpan-data-admin', 'Admin::simpan_data_admin');
$routes->get('/admin/edit-data-admin/(:alphanum)', 'Admin::edit_data_admin/$1');
$routes->post('/admin/update-data-admin', 'Admin::update_data_admin');
$routes->post('/admin/hapus-data-admin/(:any)', 'Admin::hapus_data_admin/$1');

//routes untuk module anggota
$routes->get('/anggota/master-data-anggota', 'Anggota::master_data_anggota');
$routes->get('/anggota/input-data-anggota', 'Anggota::input_data_anggota');
$routes->post('/anggota/simpan-data-anggota', 'Anggota::simpan_data_anggota');
$routes->get('/anggota/edit-data-anggota/(:any)', 'Anggota::edit_data_anggota/$1');
$routes->post('/anggota/update-data-anggota', 'Anggota::update_data_anggota');
$routes->post('/anggota/hapus-data-anggota/(:any)', 'Anggota::hapus_data_anggota/$1');


//routes untuk module rak
$routes->get('admin/rak/master-data-rak', 'Rak::master_data_rak'); // Tambahkan 'admin/'
$routes->get('admin/rak/input-data-rak', 'Rak::input_data_rak');   // Tambahkan 'admin/'
$routes->post('admin/rak/simpan-data-rak', 'Rak::simpan_data_rak'); // Tambahkan 'admin/'
$routes->get('admin/rak/edit-data-rak/(:any)', 'Rak::edit_data_rak/$1'); // Tambahkan 'admin/'
$routes->post('admin/rak/update-data-rak', 'Rak::update_data_rak'); // Tambahkan 'admin/'
$routes->post('admin/rak/hapus-data-rak/(:any)', 'Rak::hapus_data_rak/$1'); // Tambahkan 'admin/' dan ubah GET ke POST jika form hapus pakai POST

// routes untuk module kategori
$routes->get('admin/kategori/master-data-kategori', 'Kategori::master_data_kategori'); // Tambahkan 'admin/'
$routes->get('admin/kategori/input-data-kategori', 'Kategori::input_data_kategori');   // Tambahkan 'admin/'
$routes->post('admin/kategori/simpan-data-kategori', 'Kategori::simpan_data_kategori'); // Tambahkan 'admin/'
$routes->get('admin/kategori/edit-data-kategori/(:any)', 'Kategori::edit_data_kategori/$1'); // Tambahkan 'admin/'
$routes->post('admin/kategori/update-data-kategori', 'Kategori::update_data_kategori'); // Tambahkan 'admin/'
$routes->post('admin/kategori/hapus-data-kategori/(:any)', 'Kategori::hapus_data_kategori/$1'); // Tambahkan 'admin/' dan ubah GET ke POST

// --- ROUTES BARU UNTUK MODULE BUKU ---
$routes->get('admin/buku/master-data-buku', 'Buku::master_data_buku'); // <-- Perhatikan baris ini
$routes->get('admin/buku/input-data-buku', 'Buku::input_data_buku');
$routes->post('admin/buku/simpan-data-buku', 'Buku::simpan_data_buku');
$routes->get('admin/buku/edit-data-buku/(:any)', 'Buku::edit_data_buku/$1');
$routes->post('admin/buku/update-data-buku', 'Buku::update_data_buku');
$routes->post('admin/buku/hapus-data-buku/(:any)', 'Buku::hapus_data_buku/$1');