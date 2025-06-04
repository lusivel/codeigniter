<?php

namespace App\Controllers;

use App\Models\M_Rak;
use CodeIgniter\Database\ConnectionInterface; // Tambahkan ini untuk type hinting jika menggunakan PHP 7.4+

class Rak extends BaseController
{
    protected $mRak;
    protected $session;
    protected $db; // Deklarasi properti $db

    public function __construct()
    {
        $this->mRak = new M_Rak();
        helper(['form', 'url', 'date']);
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect(); // Inisialisasi koneksi database
    }

    /**
     * Menampilkan form input data rak baru.
     * URL: /admin/rak/input-data-rak
     */
    public function input_data_rak()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $data['web_title'] = "Input Data Rak";
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterRak/input-rak', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Menyimpan data rak baru.
     * URL: /admin/rak/simpan-data-rak (POST)
     */
    public function simpan_data_rak()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // --- DEFINISI ATURAN VALIDASI ---
        $rules = [
            'nama_rak' => [
                'label' => 'Nama Rak',
                'rules' => 'required|min_length[3]|max_length[50]|is_unique[tbl_rak.nama_rak,is_delete_rak,0]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.',
                    'max_length' => '{field} maksimal {param} karakter.',
                    'is_unique' => '{field} sudah digunakan.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama_rak = $this->request->getPost('nama_rak');

        // --- LOGIKA AUTO NUMBER UNTUK id_rak ---
        $hasilAutoNumber = $this->mRak->autoNumber()->getRowArray();
        $id_rak = "RAK001";
        if ($hasilAutoNumber && !empty($hasilAutoNumber['id_rak'])) {
            $kode   = $hasilAutoNumber['id_rak'];
            $noUrut = (int)substr($kode, 3);
            $noUrut++;
            $id_rak = "RAK" . sprintf("%03s", $noUrut);
        }

        // Menyiapkan data untuk disimpan
        $dataSimpan = [
            'id_rak'        => $id_rak,
            'nama_rak'      => $nama_rak,
            'is_delete_rak' => 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        if ($this->mRak->insert($dataSimpan)) {
            $this->session->setFlashdata('success', 'Data Rak Berhasil Ditambahkan!');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        } else {
            $this->session->setFlashdata('error', 'Gagal menyimpan data rak ke database. Periksa log error.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menampilkan master data rak.
     * URL: /admin/rak/master-data-rak
     */
    public function master_data_rak()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }
        $uri = service('uri');
        
        $dataRak = $this->mRak->getActiveRak();

        $data['pages']     = $uri->getSegment(2) ?? 'master-data-rak';
        $data['data_rak']  = $dataRak;
        $data['web_title'] = "Master Data Rak";

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterRak/master-data-rak', $data);
        echo view('Backend/Template/Footer', $data);
    }
    
    /**
     * Menampilkan form edit data rak.
     * URL: /admin/rak/edit-data-rak/{id_rak} (GET)
     */
    public function edit_data_rak($id_rak = null)
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        if (empty($id_rak)) {
            $this->session->setFlashdata('error', 'ID Rak tidak valid.');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        }

        $dataRak = $this->mRak->withDeleted()->find($id_rak);

        if (!$dataRak || ($dataRak['is_delete_rak'] ?? 0) == 1) {
            $this->session->setFlashdata('error', 'Data rak tidak ditemukan atau sudah dihapus.');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        }
        
        $this->session->set(['idUpdateRak' => $dataRak['id_rak']]); 

        $data['page']         = 'edit-data-rak';
        $data['web_title']    = "Edit Data Rak";
        $data['data_rak']     = $dataRak;

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterRak/edit-rak', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memperbarui data rak.
     * URL: /admin/rak/update-data-rak (POST)
     */
    public function update_data_rak()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $idUpdate = $this->session->get('idUpdateRak');

        if (empty($idUpdate)) {
            $this->session->setFlashdata('error', 'ID Rak tidak valid untuk update.');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        }
        
        $nama_rak = $this->request->getPost('nama_rak');

        // --- DEFINISI ATURAN VALIDASI UNTUK UPDATE ---
        $rules = [
            'nama_rak' => [
                'label' => 'Nama Rak',
                'rules' => "required|min_length[3]|max_length[50]|is_unique[tbl_rak.nama_rak,id_rak,{$idUpdate},is_delete_rak,0]",
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.',
                    'max_length' => '{field} maksimal {param} karakter.',
                    'is_unique' => '{field} sudah digunakan oleh rak lain.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'nama_rak'   => $nama_rak,
            'updated_at' => date("Y-m-d H:i:s"),
        ];
        
        if ($this->mRak->update($idUpdate, $dataUpdate)) {
            $this->session->setFlashdata('success', 'Data Rak Berhasil Diperbarui!');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        } else {
            $this->session->setFlashdata('error', 'Gagal memperbarui data rak. Periksa log error.');
            return redirect()->back()->withInput();
        }
    }
    
    /**
     * Menghapus data rak (soft delete).
     * URL: /admin/rak/hapus-data-rak/{id_rak} (POST)
     */
    public function hapus_data_rak($id_rak = null)
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        if (empty($id_rak)) {
            $this->session->setFlashdata('error', 'ID Rak tidak valid untuk dihapus.');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        }

        $rak = $this->mRak->withDeleted()->find($id_rak);

        if (!$rak) {
            $this->session->setFlashdata('error', 'Data rak tidak ditemukan!');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        }
        
        if (($rak['is_delete_rak'] ?? 0) == 1) {
            $this->session->setFlashdata('error', 'Data rak sudah dihapus!');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        }

        // --- Perbaikan: Eksekusi query UPDATE secara langsung ---
        // Jika delete() bawaan Model masih bermasalah, kita akan memaksa update melalui DB Connection.
        $dataToUpdate = [
            'is_delete_rak' => 1,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        // Memanggil Query Builder langsung dari DB Connection untuk memastikan update
        // Ini adalah cara eksplisit untuk melakukan update tanpa melewati metode delete() dari Model
        if ($this->db->table($this->mRak->table)->where($this->mRak->primaryKey, $id_rak)->update($dataToUpdate)) {
            $this->session->setFlashdata('success', 'Data Rak Berhasil Dihapus!');
            return redirect()->to(base_url('admin/rak/master-data-rak'));
        } else {
            // Dapatkan detail error dari database jika query gagal
            $error = $this->db->error();
            log_message('error', 'Gagal menghapus rak: ' . $error['message'] . ' - SQL: ' . $this->db->getLastQuery());
            $this->session->setFlashdata('error', 'Gagal menghapus data rak. Periksa log error aplikasi.');
            return redirect()->back();
        }
    }
}