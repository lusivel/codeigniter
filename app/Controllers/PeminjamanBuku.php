<?php

namespace App\Controllers;

use App\Models\M_Buku; // Memuat Model Buku
use App\Models\M_Anggota; // Memuat Model Anggota
use App\Models\M_Peminjaman; // Memuat Model Peminjaman

class PeminjamanBuku extends BaseController
{
    protected $mBuku; // Properti untuk instance Model Buku
    protected $mAnggota; // Properti untuk instance Model Anggota
    protected $mPeminjaman; // Properti untuk instance Model Peminjaman

    public function __construct()
    {
        // Inisialisasi Model-Model yang Dibutuhkan
        $this->mBuku = new M_Buku();
        $this->mAnggota = new M_Anggota();
        $this->mPeminjaman = new M_Peminjaman();
    }

    /**
     * Menampilkan form input peminjaman buku Step 1.
     * Pengguna dapat memilih anggota dan buku untuk dipinjam.
     * URL: /admin/peminjaman/step-1 (GET)
     */
    public function input_data_peminjaman()
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Mengambil data buku yang tersedia dan data anggota aktif untuk dropdown/pilihan
        $data['data_buku'] = $this->mBuku->getAvailableBooks();
        $data['data_anggota'] = $this->mAnggota->getActiveMembers();
        $data['pages'] = 'peminjaman'; // Untuk highlight di sidebar

        // Memuat view header, sidebar, form peminjaman Step 1, dan footer
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/Transaksi/peminjaman-step-1', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memproses data dari form peminjaman Step 1.
     * Melakukan validasi awal dan cek pinjaman aktif anggota.
     * URL: /admin/peminjaman/proses-step-1 (POST)
     */
    public function proses_step1_peminjaman()
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Mengambil dan membersihkan ID Buku dan ID Anggota dari POST request
        $idBuku = trim($this->request->getPost('id_buku'));
        $idAnggota = trim($this->request->getPost('id_anggota'));

        // Debugging: uncomment baris di bawah untuk melihat nilai yang diterima
        // dd([
        //     'idBuku_dari_form' => $idBuku,
        //     'idAnggota_dari_form' => $idAnggota,
        //     'tipe_idBuku' => getType($idBuku),
        //     'tipe_idAnggota' => getType($idAnggota),
        // ]);

        // Validasi dasar: memastikan ID buku dan anggota tidak kosong
        if (empty($idBuku) || empty($idAnggota)) {
            session()->setFlashdata('error', 'Pilih buku dan anggota terlebih dahulu!');
            return redirect()->back();
        }

        // Mencari detail buku dan anggota
        $buku = $this->mBuku->getActiveAndAvailableBookById($idBuku); // Mencari buku yang aktif dan tersedia
        $anggota = $this->mAnggota->find($idAnggota); // Mencari anggota

        $errorMessage = ''; // Variabel untuk menyimpan pesan error spesifik

        // Memeriksa keberadaan buku dan anggota
        if (!$buku && !$anggota) {
            $errorMessage = 'Buku dan anggota tidak ditemukan!';
        } elseif (!$buku) {
            $errorMessage = 'Buku dengan ID tersebut tidak ditemukan atau tidak tersedia untuk dipinjam (stok habis/sudah dihapus)!';
        } elseif (!$anggota) {
            $errorMessage = 'Anggota dengan ID tersebut tidak ditemukan!';
        }

        // --- VALIDASI TAMBAHAN: Cek Pinjaman Aktif Anggota ---
        $activeLoans = [];
        if (empty($errorMessage)) { // Hanya cek jika tidak ada error dari pengecekan buku/anggota
            $activeLoans = $this->mPeminjaman->getActiveLoansByAnggotaId($idAnggota);
            if (!empty($activeLoans)) {
                // Jika ada pinjaman aktif, set pesan error dan simpan detailnya ke sesi
                $errorMessage = 'Anggota ini masih memiliki pinjaman buku yang belum dikembalikan.';
                session()->set('temp_active_loans', $activeLoans); // Simpan detail pinjaman aktif ke sesi
            } else {
                session()->remove('temp_active_loans'); // Pastikan sesi ini dihapus jika tidak ada pinjaman aktif
            }
        }

        // Jika ada pesan error, set flashdata dan kembali ke halaman sebelumnya
        if (!empty($errorMessage)) {
            session()->setFlashdata('error', $errorMessage);
            return redirect()->back();
        }

