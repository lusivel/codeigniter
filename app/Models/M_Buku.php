<?php namespace App\Models;

use CodeIgniter\Model;

class M_Buku extends Model
{
    protected $table      = 'tbl_buku'; // Nama tabel di database
    protected $primaryKey = 'id_buku';  // Primary key tabel
    
    // Properti Model dasar CodeIgniter 4
    protected $useAutoIncrement = false; // ID buku tidak auto-increment (misal: BK0001)
    protected $returnType     = 'array'; // Mengembalikan hasil dalam bentuk array
    protected $useSoftDeletes = false; // Mengatur soft delete secara manual di method

    // Field yang diizinkan untuk diisi pada tabel tbl_buku
    // SANGAT PENTING: SESUAIKAN DENGAN KOLOM REAL DI DATABASE MASTER
    protected $allowedFields = [
        'id_buku', 'judul_buku', 'pengarang', 'penerbit', 'tahun_terbit', // 'tahun_terbit' sesuai DB
        'jumlah_eksemplar', 'id_kategori', 'id_rak', 'keterangan',
        'cover_buku', 'e_book', 'is_delete_buku', 'stok_buku', // Pastikan 'stok_buku' ada di tabel
        'created_at', 'updated_at' 
    ]; 

    // Pengaturan Timestamp untuk created_at dan updated_at
    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime'; 
    protected $createdField  = 'created_at'; 
    protected $updatedField  = 'updated_at'; 

    // Pengaturan Validasi (Opsional, bisa juga di Controller)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks (Opsional) - Dihapus jika tidak ada logika callback
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = []; 
    // protected $beforeFind     = [];
    // protected $afterFind      = []; 
    // protected $beforeDelete   = [];
    // protected $afterDelete    = []; 

    /**
     * Mengambil semua data buku yang aktif (is_delete_buku = '0')
     * dan melakukan JOIN ke tabel kategori dan rak untuk mendapatkan nama.
     * @return array Array of book data.
     */
    public function get_all_active_buku()
    {
        return $this->db->table($this->table)
                        ->select('tbl_buku.*, tbl_kategori.nama_kategori, tbl_rak.nama_rak') 
                        ->join('tbl_kategori', 'tbl_kategori.id_kategori = tbl_buku.id_kategori', 'LEFT')
                        ->join('tbl_rak', 'tbl_rak.id_rak = tbl_buku.id_rak', 'LEFT')
                        ->where('tbl_buku.is_delete_buku', '0') 
                        ->orderBy('tbl_buku.judul_buku', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Mengambil data buku berdasarkan ID.
     * Mengambil SEMUA KOLOM yang dibutuhkan, termasuk stok_buku.
     * @param string $id_buku ID dari buku.
     * @return array|null Book data or null if not found.
     */
    public function get_buku_by_id($id_buku)
    {
        return $this->select('*')->find($id_buku); 
    }

    /**
     * Mengambil daftar buku yang tersedia untuk dipinjam.
     * Kriteria: jumlah_eksemplar > 0 dan belum dihapus.
     * @return array Array of available book data.
     */
    public function getAvailableBooks() 
    {
        return $this->db->table($this->table)
                        ->select('tbl_buku.*, tbl_kategori.nama_kategori, tbl_rak.nama_rak')
                        ->join('tbl_kategori', 'tbl_kategori.id_kategori = tbl_buku.id_kategori', 'LEFT')
                        ->join('tbl_rak', 'tbl_rak.id_rak = tbl_buku.id_rak', 'LEFT')
                        ->where('tbl_buku.is_delete_buku', '0') 
                        ->where('tbl_buku.jumlah_eksemplar >', 0) 
                        ->orderBy('tbl_buku.judul_buku', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Mengambil data buku yang aktif dan tersedia untuk dipinjam berdasarkan ID.
     * @param string $id_buku ID dari buku.
     * @return array|null Book data or null if not found.
     */
    public function getActiveAndAvailableBookById($id_buku) 
    {
        return $this->where('id_buku', $id_buku)
                    ->where('is_delete_buku', '0') 
                    ->where('jumlah_eksemplar >', 0) 
                    ->first(); 
    }
}