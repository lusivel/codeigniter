<?php

namespace App\Controllers; 

use App\Controllers\BaseController; 
use App\Models\M_Buku; // Memuat Model Buku
use App\Models\M_Kategori; // Memuat Model Kategori
use App\Models\M_Rak;     // Memuat Model Rak
use CodeIgniter\Validation\Validation; // Memuat library Validasi

class Buku extends BaseController 
{
    protected $bukuModel;
    protected $kategoriModel;
    protected $rakModel;
    protected $session; 
    protected $validation; 

    public function __construct()
    {
        // Inisialisasi Model, Session, dan Validation Service
        $this->bukuModel = new M_Buku(); 
        $this->kategoriModel = new M_Kategori(); 
        $this->rakModel = new M_Rak();     
        
        helper(['form', 'url', 'date']); 
        $this->session = \Config\Services::session(); 
        $this->validation = \Config\Services::validation(); 
    }

    /**
     * Menampilkan master data buku.
     * URL: /admin/buku/master-data-buku
     */
    public function master_data_buku()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('isLoggedIn')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $data['web_title'] = "Master Data Buku"; // Judul halaman
        // Mengambil semua data buku yang aktif
        $data['data_buku'] = $this->bukuModel->get_all_active_buku(); 
        $data['pages'] = 'buku'; // Untuk highlight di sidebar
        
        // Memuat view header, sidebar, master data buku, dan footer
        echo view('Backend/Template/Header', $data); 
        echo view('Backend/Template/Sidebar', $data); 
        echo view('Backend/MasterBuku/master-data-buku', $data); 
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Menampilkan form input data buku baru.
     * URL: /admin/buku/input-data-buku
     */
    public function input_data_buku()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('isLoggedIn')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $data['web_title'] = "Input Data Buku"; // Judul halaman
        // Mengambil daftar kategori dan rak aktif untuk dropdown
        $data['data_kategori'] = $this->kategoriModel->get_all_kategori_aktif(); 
        $data['data_rak'] = $this->rakModel->get_all_rak_aktif();
        $data['pages'] = 'buku'; 
        