        // Jika semua validasi lolos, simpan data sementara ke sesi
        session()->set([
            'temp_id_buku' => $idBuku,
            'temp_id_anggota' => $idAnggota,
            'temp_judul_buku' => $buku['judul_buku'],
            'temp_nama_anggota' => $anggota['nama_anggota'],
            'temp_tanggal_pinjam' => date('Y-m-d'), // Tanggal pinjam otomatis hari ini
            'temp_tanggal_kembali_estimasi' => date('Y-m-d', strtotime('+7 days')) // Estimasi kembali 7 hari kemudian
        ]);

        // Redirect ke halaman konfirmasi peminjaman Step 2
        return redirect()->to(base_url('admin/peminjaman/step-2'));
    }

    /**
     * Menampilkan halaman konfirmasi peminjaman buku Step 2.
     * Menampilkan detail pinjaman sementara atau daftar pinjaman aktif jika ada.
     * URL: /admin/peminjaman/step-2 (GET)
     */
    public function tampil_step2_peminjaman()
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Mengambil data peminjaman sementara dari sesi
        $dataPeminjamanSementara = session()->get();
        // Mengambil data pinjaman aktif dari sesi (jika ada, dari cek di proses_step1_peminjaman)
        $dataActiveLoans = session()->get('temp_active_loans');

        // Jika tidak ada data peminjaman sementara DAN tidak ada pinjaman aktif yang ditampilkan
        if (empty($dataPeminjamanSementara['temp_id_buku']) || empty($dataPeminjamanSementara['temp_id_anggota'])) {
            if (empty($dataActiveLoans)) { 
                 session()->setFlashdata('error', 'Data peminjaman tidak lengkap. Silakan ulangi dari awal.');
                 return redirect()->to(base_url('admin/peminjaman/step-1'));
            }
        }

        $data['pages'] = 'peminjaman'; // Untuk highlight di sidebar
        $data['data_peminjaman_sementara'] = $dataPeminjamanSementara; // Data pinjaman yang baru diajukan
        $data['data_active_loans'] = $dataActiveLoans; // Data pinjaman anggota yang belum dikembalikan
        
        // Mempersiapkan data untuk tombol "Selesaikan Peminjaman" agar aktif
        // Ini akan terisi jika ada data peminjaman sementara dari Step 1
        $data['temp_peminjaman'] = []; 
        if (!empty($dataPeminjamanSementara['temp_id_buku'])) {
            $data['temp_peminjaman'][] = [
                'id_buku' => $dataPeminjamanSementara['temp_id_buku'],
                'judul_buku' => $dataPeminjamanSementara['temp_judul_buku'],
            ];
        }

        // Memuat view header, sidebar, konfirmasi peminjaman Step 2, dan footer
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/Transaksi/peminjaman-step-2', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memproses data dari halaman Step 2 sebelum penyimpanan final.
     * URL: /admin/peminjaman/proses-step-2 (POST)
     */
    public function proses_step2_data()
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Cek kembali jika ada pinjaman aktif (validasi ulang untuk keamanan)
        $idAnggota = session()->get('temp_id_anggota');
        if ($this->mPeminjaman->hasActiveLoans($idAnggota)) {
            $this->session->setFlashdata('error', 'Anggota ini masih memiliki pinjaman buku yang belum dikembalikan.');
            return redirect()->to(base_url('admin/peminjaman/step-2')); // Kembali ke Step 2
        }

        // Memastikan semua data yang dibutuhkan dari sesi ada
        $idBuku = session()->get('temp_id_buku');
        $idAnggota = session()->get('temp_id_anggota');
        $tanggalPinjam = session()->get('temp_tanggal_pinjam');
        $tanggalKembaliEstimasi = session()->get('temp_tanggal_kembali_estimasi');

        if (empty($idBuku) || empty($idAnggota) || empty($tanggalPinjam) || empty($tanggalKembaliEstimasi)) {
            session()->setFlashdata('error', 'Data peminjaman tidak lengkap. Silakan ulangi dari awal.');
            return redirect()->to(base_url('admin/peminjaman/step-1'));
        }

        // Redirect ke proses penyimpanan final
        return redirect()->to(base_url('admin/peminjaman/konfirmasi-peminjaman'));
    }

    /**
     * Menyimpan data peminjaman buku ke database secara final.
     * URL: /admin/peminjaman/konfirmasi-peminjaman (POST)
     */
    public function simpan_data_peminjaman()
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Cek terakhir jika ada pinjaman aktif sebelum menyimpan (validasi keamanan ganda)
        $idAnggota = session()->get('temp_id_anggota');
        if ($this->mPeminjaman->hasActiveLoans($idAnggota)) {
            $this->session->setFlashdata('error', 'Anggota ini masih memiliki pinjaman buku yang belum dikembalikan dan tidak dapat meminjam lagi.');
            return redirect()->to(base_url('admin/peminjaman/step-2')); // Kembali ke Step 2
        }

        // Mengambil data peminjaman dari sesi
        $idBuku = session()->get('temp_id_buku');
        $idAnggota = session()->get('temp_id_anggota');
        $tanggalPinjam = session()->get('temp_tanggal_pinjam');
        $tanggalKembaliEstimasi = session()->get('temp_tanggal_kembali_estimasi');

        // Validasi data sesi tidak kosong
        if (empty($idBuku) || empty($idAnggota)) {
            session()->setFlashdata('error', 'Data peminjaman tidak lengkap. Silakan ulangi proses.');
            return redirect()->to(base_url('admin/peminjaman/step-1'));
        }

        // --- LOGIKA AUTO NUMBER UNTUK no_peminjaman ---
        $hasilAutoNumber = $this->mPeminjaman->autoNumberPeminjaman()->getRowArray();
        $no_peminjaman = "PMJ001"; // Default jika tabel kosong
        if ($hasilAutoNumber && !empty($hasilAutoNumber['no_peminjaman'])) {
            $kode   = $hasilAutoNumber['no_peminjaman'];
            $noUrut = (int)substr($kode, -3); // Ambil 3 digit terakhir
            $noUrut++;
            $no_peminjaman = "PMJ" . sprintf("%03s", $noUrut); // Format ulang ID (contoh: PMJ001, PMJ002)
        }
        // --- AKHIR LOGIKA AUTO NUMBER ---

        // Menyiapkan data untuk disimpan ke tabel tbl_peminjaman
        $dataSimpan = [
            'no_peminjaman' => $no_peminjaman,
            'id_buku' => $idBuku,
            'id_anggota' => $idAnggota,
            'id_admin' => session()->get('ses_id'), // Mengambil ID Admin dari sesi login
            'tgl_pinjam' => $tanggalPinjam,
            'tanggal_kembali_estimasi' => $tanggalKembaliEstimasi,
            'status_peminjaman' => 'dipinjam', // Status awal saat meminjam
            'status_ambil_buku' => 'belum diambil', // Status pengambilan buku
            'total_pinjam' => 1, // Jumlah buku dalam transaksi ini (asumsi 1 untuk alur ini)
            'qr_code' => NULL, // Default NULL, bisa diisi jika diimplementasikan
            'is_delete_peminjaman' => '0', // Status aktif
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Memanggil method saveDataPeminjaman dari model untuk menyimpan transaksi
        if ($this->mPeminjaman->saveDataPeminjaman($dataSimpan)) {
            // Jika berhasil menyimpan, kurangi stok buku
            $buku = $this->mBuku->find($idBuku);
            if ($buku) {
                $this->mBuku->update($idBuku, ['stok_buku' => $buku['stok_buku'] - 1]);
            }
            // Hapus data temporary dari sesi setelah transaksi berhasil
            session()->remove(['temp_id_buku', 'temp_id_anggota', 'temp_judul_buku', 'temp_nama_anggota', 'temp_tanggal_pinjam', 'temp_tanggal_kembali_estimasi', 'temp_active_loans']);
            
            $this->session->setFlashdata('success', 'Peminjaman buku berhasil dicatat!');
            return redirect()->to(base_url('admin/peminjaman/data-transaksi')); // Redirect ke daftar transaksi
        } else {
            // Jika gagal menyimpan, set pesan error
            $this->session->setFlashdata('error', 'Gagal menyimpan data peminjaman ke database. Periksa log error.');
            return redirect()->back(); // Kembali ke halaman sebelumnya
        }
    }

    /**
     * Menampilkan master data peminjaman.
     * Menampilkan semua transaksi yang aktif dan belum dikembalikan.
     * URL: /admin/peminjaman/data-transaksi
     */
    public function master_data_peminjaman()
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url("admin/login-admin"));
        }

        // Mengambil semua data peminjaman yang aktif dan belum dikembalikan
        $dataPeminjaman = $this->mPeminjaman->getAllPeminjamanJoin();
        $data['pages'] = 'peminjaman';
        $data['data_peminjaman'] = $dataPeminjaman;

        // Memuat view header, sidebar, master data peminjaman, dan footer
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/Transaksi/master-data-peminjaman', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Menampilkan daftar pinjaman yang aktif untuk keperluan pengembalian buku.
     * URL: /admin/peminjaman/daftar-pengembalian
     */
    public function daftar_pengembalian()
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Ambil daftar semua pinjaman yang sedang 'dipinjam' dari database
        $data['loans_to_return'] = $this->mPeminjaman->where('status_peminjaman', 'dipinjam')->findAll();

        $data['pages'] = 'pengembalian';
        $data['web_title'] = 'Daftar Pengembalian Buku';

        // Memuat view header, sidebar, daftar pengembalian, dan footer
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/Transaksi/daftar-pengembalian', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Melakukan soft delete pada data peminjaman.
     * URL: /admin/peminjaman/hapus-data-peminjaman/{no_peminjaman} (POST)
     * @param string $noPeminjamanEncoded Nomor peminjaman yang akan dihapus.
     */
    public function hapus_data_peminjaman($noPeminjamanEncoded)
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Mengambil data peminjaman untuk memastikan keberadaan
        $peminjaman = $this->mPeminjaman->find($noPeminjamanEncoded);

        // Memeriksa apakah data peminjaman ditemukan
        if (!$peminjaman) {
            session()->setFlashdata('error', 'Data peminjaman tidak ditemukan!');
            return redirect()->back();
        }

        // Memanggil method hapusDataPeminjaman dari model untuk soft delete
        // ID peminjaman harus sesuai dengan primary key di model (no_peminjaman)
        if ($this->mPeminjaman->hapusDataPeminjaman(['no_peminjaman' => $peminjaman['no_peminjaman']])) {
            session()->setFlashdata('success', 'Data peminjaman berhasil dihapus!');
            return redirect()->to(base_url('admin/peminjaman/data-transaksi'));
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data peminjaman. Periksa log error.');
            return redirect()->back();
        }
    }

    /**
     * Memproses pengembalian buku.
     * Mengubah status peminjaman menjadi 'dikembalikan' dan menambah stok buku.
     * URL: /admin/peminjaman/proses-pengembalian/{no_peminjaman} (GET)
     * @param string $noPeminjamanEncoded Nomor peminjaman yang akan diproses pengembaliannya.
     */
    public function proses_pengembalian($noPeminjamanEncoded)
    {
        // Pengecekan sesi login admin
        if (session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Mengambil data peminjaman berdasarkan nomor peminjaman
        $peminjaman = $this->mPeminjaman->find($noPeminjamanEncoded); 

        // Memeriksa apakah data peminjaman ditemukan
        if (!$peminjaman) {
            session()->setFlashdata('error', 'Data peminjaman tidak ditemukan!');
            return redirect()->back();
        }

        // Menyiapkan data untuk update status peminjaman
        $dataUpdate = [
            'status_peminjaman' => 'dikembalikan', // Mengubah status menjadi dikembalikan
            'tanggal_kembali_aktual' => date('Y-m-d H:i:s'), // Mencatat tanggal pengembalian aktual
            'updated_at' => date('Y-m-d H:i:s'), // Memperbarui timestamp
        ];

        // Memanggil method updateDataPeminjaman dari model untuk update status
        if ($this->mPeminjaman->updateDataPeminjaman($peminjaman['no_peminjaman'], $dataUpdate)) {
            // Jika berhasil update status, tambahkan stok buku kembali
            $buku = $this->mBuku->find($peminjaman['id_buku']);
            if ($buku) {
                $this->mBuku->update($buku['id_buku'], ['stok_buku' => $buku['stok_buku'] + 1]);
            }
            $this->session->setFlashdata('success', 'Buku berhasil dikembalikan!');
            return redirect()->to(base_url('admin/peminjaman/data-transaksi')); // Redirect ke daftar transaksi
        } else {
            $this->session->setFlashdata('error', 'Gagal memproses pengembalian buku. Periksa log error.');
            return redirect()->back(); // Kembali ke halaman sebelumnya
        }
    }
}