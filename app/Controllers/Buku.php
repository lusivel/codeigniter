<?php

namespace App\Controllers;

use App\Models\M_Buku;
use App\Models\M_Kategori;
use App\Models\M_Rak;
use CodeIgniter\Files\File;

class Buku extends BaseController
{
    protected $mBuku;
    protected $mKategori;
    protected $mRak;
    protected $session;

    public function __construct()
    {
        $this->mBuku = new M_Buku();
        $this->mKategori = new M_Kategori();
        $this->mRak = new M_Rak();
        helper(['form', 'url', 'date', 'filesystem']);
        $this->session = \Config\Services::session();
    }

    /**
     * Menampilkan halaman utama master data buku.
     */
    public function master_data_buku()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $uri = service('uri');

        // Mengambil data buku yang aktif melalui Model
        $dataBuku = $this->mBuku->getActiveBooks();

        $data['pages']     = $uri->getSegment(2) ?? 'master-data-buku';
        $data['data_buku'] = $dataBuku;
        $data['web_title'] = "Master Data Buku";

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterBuku/master-data-buku', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Menampilkan form untuk menginput data buku baru.
     */
    public function input_data_buku()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $data['web_title'] = "Input Data Buku";
        $data['kategori_list'] = $this->mKategori->getActiveKategori();
        $data['rak_list'] = $this->mRak->getActiveRak();

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterBuku/input-data-buku', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memproses dan menyimpan data buku baru.
     */
    public function simpan_data_buku()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $rules = [
            'judul_buku' => 'required|min_length[3]|max_length[200]',
            'pengarang' => 'required|min_length[3]|max_length[50]',
            'penerbit' => 'required|min_length[3]|max_length[50]',
            'tahun_terbit' => 'required|numeric|exact_length[4]',
            'jumlah_eksemplar' => 'required|numeric|greater_than[0]',
            'id_kategori' => 'required|alpha_dash|max_length[6]',
            'id_rak' => 'required|alpha_dash|max_length[6]',
            'cover_buku' => 'uploaded[cover_buku]|max_size[cover_buku,2048]|is_image[cover_buku]|mime_in[cover_buku,image/jpg,image/jpeg,image/png]',
            'e_book' => 'permit_empty|max_size[e_book,10240]|ext_in[e_book,pdf,epub]',
            'keterangan' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $cover_buku = $this->request->getFile('cover_buku');
        $e_book = $this->request->getFile('e_book');

        $hasilAutoNumber = $this->mBuku->autoNumber()->getRowArray();
        $id_buku = "BUK001";
        if ($hasilAutoNumber && !empty($hasilAutoNumber['id_buku'])) {
            $kode   = $hasilAutoNumber['id_buku'];
            $noUrut = (int)substr($kode, 3);
            $noUrut++;
            $id_buku = "BUK" . sprintf("%03s", $noUrut);
        }

        $namaCoverBaru = null;
        if ($cover_buku->isValid() && !$cover_buku->hasMoved()) {
            $namaCoverBaru = $cover_buku->getRandomName();
            $cover_buku->move(ROOTPATH . 'public/uploads/covers', $namaCoverBaru);
        }

        $namaEbookBaru = null;
        if ($e_book->isValid() && !$e_book->hasMoved()) {
            $namaEbookBaru = $e_book->getRandomName();
            $e_book->move(ROOTPATH . 'public/uploads/ebooks', $namaEbookBaru);
        }

        $dataSimpan = [
            'id_buku'           => $id_buku,
            'cover_buku'        => $namaCoverBaru,
            'judul_buku'        => $this->request->getPost('judul_buku'),
            'pengarang'         => $this->request->getPost('pengarang'),
            'penerbit'          => $this->request->getPost('penerbit'),
            'tahun_terbit'      => $this->request->getPost('tahun_terbit'),
            'jumlah_eksemplar'  => $this->request->getPost('jumlah_eksemplar'),
            'id_kategori'       => $this->request->getPost('id_kategori'),
            'id_rak'            => $this->request->getPost('id_rak'),
            'keterangan'        => $this->request->getPost('keterangan'),
            'e_book'            => $namaEbookBaru,
        ];

        if ($this->mBuku->insert($dataSimpan)) {
            $this->session->setFlashdata('success', 'Data Buku Berhasil Ditambahkan!');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        } else {
            $this->session->setFlashdata('error', 'Gagal menyimpan data buku. Periksa log error.');
            if ($namaCoverBaru && file_exists(ROOTPATH . 'public/uploads/covers/' . $namaCoverBaru)) {
                unlink(ROOTPATH . 'public/uploads/covers/' . $namaCoverBaru);
            }
            if ($namaEbookBaru && file_exists(ROOTPATH . 'public/uploads/ebooks/' . $namaEbookBaru)) {
                unlink(ROOTPATH . 'public/uploads/ebooks/' . $namaEbookBaru);
            }
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menampilkan form untuk mengedit data buku.
     */
    public function edit_data_buku($id_buku = null)
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        if (empty($id_buku)) {
            $this->session->setFlashdata('error', 'ID Buku tidak valid.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        $dataBuku = $this->mBuku->find($id_buku);

        if (!$dataBuku) {
            $this->session->setFlashdata('error', 'Data buku tidak ditemukan atau sudah dihapus!');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        $data['web_title']    = "Edit Data Buku";
        $data['data_buku'] = $dataBuku;
        $data['kategori_list'] = $this->mKategori->getActiveKategori();
        $data['rak_list'] = $this->mRak->getActiveRak();

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterBuku/edit-buku', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memproses dan memperbarui data buku.
     */
    public function update_data_buku()
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $idUpdate = $this->request->getPost('id_buku');

        if (empty($idUpdate)) {
            $this->session->setFlashdata('error', 'ID Buku tidak valid untuk update.');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        }

        $oldBuku = $this->mBuku->find($idUpdate);
        if (!$oldBuku) {
            $this->session->setFlashdata('error', 'Data buku tidak ditemukan untuk diperbarui.');
            return redirect()->back()->withInput();
        }

        $rules = [
            'judul_buku' => 'required|min_length[3]|max_length[200]',
            'pengarang' => 'required|min_length[3]|max_length[50]',
            'penerbit' => 'required|min_length[3]|max_length[50]',
            'tahun_terbit' => 'required|numeric|exact_length[4]',
            'jumlah_eksemplar' => 'required|numeric|greater_than[0]',
            'id_kategori' => 'required|alpha_dash|max_length[6]',
            'id_rak' => 'required|alpha_dash|max_length[6]',
            'keterangan' => 'permit_empty|max_length[500]',
            'cover_buku' => 'permit_empty|max_size[cover_buku,2048]|is_image[cover_buku]|mime_in[cover_buku,image/jpg,image/jpeg,image/png]',
            'e_book' => 'permit_empty|max_size[e_book,10240]|ext_in[e_book,pdf,epub]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'judul_buku'        => $this->request->getPost('judul_buku'),
            'pengarang'         => $this->request->getPost('pengarang'),
            'penerbit'          => $this->request->getPost('penerbit'),
            'tahun_terbit'      => $this->request->getPost('tahun_terbit'),
            'jumlah_eksemplar'  => $this->request->getPost('jumlah_eksemplar'),
            'id_kategori'       => $this->request->getPost('id_kategori'),
            'id_rak'            => $this->request->getPost('id_rak'),
            'keterangan'        => $this->request->getPost('keterangan'),
        ];

        $cover_buku = $this->request->getFile('cover_buku');
        if ($cover_buku && $cover_buku->isValid() && !$cover_buku->hasMoved()) {
            if ($oldBuku['cover_buku'] && file_exists(ROOTPATH . 'public/uploads/covers/' . $oldBuku['cover_buku'])) {
                unlink(ROOTPATH . 'public/uploads/covers/' . $oldBuku['cover_buku']);
            }
            $namaCoverBaru = $cover_buku->getRandomName();
            $cover_buku->move(ROOTPATH . 'public/uploads/covers', $namaCoverBaru);
            $dataUpdate['cover_buku'] = $namaCoverBaru;
        }

        $e_book = $this->request->getFile('e_book');
        if ($e_book && $e_book->isValid() && !$e_book->hasMoved()) {
            if ($oldBuku['e_book'] && file_exists(ROOTPATH . 'public/uploads/ebooks/' . $oldBuku['e_book'])) {
                unlink(ROOTPATH . 'public/uploads/ebooks/' . $oldBuku['e_book']);
            }
            $namaEbookBaru = $e_book->getRandomName();
            $e_book->move(ROOTPATH . 'public/uploads/ebooks', $namaEbookBaru);
            $dataUpdate['e_book'] = $namaEbookBaru;
        } else if ($this->request->getPost('remove_ebook')) {
            if ($oldBuku['e_book'] && file_exists(ROOTPATH . 'public/uploads/ebooks/' . $oldBuku['e_book'])) {
                unlink(ROOTPATH . 'public/uploads/ebooks/' . $oldBuku['e_book']);
            }
            $dataUpdate['e_book'] = null;
        }

        if ($this->mBuku->update($idUpdate, $dataUpdate)) {
            $this->session->setFlashdata('success', 'Data Buku Berhasil Diperbarui!');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        } else {
            $this->session->setFlashdata('error', 'Gagal memperbarui data buku. Periksa log error.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menghapus data buku (soft delete).
     */
    public function hapus_data_buku($id_buku = null)
    {
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
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

        if ($this->mBuku->delete($id_buku)) {
            $this->session->setFlashdata('success', 'Data Buku Berhasil Dihapus!');
            return redirect()->to(base_url('admin/buku/master-data-buku'));
        } else {
            $this->session->setFlashdata('error', 'Gagal menghapus data buku. Periksa log error.');
            return redirect()->back();
        }
    }
}