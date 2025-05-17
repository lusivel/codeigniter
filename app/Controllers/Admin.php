<?php

namespace App\Controllers;

use App\Models\M_Admin;

class Admin extends BaseController
{
    public function login()
    {
        return view('Backend/Login/login');
    }

    public function dashboard()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin'); ?>";
            </script>
            <?php
        } else {
            echo view('Backend/Template/Header');
            echo view('Backend/Template/Sidebar');
            echo view('Backend/Login/dashboard_admin');
            echo view('Backend/Template/Footer');
        }
    }


    public function autentikasi()
    {
        $modelAdmin  = new M_Admin(); // proses inisiasi model
        $username    = $this->request->getPost('username');
        $password    = $this->request->getPost('password');

        $cekUsername = $modelAdmin->getDataAdmin(['username_admin' => $username, 'is_delete_admin' => '0'])->getNumRows();
        if ($cekUsername == 0) {
            session()->setFlashdata('error', 'Username Tidak Ditemukan!');
            ?>
            <script>
                history.go(-1);
            </script>
            <?php
        } else {
            $dataUser      = $modelAdmin->getDataAdmin(['username_admin' => $username, 'is_delete_admin' => '0'])->getRowArray();
            $passwordUser  = $dataUser['password_admin'];

            $verifikasiPassword = password_verify($password, $passwordUser);
            if (!$verifikasiPassword) {
                session()->setFlashdata('error', 'Password Tidak Sesuai!');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                $dataSession = [
                    'ses_id'    => $dataUser['id_admin'],
                    'ses_user'  => $dataUser['nama_admin'],
                    'ses_level' => $dataUser['akses_level'],
                ];
                session()->set($dataSession);
                session()->setFlashdata('success', 'Login Berhasil!');
                ?>
                <script>
                    document.location = "<?= base_url('admin/dashboard-admin'); ?>";
                </script>
                <?php
            }
        }
    }
    public function logout()
    {
        session()->remove('ses_id');
        session()->remove('ses_user');
        session()->remove('ses_level');
        session()->setFlashdata('info', 'Anda telah keluar dari sistem!');
        ?>
        <script>
            document.location = "<?= base_url('admin/login-admin'); ?>";
        </script>
        <?php
    }
    public function input_data_admin()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin'); ?>";
            </script>
            <?php
        } else {
            echo view('Backend/Template/Header');
            echo view('Backend/Template/Sidebar');
            echo view('Backend/MasterAdmin/input-data-admin');
            echo view('Backend/Template/Footer');
        }
    }

    public function simpan_data_admin()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin'); ?>";
            </script>
            <?php
        } else {
            $modelAdmin = new M_Admin; // inisiasi

            $nama       = $this->request->getPost('nama');
            $username   = $this->request->getPost('username');
            $level      = $this->request->getPost('level');

            $cekUsername = $modelAdmin->getDataAdmin(['username_admin' => $username])->getNumRows();
            if ($cekUsername > 0) {
                session()->setFlashdata('error', 'Username sudah digunakan!!');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                $hasil = $modelAdmin->autoNumber()->getRowArray();
                if (!$hasil) {
                    $id = "ADM001";
                } else {
                    $kode   = $hasil['id_admin'];
                    $noUrut = (int)substr($kode, -3);
                    $noUrut++;
                    $id     = "ADM" . sprintf("%03s", $noUrut);
                }
            }

            $datasimpan = [
                'id_admin'         => $id,
                'nama_admin'       => $nama,
                'username_admin' => $username,
                'password_admin' => password_hash('pass_admin', PASSWORD_DEFAULT),
                'akses_level'      => $level,
                'is_delete_admin'  => '0',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ];
            $modelAdmin->saveDataAdmin($datasimpan);
            session()->setFlashdata('success', 'Data Admin Berhasil Ditambahkan!!');
            ?>
            <script>
                document.location = "<?= base_url('admin/master-data-admin'); ?>";
            </script>
            <?php
        }
    }

    public function master_data_admin()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url("admin/login-admin"); ?>";
            </script>
            <?php
        } else {
            $modelAdmin = new M_Admin();  // inisiasi
            $uri        = service('uri');
            $pages      = $uri->getSegment(2);
            $dataUser   = $modelAdmin->getDataAdmin(['is_delete_admin' => '0', 'akses_level !=' => '1'])->getResultArray();

            $data['pages']     = $pages;
            $data['data_user'] = $dataUser;

            echo view('Backend/Template/Header', $data);
            echo view('Backend/Template/Sidebar', $data);
            echo view('Backend/MasterAdmin/master-data-admin', $data);
            echo view('Backend/Template/Footer', $data);
        }
    }
    public function edit_data_admin()
        {
            $uri    = service('uri');
            $idEdit = $uri->getSegment(3);
            $modelAdmin = new M_Admin();

            // Mengambil data admin dari tabel admin di database berdasarkan parameter yang dikirimkan
            $dataAdmin = $modelAdmin->getDataAdmin(['sha1(id_admin)' => $idEdit])->getRowArray();

            session()->set(['idUpdate' => $dataAdmin['id_admin']]);

            $page = $uri->getSegment(2);

            $data['page']       = $page;
            $data['web_title']  = "Edit Data Admin";
            $data['data_admin'] = $dataAdmin; // mengirim array data admin ke view

            echo view('Backend/Template/Header', $data);
            echo view('Backend/Template/Sidebar', $data);
            echo view('Backend/MasterAdmin/edit-admin', $data);
            echo view('Backend/Template/Footer', $data);
    }

    public function update_data_admin()
        {
            $modelAdmin = new M_Admin();

            $idUpdate = session()->get('idUpdate');
            $nama     = $this->request->getPost('nama');
            $level    = $this->request->getPost('level');

            if ($nama == "" or $level == "") {
                session()->setFlashdata('error', 'Isian tidak boleh kosong!!');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                $dataUpdate = [
                    'nama_admin'  => $nama,
                    'akses_level' => $level,
                    'updated_at'  => date("Y-m-d H:i:s"),
                ];
                $whereUpdate  = ['id_admin' => $idUpdate];

                $modelAdmin->updateDataAdmin($dataUpdate, $whereUpdate);
                session()->remove('idUpdate');
                session()->setFlashdata('success', 'Data Admin Berhasil Diperbaharui!');
                ?>
                <script>
                    document.location = "<?= base_url('admin/master-data-admin'); ?>";
                </script>
                <?php
            }
        }
    public function hapus_data_admin()
        {
            $modelAdmin = new M_Admin();
            $uri        = service('uri');
            $idHapus    = $uri->getSegment(3);

            $dataUpdate = [
                'is_delete_admin' => '1',
                'updated_at'      => date("Y-m-d H:i:s"),
            ];

            $whereUpdate = ['sha1(id_admin)' => $idHapus];

            $modelAdmin->updateDataAdmin($dataUpdate, $whereUpdate);
            session()->setFlashdata('success', 'Data Admin Berhasil Dihapus!');
            ?>
            <script>
                document.location = "<?= base_url('admin/master-data-admin'); ?>";
            </script>
            <?php
        }
// module admin selesai
}