        // Memuat view header, sidebar, input data buku, dan footer
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterBuku/input-data-buku', $data); // View form input buku
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Metode internal untuk upload file (cover buku atau e-book).
     * @param string $fileInputName Nama input file di form (misal: 'cover_buku').
     * @param string $uploadPathSubDir Sub-direktori di 'uploads/' tempat file akan disimpan.
     * @param array $allowedMimes Array mime type yang diizinkan.
     * @return array Status upload ('success', 'error', 'no_file') dan nama file atau pesan error.
     */
    private function _upload_file_ci4($fileInputName, $uploadPathSubDir, $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'])
    {
        $file = $this->request->getFile($fileInputName);

        // Memeriksa apakah ada file yang diupload dan valid
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Memeriksa tipe file
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return ['status' => 'error', 'error_message' => 'Tipe file tidak diizinkan: ' . $file->getMimeType()];
            }
            // Memeriksa ukuran file (maksimal 5MB)
            if ($file->getSize() > (5 * 1024 * 1024)) {
                 return ['status' => 'error', 'error_message' => 'Ukuran file melebihi 5MB.'];
            }
            
            // Menggenerate nama file baru yang unik
            $newName = $file->getRandomName(); 
            // Memindahkan file ke direktori tujuan
            if ($file->move(FCPATH . 'uploads/' . $uploadPathSubDir, $newName)) { 
                return ['status' => 'success', 'file_name' => $newName];
            } else {
                return ['status' => 'error', 'error_message' => $file->getErrorString() . '(' . $file->getError() . ')'];
            }
        } elseif ($file && $file->getError() == UPLOAD_ERR_NO_FILE) { 
            // Jika tidak ada file yang diupload
            return ['status' => 'no_file', 'file_name' => null]; 
        } elseif ($file && $file->getError()) {
            // Jika ada error upload selain tidak ada file
            return ['status' => 'error', 'error_message' => $file->getErrorString() . '(' . $file->getError() . ')']; 
        }
        return ['status' => 'no_file', 'file_name' => null]; 
    }

    /**
     * Menyimpan data buku baru.
     * URL: /admin/buku/simpan-data-buku (POST)
     */
    public function simpan_data_buku()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('isLoggedIn')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // --- DEFINISI ATURAN VALIDASI ---
        $rules = [
            'id_buku'           => [
                'label' => 'ID Buku',
                'rules' => 'required|trim|max_length[6]|is_unique[tbl_buku.id_buku]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'max_length' => '{field} maksimal {param} karakter.',
                    'is_unique' => '{field} sudah ada di database.'
                ]
            ],
            'judul_buku'        => 'required|min_length[3]|max_length[200]',
            'pengarang'         => 'permit_empty|max_length[50]', // permit_empty jika tidak wajib
            'penerbit'          => 'permit_empty|max_length[50]', // permit_empty jika tidak wajib
            'tahun_terbit'      => 'required|exact_length[4]|numeric', // Sesuaikan dengan nama kolom DB
            'jumlah_eksemplar'  => 'required|numeric|greater_than_equal_to[0]',
            'id_kategori'       => 'required',
            'id_rak'            => 'required',
            'keterangan'        => 'permit_empty|max_length[500]', // permit_empty jika tidak wajib
            'cover_buku'        => 'permit_empty|max_size[cover_buku,5120]|is_image[cover_buku]|mime_in[cover_buku,image/jpg,image/jpeg,image/gif,image/png]',
            'e_book'            => 'permit_empty|max_size[e_book,5120]|ext_in[e_book,pdf,epub,mobi,doc,docx]'
        ];

        // --- JALANKAN VALIDASI ---
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mengambil data dari POST request
        $data_buku = [
            'id_buku'           => trim($this->request->getPost('id_buku')), // Trim spasi
            'judul_buku'        => $this->request->getPost('judul_buku'),
            'pengarang'         => $this->request->getPost('pengarang'),
            'penerbit'          => $this->request->getPost('penerbit'),
            'tahun_terbit'      => $this->request->getPost('tahun_terbit'), // Ambil dari input 'tahun_terbit'
            'jumlah_eksemplar'  => $this->request->getPost('jumlah_eksemplar'),
            'id_kategori'       => $this->request->getPost('id_kategori'),
            'keterangan'        => $this->request->getPost('keterangan'),
            'id_rak'            => $this->request->getPost('id_rak'),
            'is_delete_buku'    => '0', // Status aktif
            'stok_buku'         => $this->request->getPost('jumlah_eksemplar'), // Stok awal sama dengan jumlah eksemplar
            'created_at'        => date('Y-m-d H:i:s'), 
            'updated_at'        => date('Y-m-d H:i:s')
        ];

        // --- Proses Upload Cover Buku ---
        $upload_cover = $this->_upload_file_ci4('cover_buku', 'cover_buku/', ['image/jpeg', 'image/png', 'image/gif']);
        if ($upload_cover['status'] == 'success') {
            $data_buku['cover_buku'] = $upload_cover['file_name'];
        } elseif ($upload_cover['status'] == 'error') {
            $this->session->setFlashdata('error_upload_cover', $upload_cover['error_message']);
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        } elseif ($upload_cover['status'] == 'no_file') {
            $data_buku['cover_buku'] = NULL; // Jika tidak ada file, set ke NULL
        }

        // --- Proses Upload E-Book ---
        $upload_ebook = $this->_upload_file_ci4('e_book', 'e_book/', ['application/pdf', 'application/epub+zip', 'application/x-mobipocket-ebook', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
        if ($upload_ebook['status'] == 'success') {
            $data_buku['e_book'] = $upload_ebook['file_name'];
        } elseif ($upload_ebook['status'] == 'error') {
            // Jika upload e-book gagal, dan cover sudah terupload, hapus cover yang sudah terupload
            $this->session->setFlashdata('error_upload_ebook', $upload_ebook['error_message']);
            if (!empty($data_buku['cover_buku']) && file_exists(FCPATH . 'uploads/cover_buku/' . $data_buku['cover_buku'])) {
                unlink(FCPATH . 'uploads/cover_buku/' . $data_buku['cover_buku']);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        } elseif ($upload_ebook['status'] == 'no_file') {
            $data_buku['e_book'] = NULL; // Jika tidak ada file, set ke NULL
        }
        
        // Memanggil method insert() dari model
        if ($this->bukuModel->insert($data_buku)) { 
            $this->session->setFlashdata('success', 'Data buku berhasil disimpan!');
            return redirect()->to(base_url('admin/buku/master-data-buku')); 
        } else {
            $this->session->setFlashdata('error', 'Gagal menyimpan data buku ke database. Periksa log error.');
            // Jika penyimpanan ke DB gagal, hapus juga file yang sudah diupload
            if (!empty($data_buku['cover_buku']) && file_exists(FCPATH . 'uploads/cover_buku/' . $data_buku['cover_buku'])) {
                unlink(FCPATH . 'uploads/cover_buku/' . $data_buku['cover_buku']);
            }
            if (!empty($data_buku['e_book']) && file_exists(FCPATH . 'uploads/e_book/' . $data_buku['e_book'])) {
                unlink(FCPATH . 'uploads/e_book/' . $data_buku['e_book']);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

    /**
     * Menampilkan form edit data buku.
     * URL: /admin/buku/edit-data-buku/{id_buku} (GET)
     */
    public function edit_data_buku($id_buku = null) { 
        // Pengecekan sesi login admin
        if (!$this->session->get('isLoggedIn')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Validasi ID buku dari URL
        if (empty($id_buku)) {
            $this->session->setFlashdata('error', 'ID Buku tidak valid.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        // Mengambil data buku berdasarkan ID
        $buku = $this->bukuModel->find($id_buku); 

        // Memeriksa apakah data buku ditemukan atau sudah di-soft delete
        if (empty($buku) || ($buku['is_delete_buku'] ?? '0') == '1') {
            $this->session->setFlashdata('error', 'Data buku tidak ditemukan atau sudah dihapus.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }
        
        $data['web_title'] = "Edit Data Buku";
        $data['data_buku'] = $buku; 
        // Mengambil daftar kategori dan rak aktif untuk dropdown
        $data['data_kategori'] = $this->kategoriModel->get_all_kategori_aktif(); 
        $data['data_rak'] = $this->rakModel->get_all_rak_aktif();
        $data['pages'] = 'buku';

        // Memuat view header, sidebar, form edit buku, dan footer
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterBuku/edit-buku', $data); 
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memperbarui data buku.
     * URL: /admin/buku/update-data-buku (POST)
     */
    public function update_data_buku() {
        // Pengecekan sesi login admin
        if (!$this->session->get('isLoggedIn')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Mengambil ID buku dari hidden input form
        $id_buku_update = $this->request->getPost('id_buku_hidden'); 

        // Validasi ID buku
        if(empty($id_buku_update)){
            $this->session->setFlashdata('error', 'ID Buku tidak valid untuk update.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }
        
        // --- DEFINISI ATURAN VALIDASI UNTUK UPDATE ---
        $rules = [
            'judul_buku'        => 'required|min_length[3]|max_length[200]',
            'pengarang'         => 'permit_empty|max_length[50]',
            'penerbit'          => 'permit_empty|max_length[50]',
            'tahun_terbit'      => 'required|exact_length[4]|numeric',
            'jumlah_eksemplar'  => 'required|numeric|greater_than_equal_to[0]',
            'id_kategori'       => 'required',
            'id_rak'            => 'required',
            'keterangan'        => 'permit_empty|max_length[500]',
            'cover_buku'        => 'permit_empty|max_size[cover_buku,5120]|is_image[cover_buku]|mime_in[cover_buku,image/jpg,image/jpeg,image/gif,image/png]',
            'e_book'            => 'permit_empty|max_size[e_book,5120]|ext_in[e_book,pdf,epub,mobi,doc,docx]'
        ];

        // --- JALANKAN VALIDASI ---
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mengambil data buku lama untuk memeriksa file yang ada
        $buku_lama = $this->bukuModel->find($id_buku_update); 
        if (!$buku_lama) {
             $this->session->setFlashdata('error', 'Data buku yang akan diupdate tidak ditemukan.');
             return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        // Menyiapkan data untuk diperbarui
        $data_update = [
            'judul_buku'        => $this->request->getPost('judul_buku'),
            'pengarang'         => $this->request->getPost('pengarang'),
            'penerbit'          => $this->request->getPost('penerbit'),
            'tahun_terbit'      => $this->request->getPost('tahun_terbit'),
            'jumlah_eksemplar'  => $this->request->getPost('jumlah_eksemplar'),
            'id_kategori'       => $this->request->getPost('id_kategori'),
            'keterangan'        => $this->request->getPost('keterangan'),
            'id_rak'            => $this->request->getPost('id_rak'),
            'updated_at'        => date('Y-m-d H:i:s')
        ];

        // --- Proses Upload Cover Buku (Update) ---
        $upload_cover = $this->_upload_file_ci4('cover_buku', 'cover_buku/', ['image/jpeg', 'image/png', 'image/gif']);
        if ($upload_cover['status'] == 'success') {
            $data_update['cover_buku'] = $upload_cover['file_name'];
            // Hapus cover lama jika ada dan bukan default
            if (!empty($buku_lama['cover_buku']) && $buku_lama['cover_buku'] != 'default_cover.png' && file_exists(FCPATH . 'uploads/cover_buku/' . $buku_lama['cover_buku'])) {
                unlink(FCPATH . 'uploads/cover_buku/' . $buku_lama['cover_buku']);
            }
        } elseif ($upload_cover['status'] == 'error') {
            $this->session->setFlashdata('error_upload_cover', $upload_cover['error_message']);
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // Jika status 'no_file', tidak perlu update cover_buku, biarkan yang lama

        // --- Proses Upload E-Book (Update) ---
        $upload_ebook = $this->_upload_file_ci4('e_book', 'e_book/', ['application/pdf', 'application/epub+zip', 'application/x-mobipocket-ebook', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
        if ($upload_ebook['status'] == 'success') {
            $data_update['e_book'] = $upload_ebook['file_name'];
            // Hapus e-book lama jika ada
            if (!empty($buku_lama['e_book']) && file_exists(FCPATH . 'uploads/e_book/' . $buku_lama['e_book'])) {
                unlink(FCPATH . 'uploads/e_book/' . $buku_lama['e_book']);
            }
        } elseif ($upload_ebook['status'] == 'error') {
            $this->session->setFlashdata('error_upload_ebook', $upload_ebook['error_message']);
            // Jika upload e-book gagal, dan cover baru sudah terupload, hapus cover baru tersebut
            if (isset($data_update['cover_buku']) && $data_update['cover_buku'] != ($buku_lama['cover_buku'] ?? null) && file_exists(FCPATH . 'uploads/cover_buku/' . $data_update['cover_buku'])) {
                unlink(FCPATH . 'uploads/cover_buku/' . $data_update['cover_buku']);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // Jika status 'no_file', tidak perlu update e_book, biarkan yang lama
        
        // Memanggil method update() dari model
        if ($this->bukuModel->update($id_buku_update, $data_update)) { 
            $this->session->setFlashdata('success', 'Data buku berhasil diupdate!');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        } else {
            $this->session->setFlashdata('error', 'Gagal mengupdate data buku ke database. Periksa log error.');
            // Jika update ke DB gagal, hapus file yang baru saja diupload
            if (isset($data_update['cover_buku']) && $data_update['cover_buku'] != ($buku_lama['cover_buku'] ?? null) && file_exists(FCPATH . 'uploads/cover_buku/' . $data_update['cover_buku'])) {
                unlink(FCPATH . 'uploads/cover_buku/' . $data_update['cover_buku']);
            }
            if (isset($data_update['e_book']) && $data_update['e_book'] != ($buku_lama['e_book'] ?? null) && file_exists(FCPATH . 'uploads/e_book/' . $data_update['e_book'])) {
                unlink(FCPATH . 'uploads/e_book/' . $data_update['e_book']);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

    /**
     * Menghapus data buku (soft delete).
     * URL: /admin/buku/hapus-data-buku/{id_buku} (POST)
     */
    public function hapus_data_buku($id_buku = null) { 
        // Pengecekan sesi login admin
        if (!$this->session->get('isLoggedIn')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Validasi ID buku dari URL
        if (empty($id_buku)) {
            $this->session->setFlashdata('error', 'ID Buku tidak valid untuk dihapus.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        // Mengambil data buku untuk memastikan keberadaan sebelum menghapus
        $buku = $this->bukuModel->find($id_buku);

        // Memeriksa apakah data buku ditemukan dan belum di-soft delete
        if (empty($buku) || ($buku['is_delete_buku'] ?? '0') == '1') {
            $this->session->setFlashdata('error', 'Data buku tidak ditemukan atau sudah dihapus.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        // Menyiapkan data untuk soft delete
        $data_update = [
            'is_delete_buku' => '1', // Set status menjadi tidak aktif
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        // Memanggil method update() dari model untuk soft delete
        if ($this->bukuModel->update($id_buku, $data_update)) { 
            $this->session->setFlashdata('success', 'Data buku berhasil dihapus (soft delete)!');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        } else {
            $this->session->setFlashdata('error', 'Gagal menghapus data buku. Periksa log error.');
            return redirect()->back();
        }
    }
}