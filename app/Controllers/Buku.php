<?php

namespace App\Controllers;

use App\Models\M_Buku;
use App\Models\M_Kategori;
use App\Models\M_Rak;

class Buku extends BaseController
{
    protected $mBuku;
    protected $mKategori;
    protected $mRak;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->mBuku = new M_Buku();
        $this->mKategori = new M_Kategori();
        $this->mRak = new M_Rak();
        helper(['form', 'url', 'date', 'filesystem']);
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }

    /**
     * Memastikan user sudah login.
     * Mengembalikan RedirectResponse jika tidak login, atau true jika login.
     * @return \CodeIgniter\HTTP\RedirectResponse|bool
     */
    private function isLoggedIn()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }
        return true;
    }

    public function master_data_buku()
    {
        if (($check = $this->isLoggedIn()) !== true) {
            return $check;
        }

        $uri = service('uri');
        
        $dataBuku = $this->mBuku->getActiveBuku();

        $data['pages']     = $uri->getSegment(2) ?? 'master-data-buku';
        $data['data_buku'] = $dataBuku;
        $data['web_title'] = "Master Data Buku";

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterBuku/master-data-buku', $data);
        echo view('Backend/Template/Footer', $data);
    }

    public function input_data_buku()
    {
        if (($check = $this->isLoggedIn()) !== true) {
            return $check;
        }

        $data['web_title'] = "Input Data Buku";
        $data['kategori_list'] = $this->mKategori->getActiveKategori();
        $data['rak_list'] = $this->mRak->getActiveRak();

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterBuku/input-data-buku', $data); // Menggunakan input-data-buku sesuai permintaan terakhir
        echo view('Backend/Template/Footer', $data);
    }

    public function simpan_data_buku()
    {
        if (($check = $this->isLoggedIn()) !== true) {
            return $check;
        }

        // --- DEFINISI ATURAN VALIDASI ---
        $rules = [
            'judul_buku' => 'required|min_length[3]|max_length[200]|is_unique[tbl_buku.judul_buku,is_delete_buku,0]',
            'pengarang' => 'required|min_length[3]|max_length[50]',
            'penerbit' => 'required|min_length[3]|max_length[50]',
            'tahun_terbit' => 'required|numeric|exact_length[4]|greater_than_equal_to[1900]|less_than_equal_to[' . date('Y') . ']',
            'jumlah_eksemplar' => 'required|numeric|greater_than[0]',
            'id_kategori' => 'required',
            'id_rak' => 'required',
            'cover_buku' => [
                'rules'  => 'max_size[cover_buku,1024]|is_image[cover_buku]|mime_in[cover_buku,image/jpg,image/jpeg,image/png,image/gif]',
                'errors' => [
                    'max_size'  => 'Ukuran cover buku maksimal 1MB.',
                    'is_image'  => 'File harus berupa gambar.',
                    'mime_in'   => 'Format gambar yang diizinkan: jpg, jpeg, png, gif.'
                ]
            ],
            'e_book' => [
                'rules'  => 'max_size[e_book,10240]|mime_in[e_book,application/pdf]', // 10MB
                'errors' => [
                    'max_size'  => 'Ukuran e-book maksimal 10MB.',
                    'mime_in'   => 'Format e-book yang diizinkan: pdf.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mengambil data dari POST request
        $judul_buku         = $this->request->getPost('judul_buku');
        $pengarang          = $this->request->getPost('pengarang');
        $penerbit           = $this->request->getPost('penerbit');
        $tahun_terbit       = $this->request->getPost('tahun_terbit');
        $jumlah_eksemplar   = $this->request->getPost('jumlah_eksemplar');
        $id_kategori        = $this->request->getPost('id_kategori');
        $id_rak             = $this->request->getPost('id_rak');
        $keterangan         = $this->request->getPost('keterangan');

        // --- Logika Upload Cover Buku ---
        $fileCover = $this->request->getFile('cover_buku');
        $namaCover = null;
        if ($fileCover && $fileCover->isValid() && !$fileCover->hasMoved()) {
            $namaCover = $fileCover->getRandomName();
            $fileCover->move(FCPATH . 'uploads/covers', $namaCover);
        }

        // --- Logika Upload E-Book ---
        $fileEbook = $this->request->getFile('e_book');
        $namaEbook = null;
        if ($fileEbook && $fileEbook->isValid() && !$fileEbook->hasMoved()) {
            $namaEbook = $fileEbook->getRandomName();
            $fileEbook->move(FCPATH . 'uploads/ebooks', $namaEbook);
        }

        // Generate ID buku otomatis
        $hasilAutoNumber = $this->mBuku->autoNumber(); // Diperbaiki: Tidak ada lagi ->getRowArray()
        $id_buku = "BK0001";
        if ($hasilAutoNumber && !empty($hasilAutoNumber['id_buku'])) {
            $kode   = $hasilAutoNumber['id_buku'];
            $noUrut = (int)substr($kode, 2);
            $noUrut++;
            $id_buku = "BK" . sprintf("%04s", $noUrut);
        }

        // Menyiapkan data untuk disimpan
        $dataSimpan = [
            'id_buku'           => $id_buku,
            'judul_buku'        => $judul_buku,
            'pengarang'         => $pengarang,
            'penerbit'          => $penerbit,
            'tahun_terbit'      => $tahun_terbit,
            'jumlah_eksemplar'  => $jumlah_eksemplar,
            'id_kategori'       => $id_kategori,
            'id_rak'            => $id_rak,
            'keterangan'        => $keterangan,
            'cover_buku'        => $namaCover,
            'e_book'            => $namaEbook,
            'is_delete_buku'    => 0, // Default aktif
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        try {
            if ($this->mBuku->insert($dataSimpan)) {
                $this->session->setFlashdata('success', 'Data Buku Berhasil Ditambahkan!');
                return redirect()->to(base_url('admin/buku/master-data-buku'));
            } else {
                $error = $this->db->error(); 
                log_message('error', 'Gagal menyimpan buku (insert returned false): ' . $error['message'] . ' - SQL: ' . $this->db->getLastQuery());
                $this->session->setFlashdata('error', 'Gagal menyimpan data buku. Terjadi masalah saat memasukkan data.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', 'Gagal menyimpan buku (exception): ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Gagal menyimpan data buku. Terjadi kesalahan sistem. Silakan coba lagi nanti.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menampilkan form edit data buku.
     * URL: /admin/buku/edit-data-buku/{id_buku} (GET)
     */
    public function edit_data_buku($id_buku = null)
    {
        if (($check = $this->isLoggedIn()) !== true) {
            return $check;
        }

        if (empty($id_buku)) {
            $this->session->setFlashdata('error', 'ID Buku tidak valid.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        $dataBuku = $this->mBuku->withDeleted()->find($id_buku);

        if (!$dataBuku || ($dataBuku['is_delete_buku'] ?? 0) == 1) {
            $this->session->setFlashdata('error', 'Data buku tidak ditemukan atau sudah dihapus.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }
        
        $data['web_title'] = "Edit Data Buku";
        $data['data_buku'] = $dataBuku;
        $data['kategori_list'] = $this->mKategori->getActiveKategori();
        $data['rak_list'] = $this->mRak->getActiveRak();

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterBuku/edit-buku', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memperbarui data buku.
     * URL: /admin/buku/update-data-buku (POST)
     */
    public function update_data_buku()
    {
        if (($check = $this->isLoggedIn()) !== true) {
            return $check;
        }

        $idUpdate = $this->request->getPost('id_buku');

        if (empty($idUpdate)) {
            $this->session->setFlashdata('error', 'ID Buku tidak valid untuk update.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }
        
        // Ambil data buku lama untuk validasi is_unique dan path file lama
        $oldBookData = $this->mBuku->withDeleted()->find($idUpdate);
        if (!$oldBookData) {
            $this->session->setFlashdata('error', 'Data buku tidak ditemukan untuk diperbarui.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        // --- DEFINISI ATURAN VALIDASI UNTUK UPDATE ---
        $rules = [
            'judul_buku' => [
                'label' => 'Judul Buku',
                // Mengembalikan is_unique dengan pengecualian ID saat ini dan is_delete_buku = 0
                'rules' => "required|min_length[3]|max_length[200]|is_unique[tbl_buku.judul_buku,id_buku,{$idUpdate},is_delete_buku,0]", 
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.',
                    'max_length' => '{field} maksimal {param} karakter.',
                    'is_unique' => '{field} sudah digunakan oleh buku lain yang aktif.' 
                ]
            ],
            'pengarang' => 'required|min_length[3]|max_length[50]',
            'penerbit' => 'required|min_length[3]|max_length[50]',
            'tahun_terbit' => 'required|numeric|exact_length[4]|greater_than_equal_to[1900]|less_than_equal_to[' . date('Y') . ']',
            'jumlah_eksemplar' => 'required|numeric|greater_than[0]',
            'id_kategori' => 'required',
            'id_rak' => 'required',
            'cover_buku' => [
                'rules'  => 'if_exist|max_size[cover_buku,1024]|is_image[cover_buku]|mime_in[cover_buku,image/jpg,image/jpeg,image/png,image/gif]',
                'errors' => [
                    'max_size'  => 'Ukuran cover buku maksimal 1MB.',
                    'is_image'  => 'File harus berupa gambar.',
                    'mime_in'   => 'Format gambar yang diizinkan: jpg, jpeg, png, gif.'
                ]
            ],
            'e_book' => [
                'rules'  => 'if_exist|max_size[e_book,10240]|mime_in[e_book,application/pdf]', // 10MB
                'errors' => [
                    'max_size'  => 'Ukuran e-book maksimal 10MB.',
                    'mime_in'   => 'Format e-book yang diizinkan: pdf.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $judul_buku         = $this->request->getPost('judul_buku');
        $pengarang          = $this->request->getPost('pengarang');
        $penerbit           = $this->request->getPost('penerbit');
        $tahun_terbit       = $this->request->getPost('tahun_terbit');
        $jumlah_eksemplar   = $this->request->getPost('jumlah_eksemplar');
        $id_kategori        = $this->request->getPost('id_kategori');
        $id_rak             = $this->request->getPost('id_rak');
        $keterangan         = $this->request->getPost('keterangan');

        $oldCover    = $oldBookData['cover_buku'] ?? null;
        $oldEbook    = $oldBookData['e_book'] ?? null;

        // --- Logika Upload Cover Buku ---
        $fileCover = $this->request->getFile('cover_buku');
        $namaCover = $oldCover; // Defaultnya pakai nama cover lama
        // Jika ada file cover baru diupload
        if ($fileCover && $fileCover->isValid() && !$fileCover->hasMoved()) {
            // Hapus cover lama jika ada
            if ($oldCover && file_exists(FCPATH . 'uploads/covers/' . $oldCover)) {
                unlink(FCPATH . 'uploads/covers/' . $oldCover);
            }
            $namaCover = $fileCover->getRandomName();
            $fileCover->move(FCPATH . 'uploads/covers', $namaCover);
        } 
        // Logika asumsikan ada hidden input 'clear_cover' di form (atau checkbox)
        else if ($this->request->getPost('clear_cover') == '1') {
            if ($oldCover && file_exists(FCPATH . 'uploads/covers/' . $oldCover)) {
                unlink(FCPATH . 'uploads/covers/' . $oldCover);
            }
            $namaCover = null;
        }

        // --- Logika Upload E-Book ---
        $fileEbook = $this->request->getFile('e_book');
        $namaEbook = $oldEbook; // Defaultnya pakai nama ebook lama
        // Jika ada file e-book baru diupload
        if ($fileEbook && $fileEbook->isValid() && !$fileEbook->hasMoved()) {
            // Hapus e-book lama jika ada
            if ($oldEbook && file_exists(FCPATH . 'uploads/ebooks/' . $oldEbook)) {
                unlink(FCPATH . 'uploads/ebooks/' . $oldEbook);
            }
            $namaEbook = $fileEbook->getRandomName();
            $fileEbook->move(FCPATH . 'uploads/ebooks', $namaEbook);
        }
        // Logika asumsikan ada hidden input 'clear_ebook' di form (atau checkbox)
        else if ($this->request->getPost('clear_ebook') == '1') {
            if ($oldEbook && file_exists(FCPATH . 'uploads/ebooks/' . $oldEbook)) {
                unlink(FCPATH . 'uploads/ebooks/' . $oldEbook);
            }
            $namaEbook = null;
        }

        // Menyiapkan data untuk diperbarui
        $dataUpdate = [
            'judul_buku'        => $judul_buku,
            'pengarang'         => $pengarang,
            'penerbit'          => $penerbit,
            'tahun_terbit'      => $tahun_terbit,
            'jumlah_eksemplar'  => $jumlah_eksemplar,
            'id_kategori'       => $id_kategori,
            'id_rak'            => $id_rak,
            'keterangan'        => $keterangan,
            'cover_buku'        => $namaCover,
            'e_book'            => $namaEbook,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        try {
            if ($this->mBuku->update($idUpdate, $dataUpdate)) {
                $this->session->setFlashdata('success', 'Data Buku Berhasil Diperbarui!');
                return redirect()->to(base_url('admin/buku/master-data-buku'));
            } else {
                $error = $this->db->error();
                log_message('error', 'Gagal memperbarui buku (update returned false): ' . $error['message'] . ' - SQL: ' . $this->db->getLastQuery());
                $this->session->setFlashdata('error', 'Gagal memperbarui data buku. Terjadi masalah saat memperbarui data.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', 'Gagal memperbarui buku (exception): ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Gagal memperbarui data buku. Terjadi kesalahan sistem. Silakan coba lagi nanti.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menghapus data buku (soft delete).
     * URL: /admin/buku/hapus-data-buku/{id_buku} (POST)
     */
    public function hapus_data_buku($id_buku = null)
    {
        if (($check = $this->isLoggedIn()) !== true) {
            return $check;
        }

        if (empty($id_buku)) {
            $this->session->setFlashdata('error', 'ID Buku tidak valid untuk dihapus.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        $buku = $this->mBuku->withDeleted()->find($id_buku);

        if (!$buku) {
            $this->session->setFlashdata('error', 'Data buku tidak ditemukan!');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        if (($buku['is_delete_buku'] ?? 0) == 1) {
            $this->session->setFlashdata('error', 'Data buku sudah dihapus sebelumnya!');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        // Ambil nama file cover dan e-book untuk dihapus fisik
        $cover_buku_file = $buku['cover_buku'] ?? null;
        $e_book_file     = $buku['e_book'] ?? null;

        try {
            // Lakukan soft delete di database
            if ($this->mBuku->delete($id_buku)) {
                // Hapus file fisik jika soft delete berhasil
                if ($cover_buku_file && file_exists(FCPATH . 'uploads/covers/' . $cover_buku_file)) {
                    unlink(FCPATH . 'uploads/covers/' . $cover_buku_file);
                }
                if ($e_book_file && file_exists(FCPATH . 'uploads/ebooks/' . $e_book_file)) {
                    unlink(FCPATH . 'uploads/ebooks/' . $e_book_file);
                }

                $this->session->setFlashdata('success', 'Data Buku Berhasil Dihapus!');
                return redirect()->to(base_url('admin/buku/master-data-buku'));
            } else {
                $error = $this->db->error();
                log_message('error', 'Gagal menghapus buku (delete returned false): ' . $error['message'] . ' - SQL: ' . $this->db->getLastQuery());
                $this->session->setFlashdata('error', 'Gagal menghapus data buku. Terjadi masalah saat menghapus data.');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            log_message('error', 'Gagal menghapus buku (exception): ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Gagal menghapus data buku. Terjadi kesalahan sistem. Silakan coba lagi nanti.');
            return redirect()->back();
        }
    }
}