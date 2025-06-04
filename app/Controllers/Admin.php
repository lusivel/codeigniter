<?php

namespace App\Controllers;

use App\Models\M_Admin;
use CodeIgniter\Controller;

class Admin extends BaseController // Gunakan BaseController jika Anda memilikinya, atau ganti menjadi Controller
{
    protected $M_Admin;

    public function __construct()
    {
        $this->M_Admin = new M_Admin();
        helper(['form', 'url', 'session']);
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->get('ses_id') && session()->get('ses_user') && session()->get('ses_level')) {
            return redirect()->to(base_url('admin/dashboard-admin'));
        }
        return view('Backend/Login/login');
    }

    public function dashboard()
    {
        // Pengecekan sesi. Sangat disarankan menggunakan Filter untuk DRY (Don't Repeat Yourself)
        if (session()->get('ses_id') == "" || session()->get('ses_user') == "" || session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        echo view('Backend/Template/Header');
        echo view('Backend/Template/Sidebar');
        echo view('Backend/Login/dashboard_admin');
        echo view('Backend/Template/Footer');
    }

    public function autentikasi()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validasi input menggunakan CodeIgniter Validation
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Username dan Password harus diisi!');
            return redirect()->back()->withInput();
        }

        // Cek username di database
        // getDataAdmin sekarang mengembalikan array, bukan objek query
        $foundUsers = $this->M_Admin->getDataAdmin(['username_admin' => $username, 'is_delete_admin' => '0']);

        if (empty($foundUsers)) { // Jika tidak ada pengguna ditemukan (array kosong)
            session()->setFlashdata('error', 'Username Tidak Ditemukan!');
            return redirect()->back()->withInput();
        } else {
            // Ambil data pengguna pertama dari array hasil
            // Karena username_admin adalah UNIQUE KEY, seharusnya hanya ada satu hasil
            $dataUser = $foundUsers[0];
            $passwordUser = $dataUser['password_admin'];

            // VERIFIKASI PASSWORD
            $verifikasiPassword = password_verify($password, $passwordUser);
            if (!$verifikasiPassword) {
                session()->setFlashdata('error', 'Password Tidak Sesuai!');
                return redirect()->back()->withInput();
            } else {
                // Set sesi jika login berhasil
                $dataSession = [
                    'ses_id'    => $dataUser['id_admin'],
                    'ses_user'  => $dataUser['nama_admin'],
                    'ses_level' => $dataUser['akses_level'],
                ];
                session()->set($dataSession);
                session()->setFlashdata('success', 'Login Berhasil!');
                return redirect()->to(base_url('admin/dashboard-admin'));
            }
        }
    }

    public function logout()
    {
        session()->remove('ses_id');
        session()->remove('ses_user');
        session()->remove('ses_level');
        session()->setFlashdata('info', 'Anda telah keluar dari sistem!');
        return redirect()->to(base_url('admin/login-admin'));
    }

    public function input_data_admin()
    {
        // Pengecekan sesi.
        if (session()->get('ses_id') == "" || session()->get('ses_user') == "" || session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }
        echo view('Backend/Template/Header');
        echo view('Backend/Template/Sidebar');
        echo view('Backend/MasterAdmin/input-data-admin');
        echo view('Backend/Template/Footer');
    }

    public function simpan_data_admin()
    {
        // Pengecekan sesi.
        if (session()->get('ses_id') == "" || session()->get('ses_user') == "" || session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Ambil data dari form, sesuaikan dengan nama 'name' di input-data-admin.php
        $nama_admin     = $this->request->getPost('nama_admin');
        $username_admin = $this->request->getPost('username_admin');
        $password_admin = $this->request->getPost('password_admin');
        $akses_level    = $this->request->getPost('akses_level');

        // Definisi aturan validasi: Sesuaikan nama field dengan nama input di form
        $rules = [
            'nama_admin'     => 'required',
            'username_admin' => 'required|is_unique[tbl_admin.username_admin]',
            'password_admin' => 'required|min_length[6]',
            'akses_level'    => 'required'
        ];

        // Jalankan validasi
        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Mohon periksa kembali isian Anda.');
            return redirect()->back()->withInput();
        }

        $datasimpan = [
            'nama_admin'       => $nama_admin,
            'username_admin'   => $username_admin,
            'password_admin'   => password_hash($password_admin, PASSWORD_DEFAULT),
            'akses_level'      => $akses_level,
            'is_delete_admin'  => '0',
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];
        
        $this->M_Admin->saveDataAdmin($datasimpan);
        session()->setFlashdata('success', 'Data Admin Berhasil Ditambahkan!!');
        return redirect()->to(base_url('admin/master-data-admin'));
    }

    public function master_data_admin()
    {
        // Pengecekan sesi.
        if (session()->get('ses_id') == "" || session()->get('ses_user') == "" || session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url("admin/login-admin"));
        }
        $uri        = service('uri');
        $pages      = $uri->getSegment(2);
        
        // MENGUBAH QUERY UNTUK MENAMPILKAN SEMUA ADMIN AKTIF
        $dataUser = $this->M_Admin->getDataAdmin(['is_delete_admin' => '0']); // Mengembalikan array

        $data['pages']     = $pages;
        $data['data_user'] = $dataUser;

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterAdmin/master-data-admin', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function edit_data_admin()
    {
        // Pengecekan sesi.
        if (session()->get('ses_id') == "" || session()->get('ses_user') == "" || session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }
        $uri    = service('uri');
        $idEdit = $uri->getSegment(3);

        // Perbaikan: getDataAdmin sekarang mengembalikan array
        $foundAdmins = $this->M_Admin->getDataAdmin(['id_admin' => $idEdit]);
        $dataAdmin = !empty($foundAdmins) ? $foundAdmins[0] : null;

        if (empty($dataAdmin)) {
            session()->setFlashdata('error', 'Data admin tidak ditemukan!');
            return redirect()->to(base_url('admin/master-data-admin'));
        }

        session()->set(['idUpdate' => $dataAdmin['id_admin']]);

        $page = $uri->getSegment(2);

        $data['page']       = $page;
        $data['web_title']  = "Edit Data Admin";
        $data['data_admin'] = $dataAdmin;

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterAdmin/edit-admin', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function update_data_admin()
    {
        // Pengecekan sesi.
        if (session()->get('ses_id') == "" || session()->get('ses_user') == "" || session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }
        $idUpdate = session()->get('idUpdate');
        $nama_admin  = $this->request->getPost('nama_admin');
        $akses_level = $this->request->getPost('akses_level');
        $password_baru = $this->request->getPost('password_admin'); // Ambil password baru dari form

        // Tambahkan validasi untuk update
        $rules = [
            'nama_admin'  => 'required',
            'akses_level' => 'required',
            'password_admin' => 'permit_empty|min_length[6]' // 'permit_empty' agar tidak wajib diisi saat update
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('error', 'Mohon periksa kembali isian Anda.');
            return redirect()->back()->withInput();
        }

        $dataUpdate = [
            'nama_admin'  => $nama_admin,
            'akses_level' => $akses_level,
            'updated_at'  => date("Y-m-d H:i:s"),
        ];

        // HASH PASSWORD HANYA JIKA DIISI DI FORM
        if (!empty($password_baru)) {
            $dataUpdate['password_admin'] = password_hash($password_baru, PASSWORD_DEFAULT);
        }

        $whereUpdate  = ['id_admin' => $idUpdate];

        $this->M_Admin->updateDataAdmin($dataUpdate, $whereUpdate);
        session()->remove('idUpdate');
        session()->setFlashdata('success', 'Data Admin Berhasil Diperbaharui!');
        return redirect()->to(base_url('admin/master-data-admin'));
    }

    public function hapus_data_admin()
    {
        // Pengecekan sesi.
        if (session()->get('ses_id') == "" || session()->get('ses_user') == "" || session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }
        $uri        = service('uri');
        $idHapus    = $uri->getSegment(3);

        $dataUpdate = [
            'is_delete_admin' => '1',
            'updated_at'      => date("Y-m-d H:i:s"),
        ];

        // Perbaikan: Hapus sha1() di sini karena id_admin harusnya sudah integer/string asli
        $whereUpdate = ['id_admin' => $idHapus];

        $this->M_Admin->updateDataAdmin($dataUpdate, $whereUpdate);
        session()->setFlashdata('success', 'Data Admin Berhasil Dihapus!');
        return redirect()->to(base_url('admin/master-data-admin'));
    }
}