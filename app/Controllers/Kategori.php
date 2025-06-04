<?php

namespace App\Controllers;

use App\Models\M_Kategori;
use CodeIgniter\Database\ConnectionInterface; // Tambahkan ini jika menggunakan PHP 7.4+

class Kategori extends BaseController
{
    protected $mKategori;
    protected $session;
    protected $db; // Tambahkan properti $db

    public function __construct()
    {
        $this->mKategori = new M_Kategori();
        helper(['form', 'url', 'date']);
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect(); // Inisialisasi koneksi database
    }

    /**
     * Menampilkan master data kategori.
     * URL: /admin/kategori/master-data-kategori
     */
    public function master_data_kategori()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) { // KOREKSI SESI
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $data['web_title'] = "Master Data Kategori";
        // Mengambil semua data kategori yang aktif (is_delete_kategori = '0')
        $data['data_kategori'] = $this->mKategori->getActiveKategori(); // KOREKSI: Gunakan getActiveKategori()
        
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterKategori/master-data-kategori', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Menampilkan form input data kategori baru.
     * URL: /admin/kategori/input-data-kategori
     */
    public function input_data_kategori()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) { // KOREKSI SESI
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $data['web_title'] = "Input Data Kategori";
        
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterKategori/input-kategori', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Menyimpan data kategori baru.
     * URL: /admin/kategori/simpan-data-kategori (POST)
     */
    public function simpan_data_kategori()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) { // KOREKSI SESI
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
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

        // --- JALANKAN VALIDASI ---
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mengambil data dari POST request
        $nama_kategori = $this->request->getPost('nama_kategori');

        // --- LOGIKA AUTO NUMBER UNTUK id_kategori ---
        $hasilAutoNumber = $this->mKategori->autoNumber()->getRowArray();
        $id_kategori = "KTG001"; // Default jika tabel kosong
        if ($hasilAutoNumber && !empty($hasilAutoNumber['id_kategori'])) {
            $kode   = $hasilAutoNumber['id_kategori'];
            $noUrut = (int)substr($kode, 3);
            $noUrut++;
            $id_kategori = "KTG" . sprintf("%03s", $noUrut);
        }
        // --- AKHIR LOGIKA AUTO NUMBER ---

        // Menyiapkan data untuk disimpan
        $dataSimpan = [
            'id_kategori'        => $id_kategori,
            'nama_kategori'      => $nama_kategori,
            'is_delete_kategori' => 0, // Status aktif (integer 0 untuk TINYINT(1))
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        // Memanggil method insert() bawaan Model
        if ($this->mKategori->insert($dataSimpan)) { // KOREKSI: Gunakan insert()
            $this->session->setFlashdata('success', 'Data Kategori Berhasil Ditambahkan!');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        } else {
            $this->session->setFlashdata('error', 'Gagal menyimpan data kategori ke database. Periksa log error.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menampilkan form edit data kategori.
     * URL: /admin/kategori/edit-data-kategori/{id_kategori} (GET)
     */
    public function edit_data_kategori($id_kategori = null)
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) { // KOREKSI SESI
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Validasi ID kategori dari URL
        if (empty($id_kategori)) {
            $this->session->setFlashdata('error', 'ID Kategori tidak valid.');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        // Mengambil data kategori berdasarkan ID (find() sudah menghormati soft delete)
        $dataKategori = $this->mKategori->withDeleted()->find($id_kategori); // Gunakan withDeleted()

        // Memeriksa apakah data kategori ditemukan dan tidak di-soft delete
        if (!$dataKategori || ($dataKategori['is_delete_kategori'] ?? 0) == 1) { // KOREKSI: Cek status is_delete_kategori
            $this->session->setFlashdata('error', 'Data kategori tidak ditemukan atau sudah dihapus.');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        $data['web_title'] = "Edit Data Kategori";
        $data['data_kategori'] = $dataKategori; // Data kategori yang akan diedit

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterKategori/edit-kategori', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memperbarui data kategori.
     * URL: /admin/kategori/update-data-kategori (POST)
     */
    public function update_data_kategori()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) { // KOREKSI SESI
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Mengambil ID kategori dari hidden input form
        $id_kategori_update = $this->request->getPost('id_kategori');

        // Validasi ID kategori
        if (empty($id_kategori_update)) {
            $this->session->setFlashdata('error', 'ID Kategori tidak valid untuk update.');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }
        
        // --- DEFINISI ATURAN VALIDASI UNTUK UPDATE ---
        $rules = [
            'nama_kategori' => [
                'label' => 'Nama Kategori',
                // is_unique untuk update: cek unik kecuali untuk ID yang sedang diupdate
                'rules' => "required|min_length[3]|max_length[50]|is_unique[tbl_kategori.nama_kategori,id_kategori,{$id_kategori_update},is_delete_kategori,0]", // KOREKSI is_unique dengan filter soft delete
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.',
                    'max_length' => '{field} maksimal {param} karakter.',
                    'is_unique' => '{field} sudah digunakan oleh kategori lain.'
                ]
            ],
        ];

        // --- JALANKAN VALIDASI ---
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mengambil data dari POST request
        $dataUpdate = [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'updated_at'    => date("Y-m-d H:i:s"),
        ];
        
        // Memanggil method update() bawaan Model
        if ($this->mKategori->update($id_kategori_update, $dataUpdate)) { // KOREKSI: Gunakan update()
            $this->session->setFlashdata('success', 'Data Kategori Berhasil Diperbarui!');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        } else {
            $this->session->setFlashdata('error', 'Gagal memperbarui data kategori. Periksa log error.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menghapus data kategori (soft delete).
     * URL: /admin/kategori/hapus-data-kategori/{id_kategori} (POST)
     */
    public function hapus_data_kategori($id_kategori = null)
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) { // KOREKSI SESI
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Validasi ID kategori dari URL
        if (empty($id_kategori)) {
            $this->session->setFlashdata('error', 'ID Kategori tidak valid untuk dihapus.');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        // Mengambil data kategori untuk memastikan keberadaan dan statusnya (aktif/dihapus)
        $kategori = $this->mKategori->withDeleted()->find($id_kategori); // Gunakan withDeleted()

        // Memeriksa apakah data kategori ditemukan
        if (!$kategori) {
            $this->session->setFlashdata('error', 'Data kategori tidak ditemukan!');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }
        
        // Memeriksa apakah kategori sudah di-soft delete
        if (($kategori['is_delete_kategori'] ?? 0) == 1) { // KOREKSI: Cek status is_delete_kategori
            $this->session->setFlashdata('error', 'Data kategori sudah dihapus!');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        }

        // Memanggil Query Builder langsung dari DB Connection untuk soft delete
        $dataUpdate = [
            'is_delete_kategori' => 1,
            'updated_at'         => date('Y-m-d H:i:s')
        ];

        // Ini adalah cara eksplisit untuk melakukan update tanpa melewati metode delete() dari Model
        if ($this->db->table($this->mKategori->table)->where($this->mKategori->primaryKey, $id_kategori)->update($dataUpdate)) {
            $this->session->setFlashdata('success', 'Data Kategori Berhasil Dihapus!');
            return redirect()->to(base_url('admin/kategori/master-data-kategori'));
        } else {
            $error = $this->db->error();
            log_message('error', 'Gagal menghapus kategori: ' . $error['message'] . ' - SQL: ' . $this->db->getLastQuery());
            $this->session->setFlashdata('error', 'Gagal menghapus data kategori. Periksa log error aplikasi.');
            return redirect()->back();
        }
    }
}