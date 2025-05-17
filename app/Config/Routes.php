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
$routes->post('/admin/update-admin', 'Admin::update_data_admin');
$routes->get('/admin/hapus-data-admin/(:alphanum)', 'Admin::hapus_data_admin/$1');

//routes untuk module anggota
$routes->get('/anggota/master-data-anggota', 'Anggota::master_data_anggota');
$routes->get('/anggota/input-data-anggota', 'Anggota::input_data_anggota');
$routes->post('/anggota/simpan-data-anggota', 'Anggota::simpan_data_anggota');
$routes->get('/anggota/edit-data-anggota/(:alphanum)', 'Anggota::edit_data_anggota/$1');
$routes->post('/anggota/update-anggota', 'Anggota::update_data_anggota'); 
$routes->get('/anggota/hapus-data-anggota/(:alphanum)', 'Anggota::hapus_data_anggota/$1');

//routes untuk module rak
$routes->get('rak/master-data-rak', 'Rak::master_data_rak');
$routes->get('rak/input-data-rak', 'Rak::input_data_rak');
$routes->post('rak/simpan-data-rak', 'Rak::simpan_data_rak');
$routes->get('rak/edit-data-rak/(:any)', 'Rak::edit_data_rak/$1');
$routes->post('rak/update-data-rak', 'Rak::update_data_rak');
$routes->get('rak/hapus-data-rak/(:any)', 'Rak::hapus_data_rak/$1');

//routes untuk module kategori
$routes->get('kategori/master-data-kategori', 'Kategori::master_data_kategori');
$routes->get('kategori/input-data-kategori', 'Kategori::input_data_kategori');
$routes->post('kategori/simpan-data-kategori', 'Kategori::simpan_data_kategori');
$routes->get('kategori/edit-data-kategori/(:any)', 'Kategori::edit_data_kategori/$1');
$routes->post('kategori/update-data-kategori', 'Kategori::update_data_kategori');
$routes->get('kategori/hapus-data-kategori/(:any)', 'Kategori::hapus_data_kategori/$1');
