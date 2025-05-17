<?php

namespace App\Controllers;

use App\Models\M_Rak;

class Rak extends BaseController
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

    public function input_data_rak()
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
            echo view('Backend/MasterRak/input-rak');
            echo view('Backend/Template/Footer');
        }
    }

    public function simpan_data_rak()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin'); ?>";
            </script>
            <?php
        } else {
            $modelRak = new M_Rak; // inisiasi

            $nama = $this->request->getPost('nama_rak');

            $cekNama = $modelRak->getDataRak(['nama_rak' => $nama])->getNumRows();
            if ($cekNama > 0) {
                session()->setFlashdata('error', 'Nama rak sudah digunakan!!');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                $datasimpan = [
                    'nama_rak'       => $nama,
                    'is_delete_rak'  => '0',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ];
                $modelRak->saveDataRak($datasimpan);
                session()->setFlashdata('success', 'Data Rak Berhasil Ditambahkan!!');
                ?>
                <script>
                    document.location = "<?= base_url('rak/master-data-rak'); ?>";
                </script>
                <?php
            }
        }
    }

    public function master_data_rak()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url("admin/login-admin"); ?>";
            </script>
            <?php
        } else {
            $modelRak = new M_Rak();  // inisiasi
            $uri      = service('uri');
            $pages    = $uri->getSegment(2);
            $dataRak  = $modelRak->getDataRak(['is_delete_rak' => '0'])->getResultArray();

            $data['pages']    = $pages;
            $data['data_rak'] = $dataRak;

            echo view('Backend/Template/Header', $data);
            echo view('Backend/Template/Sidebar', $data);
            echo view('Backend/MasterRak/master-data-rak', $data);
            echo view('Backend/Template/Footer', $data);
        }
    }
    
    public function edit_data_rak()
    {
        $uri    = service('uri');
        $idEdit = $uri->getSegment(3);
        $modelRak = new M_Rak();

        // Mengambil data rak dari tabel rak di database berdasarkan parameter yang dikirimkan
        $dataRak = $modelRak->getDataRak(['sha1(id_rak)' => $idEdit])->getRowArray();

        session()->set(['idUpdate' => $dataRak['id_rak']]);

        $page = $uri->getSegment(2);

        $data['page']      = $page;
        $data['web_title'] = "Edit Data Rak";
        $data['data_rak']  = $dataRak; // mengirim array data rak ke view

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterRak/edit-rak', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function update_data_rak()
    {
        $modelRak = new M_Rak();

        $idUpdate = session()->get('idUpdate');
        $nama     = $this->request->getPost('nama_rak');

        if ($nama == "") {
            session()->setFlashdata('error', 'Isian tidak boleh kosong!!');
            ?>
            <script>
                history.go(-1);
            </script>
            <?php
        } else {
            $dataUpdate = [
                'nama_rak'    => $nama,
                'updated_at'  => date("Y-m-d H:i:s"),
            ];
            $whereUpdate  = ['id_rak' => $idUpdate];

            $modelRak->updateDataRak($dataUpdate, $whereUpdate);
            session()->remove('idUpdate');
            session()->setFlashdata('success', 'Data Rak Berhasil Diperbaharui!');
            ?>
            <script>
                document.location = "<?= base_url('rak/master-data-rak'); ?>";
            </script>
            <?php
        }
    }
    
    public function hapus_data_rak()
    {
        $modelRak = new M_Rak();
        $uri      = service('uri');
        $idHapus  = $uri->getSegment(3);

        $dataUpdate = [
            'is_delete_rak' => '1',
            'updated_at'    => date("Y-m-d H:i:s"),
        ];

        $whereUpdate = ['sha1(id_rak)' => $idHapus];

        $modelRak->updateDataRak($dataUpdate, $whereUpdate);
        session()->setFlashdata('success', 'Data Rak Berhasil Dihapus!');
        ?>
        <script>
            document.location = "<?= base_url('rak/master-data-rak'); ?>";
        </script>
        <?php
    }
}