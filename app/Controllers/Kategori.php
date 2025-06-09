<?php

namespace App\Controllers;

use App\Models\M_Kategori;

class Kategori extends BaseController
{
    protected $mKategori;
    protected $session;
    protected $db; 

    public function __construct()
    {
        $this->mKategori = new M_Kategori();
        helper(['form', 'url', 'date']);
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect(); 
    }

    private function isLoggedIn() 
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }
        return true;
    }

    public function master_data_kategori()
    {
        if (($check = $this->isLoggedIn()) !== true) { 
            return $check;
        }

        $uri = service('uri');

        $dataKategori = $this->mKategori->getActiveKategori(); 

        $data['pages']       = $uri->getSegment(2) ?? 'master-data-kategori';
        $data['data_kategori'] = $dataKategori;
        $data['web_title']   = "Master Data Kategori";

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterKategori/master-data-kategori', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function input_data_kategori()
    {
        if (($check = $this->isLoggedIn()) !== true) { 
            return $check;
        }

        $data['web_title'] = "Input Data Kategori";

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterKategori/input-kategori', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function simpan_data_kategori()
    {
        if (($check = $this->isLoggedIn()) !== true) { 
            return $check;
        }

        // --- DEFINISI ATURAN VALIDASI ---
        $rules = [
            'nama_kategori' => [
                'label' => 'Nama Kategori',
                'rules' => 'required|min_length[3]|max_length[50]|is_unique[tbl_kategori.nama_kategori,is_delete_kategori,0]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.',
                    'max_length' => '{field} maksimal {param} karakter.',
                    'is_unique' => '{field} sudah digunakan.'
                ]
            ],
        ];

        if (!$this->validate($rules)) { // Menggunakan validasi CI4
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama_kategori = $this->request->getPost('nama_kategori');

        // --- LOGIKA AUTO NUMBER UNTUK id_kategori ---
        $hasilAutoNumber = $this->mKategori->autoNumber(); 
        $id_kategori = "KTG001";
        if ($hasilAutoNumber && !empty($hasilAutoNumber['id_kategori'])) {
            $kode   = $hasilAutoNumber['id_kategori'];
            $noUrut = (int)substr($kode, 3);
            $noUrut++;
            $id_kategori = "KTG" . sprintf("%03s", $noUrut);
        }

        $dataSimpan = [
            'id_kategori'        => $id_kategori,
            'nama_kategori'      => $nama_kategori,
            'is_delete_kategori' => 0, // Status aktif
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        try { 
            if ($this->mKategori->insert($dataSimpan)) { 
                $this->session->setFlashdata('success', 'Data Kategori Berhasil Ditambahkan!');
                return redirect()->to(base_url('admin/kategori/master-data-kategori'));
            } else {
                $error = $this->db->error(); 
                log_message('error', 'Gagal menyimpan kategori (insert returned false): ' . $error['message'] . ' - SQL: ' . $this->db->getLastQuery());
                $this->session->setFlashdata('error', 'Gagal menyimpan data kategori ke database. Terjadi masalah. Periksa log error.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) { 
            log_message('error', 'Gagal menyimpan kategori (exception): ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Gagal menyimpan data kategori ke database. Terjadi kesalahan sistem. Silakan coba lagi nanti.');
            return redirect()->back()->withInput();
        }
    }

    public function edit_data_kategori($id_kategori = null)
    {
        if (($check = $this->isLoggedIn()) !== true) { 
            return $check;
        }

        if (empty($id_kategori)) {
            $this->session->setFlashdata('error', 'ID Kategori tidak valid.');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        $dataKategori = $this->mKategori->withDeleted()->find($id_kategori); 

        if (!$dataKategori || ($dataKategori['is_delete_kategori'] ?? 0) == 1) { 
            $this->session->setFlashdata('error', 'Data kategori tidak ditemukan atau sudah dihapus.');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        $data['web_title'] = "Edit Data Kategori";
        $data['data_kategori'] = $dataKategori; 

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterKategori/edit-kategori', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function update_data_kategori()
    {
        if (($check = $this->isLoggedIn()) !== true) { 
            return $check;
        }

        $id_kategori_update = $this->request->getPost('id_kategori');

        if (empty($id_kategori_update)) {
            $this->session->setFlashdata('error', 'ID Kategori tidak valid untuk update.');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        $rules = [
            'nama_kategori' => [
                'label' => 'Nama Kategori',
                'rules' => "required|min_length[3]|max_length[50]|is_unique[tbl_kategori.nama_kategori,id_kategori,{$id_kategori_update},is_delete_kategori,0]", 
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.',
                    'max_length' => '{field} maksimal {param} karakter.',
                    'is_unique' => '{field} sudah digunakan oleh kategori lain.'
                ]
            ],
        ];

        if (!$this->validate($rules)) { // Menggunakan validasi CI4
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'updated_at'    => date("Y-m-d H:i:s"),
        ];

        try { 
            if ($this->mKategori->update($id_kategori_update, $dataUpdate)) { 
                $this->session->setFlashdata('success', 'Data Kategori Berhasil Diperbarui!');
                return redirect()->to(base_url('admin/kategori/master-data-kategori'));
            } else {
                $error = $this->db->error(); 
                log_message('error', 'Gagal memperbarui kategori (update returned false): ' . $error['message'] . ' - SQL: ' . $this->db->getLastQuery());
                $this->session->setFlashdata('error', 'Gagal memperbarui data kategori. Terjadi masalah. Periksa log error.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception | \Throwable $e) { // Tangkap juga Throwable untuk error yang lebih umum
            log_message('error', 'Gagal memperbarui kategori (exception): ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Terjadi kesalahan sistem. Silakan coba lagi nanti.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menghapus data kategori (soft delete).
     * URL: /admin/kategori/hapus-data-kategori/{id_kategori} (POST)
     */
    public function hapus_data_kategori($id_kategori = null)
    {
        if (($check = $this->isLoggedIn()) !== true) { 
            return $check;
        }

        if (empty($id_kategori)) {
            $this->session->setFlashdata('error', 'ID Kategori tidak valid untuk dihapus.');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        $kategori = $this->mKategori->withDeleted()->find($id_kategori); 

        if (!$kategori) {
            $this->session->setFlashdata('error', 'Data kategori tidak ditemukan!');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        if (($kategori['is_delete_kategori'] ?? 0) == 1) {
            $this->session->setFlashdata('error', 'Data kategori sudah dihapus!');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        try { 
            // MENGGUNAKAN METODE DELETE() BAWAAN MODEL UNTUK SOFT DELETE
            if ($this->mKategori->delete($id_kategori)) { 
                $this->session->setFlashdata('success', 'Data Kategori Berhasil Dihapus!');
                return redirect()->to(base_url('admin/kategori/master-data-kategori'));
            } else {
                $error = $this->db->error(); 
                log_message('error', 'Gagal menghapus kategori (delete returned false): ' . $error['message'] . ' - SQL: ' . $this->db->getLastQuery());
                $this->session->setFlashdata('error', 'Gagal menghapus data kategori. Terjadi masalah. Periksa log error aplikasi.');
                return redirect()->back();
            }
        } catch (\Exception | \Throwable $e) { // Tangkap juga Throwable untuk error yang lebih umum
            log_message('error', 'Gagal menghapus kategori (exception): ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Terjadi kesalahan sistem. Silakan coba lagi nanti.');
            return redirect()->back();
        }
    }
}