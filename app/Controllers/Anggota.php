<?php

namespace App\Controllers;

use App\Models\M_Anggota;

class Anggota extends BaseController
{
    public function login()
    {
        return view('Backend/Login/login_anggota'); // sesuaikan view login anggota
    }

    public function dashboard()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('anggota/login-anggota'); ?>";
            </script>
            <?php
        } else {
            echo view('Backend/Template/Header');
            echo view('Backend/Template/Sidebar');
            echo view('Backend/Login/dashboard_anggota'); // sesuaikan view dashboard anggota
            echo view('Backend/Template/Footer');
        }
    }

    public function autentikasi()
    {
        $modelAnggota  = new M_Anggota(); // proses inisiasi model
        $email         = $this->request->getPost('email');
        $password      = $this->request->getPost('password');

        $cekEmail = $modelAnggota->getDataAnggota(['email' => $email, 'is_delete_anggota' => '0'])->getNumRows();
        if ($cekEmail == 0) {
            session()->setFlashdata('error', 'Email Tidak Ditemukan!');
            ?>
            <script>
                history.go(-1);
            </script>
            <?php
        } else {
            $dataUser      = $modelAnggota->getDataAnggota(['email' => $email, 'is_delete_anggota' => '0'])->getRowArray();
            $passwordUser  = $dataUser['password_anggota'];

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
                    'ses_id'    => $dataUser['id_anggota'],
                    'ses_user'  => $dataUser['nama_anggota'],
                    'ses_level' => 'anggota', // atau jika ada level khusus di anggota, sesuaikan
                ];
                session()->set($dataSession);
                session()->setFlashdata('success', 'Login Berhasil!');
                ?>
                <script>
                    document.location = "<?= base_url('anggota/dashboard-anggota'); ?>";
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
            document.location = "<?= base_url('anggota/login-anggota'); ?>";
        </script>
        <?php
    }

    public function input_data_anggota()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('anggota/login-anggota'); ?>";
            </script>
            <?php
        } else {
            echo view('Backend/Template/Header');
            echo view('Backend/Template/Sidebar');
            echo view('Backend/MasterAnggota/input-anggota'); // buat view ini
            echo view('Backend/Template/Footer');
        }
    }

    public function simpan_data_anggota()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('anggota/login-anggota'); ?>";
            </script>
            <?php
        } else {
            $modelAnggota = new M_Anggota(); // inisiasi model

            $nama          = $this->request->getPost('nama_anggota');
            $jenis_kelamin = $this->request->getPost('jenis_kelamin');
            $no_tlp        = $this->request->getPost('no_tlp');
            $alamat        = $this->request->getPost('alamat');
            $email         = $this->request->getPost('email');

            $cekEmail = $modelAnggota->getDataAnggota(['email' => $email])->getNumRows();
            if ($cekEmail > 0) {
                session()->setFlashdata('error', 'Email sudah digunakan!!');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                // Generate ID anggota otomatis mirip admin, sesuaikan prefix jika perlu
                $hasil = $modelAnggota->autoNumber()->getRowArray();
                if (!$hasil) {
                    $id = "ANG001";
                } else {
                    $kode   = $hasil['id_anggota'];
                    $noUrut = (int)substr($kode, -3);
                    $noUrut++;
                    $id     = "ANG" . sprintf("%03s", $noUrut);
                }
            }

            $datasimpan = [
                'id_anggota'        => $id,
                'nama_anggota'      => $nama,
                'jenis_kelamin'     => $jenis_kelamin,
                'no_tlp'            => $no_tlp,
                'alamat'            => $alamat,
                'email'             => $email,
                'password_anggota'  => password_hash('pass_anggota', PASSWORD_DEFAULT),
                'is_delete_anggota' => '0',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ];
            $modelAnggota->saveDataAnggota($datasimpan);
            session()->setFlashdata('success', 'Data Anggota Berhasil Ditambahkan!!');
            ?>
            <script>
                document.location = "<?= base_url('anggota/master-data-anggota'); ?>";
            </script>
            <?php
        }
    }

    public function master_data_anggota()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('anggota/login-anggota'); ?>";
            </script>
            <?php
        } else {
            $modelAnggota = new M_Anggota();
            $uri          = service('uri');
            $pages        = $uri->getSegment(2);
            $dataUser     = $modelAnggota->getDataAnggota(['is_delete_anggota' => '0'])->getResultArray();

            $data['pages']     = $pages;
            $data['data_user'] = $dataUser;

            echo view('Backend/Template/Header', $data);
            echo view('Backend/Template/Sidebar', $data);
            echo view('Backend/MasterAnggota/master-data-anggota', $data);
            echo view('Backend/Template/Footer', $data);
        }
    }

    public function edit_data_anggota()
    {
        $uri       = service('uri');
        $idEdit    = $uri->getSegment(3);
        $modelAnggota = new M_Anggota();

        // Mengambil data anggota dari tabel anggota di database berdasarkan parameter yang dikirimkan
        $dataAnggota = $modelAnggota->getDataAnggota(['sha1(id_anggota)' => $idEdit])->getRowArray();

        session()->set(['idUpdate' => $dataAnggota['id_anggota']]);

        $page = $uri->getSegment(2);

        $data['page']        = $page;
        $data['web_title']   = "Edit Data Anggota";
        $data['data_anggota'] = $dataAnggota;

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterAnggota/edit-anggota', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function update_data_anggota()
    {
        $modelAnggota = new M_Anggota();

        $idUpdate = session()->get('idUpdate');
        $nama     = $this->request->getPost('nama_anggota');
        $jenis_kelamin = $this->request->getPost('jenis_kelamin');
        $no_tlp   = $this->request->getPost('no_tlp');
        $alamat   = $this->request->getPost('alamat');
        $email    = $this->request->getPost('email');

        if ($nama == "" or $jenis_kelamin == "" or $no_tlp == "" or $alamat == "" or $email == "") {
            session()->setFlashdata('error', 'Isian tidak boleh kosong!!');
            ?>
            <script>
                history.go(-1);
            </script>
            <?php
        } else {
            $dataUpdate = [
                'nama_anggota'  => $nama,
                'jenis_kelamin' => $jenis_kelamin,
                'no_tlp'        => $no_tlp,
                'alamat'        => $alamat,
                'email'         => $email,
                'updated_at'    => date("Y-m-d H:i:s"),
            ];
            $whereUpdate  = ['id_anggota' => $idUpdate];

            $modelAnggota->updateDataAnggota($dataUpdate, $whereUpdate);
            session()->remove('idUpdate');
            session()->setFlashdata('success', 'Data Anggota Berhasil Diperbaharui!');
            ?>
            <script>
                document.location = "<?= base_url('anggota/master-data-anggota'); ?>";
            </script>
            <?php
        }
    }

    public function hapus_data_anggota()
    {
        $modelAnggota = new M_Anggota();
        $uri          = service('uri');
        $idHapus      = $uri->getSegment(3);

        $dataUpdate = [
            'is_delete_anggota' => '1',
            'updated_at'        => date("Y-m-d H:i:s"),
        ];

        $whereUpdate = ['sha1(id_anggota)' => $idHapus];

        $modelAnggota->updateDataAnggota($dataUpdate, $whereUpdate);
        session()->setFlashdata('success', 'Data Anggota Berhasil Dihapus!');
        ?>
        <script>
            document.location = "<?= base_url('anggota/master-data-anggota'); ?>";
        </script>
        <?php
    }
}
