<?php

namespace App\Controllers;

use App\Models\M_Kategori;

class Kategori extends BaseController
{
    public function input_data_kategori()
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
            echo view('Backend/MasterKategori/input-kategori');
            echo view('Backend/Template/Footer');
        }
    }

    public function simpan_data_kategori()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin'); ?>";
            </script>
            <?php
        } else {
            $modelKategori = new M_Kategori; // inisiasi

            $nama = $this->request->getPost('nama_kategori');

            $cekNama = $modelKategori->getDataKategori(['nama_kategori' => $nama])->getNumRows();
            if ($cekNama > 0) {
                session()->setFlashdata('error', 'Nama kategori sudah digunakan!!');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                $hasil = $modelKategori->autoNumber()->getRowArray();
                if (!$hasil) {
                    $id = "KTG001";
                } else {
                    $kode   = $hasil['id_kategori'];
                    $noUrut = (int)substr($kode, -3);
                    $noUrut++;
                    $id     = "KTG" . sprintf("%03s", $noUrut);
                }
                
                $datasimpan = [
                    'id_kategori'        => $id,
                    'nama_kategori'      => $nama,
                    'is_delete_kategori' => '0',
                    'created_at'         => date('Y-m-d H:i:s'),
                    'updated_at'         => date('Y-m-d H:i:s'),
                ];
                $modelKategori->saveDataKategori($datasimpan);
                session()->setFlashdata('success', 'Data Kategori Berhasil Ditambahkan!!');
                ?>
                <script>
                    document.location = "<?= base_url('kategori/master-data-kategori'); ?>";
                </script>
                <?php
            }
        }
    }

    public function master_data_kategori()
    {
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url("admin/login-admin"); ?>";
            </script>
            <?php
        } else {
            $modelKategori = new M_Kategori();  // inisiasi
            $uri           = service('uri');
            $pages         = $uri->getSegment(2);
            $dataKategori  = $modelKategori->getDataKategori(['is_delete_kategori' => '0'])->getResultArray();

            $data['pages']         = $pages;
            $data['data_kategori'] = $dataKategori;

            echo view('Backend/Template/Header', $data);
            echo view('Backend/Template/Sidebar', $data);
            echo view('Backend/MasterKategori/master-data-kategori', $data);
            echo view('Backend/Template/Footer', $data);
        }
    }
    
    public function edit_data_kategori()
    {
        $uri          = service('uri');
        $idEdit       = $uri->getSegment(3);
        $modelKategori = new M_Kategori();

        // Mengambil data kategori dari tabel kategori di database berdasarkan parameter yang dikirimkan
        $dataKategori = $modelKategori->getDataKategori(['sha1(id_kategori)' => $idEdit])->getRowArray();

        session()->set(['idUpdate' => $dataKategori['id_kategori']]);

        $page = $uri->getSegment(2);

        $data['page']         = $page;
        $data['web_title']    = "Edit Data Kategori";
        $data['data_kategori'] = $dataKategori; // mengirim array data kategori ke view

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterKategori/edit-kategori', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function update_data_kategori()
    {
        $modelKategori = new M_Kategori();

        $idUpdate = session()->get('idUpdate');
        $nama     = $this->request->getPost('nama_kategori');

        if ($nama == "") {
            session()->setFlashdata('error', 'Isian tidak boleh kosong!!');
            ?>
            <script>
                history.go(-1);
            </script>
            <?php
        } else {
            $dataUpdate = [
                'nama_kategori' => $nama,
                'updated_at'    => date("Y-m-d H:i:s"),
            ];
            $whereUpdate  = ['id_kategori' => $idUpdate];

            $modelKategori->updateDataKategori($dataUpdate, $whereUpdate);
            session()->remove('idUpdate');
            session()->setFlashdata('success', 'Data Kategori Berhasil Diperbaharui!');
            ?>
            <script>
                document.location = "<?= base_url('kategori/master-data-kategori'); ?>";
            </script>
            <?php
        }
    }
    
    public function hapus_data_kategori()
    {
        $modelKategori = new M_Kategori();
        $uri           = service('uri');
        $idHapus       = $uri->getSegment(3);

        $dataUpdate = [
            'is_delete_kategori' => '1',
            'updated_at'         => date("Y-m-d H:i:s"),
        ];

        $whereUpdate = ['sha1(id_kategori)' => $idHapus];

        $modelKategori->updateDataKategori($dataUpdate, $whereUpdate);
        session()->setFlashdata('success', 'Data Kategori Berhasil Dihapus!');
        ?>
        <script>
            document.location = "<?= base_url('kategori/master-data-kategori'); ?>";
        </script>
        <?php
    }
}