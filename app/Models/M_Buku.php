<?php namespace App\Models;

use CodeIgniter\Model;

class M_Buku extends Model
{
    protected $table      = 'tbl_buku';
    protected $primaryKey = 'id_buku';  
    
    protected $useAutoIncrement = false; // Karena id_buku Anda generate manual "BK0001"
    protected $returnType     = 'array'; // Atau 'object'
    protected $useSoftDeletes = true; // <-- Ini yang mengaktifkan fitur soft delete untuk tbl_buku
    protected $deletedField   = 'is_delete_buku'; // <-- Ini menunjuk ke kolom 'is_delete_buku' Anda di tbl_buku

    protected $allowedFields = [
        'id_buku', 'judul_buku', 'pengarang', 'penerbit', 'tahun_terbit',
        'jumlah_eksemplar', 'id_kategori', 'id_rak', 'keterangan',
        'cover_buku', 'e_book', 'stok_buku'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Mengambil ID buku terakhir untuk auto-generate ID string (BKxxxx).
     * Hanya mempertimbangkan buku yang TIDAK di-soft delete untuk nomor urut.
     * @return array|null Sebuah array asosiatif dengan 'id_buku' terakhir atau null jika tidak ada.
     */
    public function autoNumber()
    {
        // selectMax() sudah cukup. Karena `$useSoftDeletes` true, 
        // secara default hanya record dengan `is_delete_buku = 0` yang akan dipertimbangkan.
        return $this->selectMax($this->primaryKey, 'id_buku')
                    ->orderBy($this->primaryKey, 'DESC')
                    ->first(); 
    }

    /**
     * Mengambil daftar semua buku yang aktif (is_delete_buku = '0') dengan join kategori dan rak.
     * @return array Array of active buku data.
     */
    public function getActiveBuku()
    {
        // Pastikan nama kolom dan tabel sudah benar dan konsisten.
        // `findAll()` akan secara otomatis menambahkan `WHERE is_delete_buku = 0` karena `$useSoftDeletes = true`.
        // Jadi, `->where('tbl_buku.is_delete_buku', 0)` di sini sebenarnya redundan namun tidak salah.
        return $this->select('tbl_buku.*, tbl_kategori.nama_kategori, tbl_rak.nama_rak')
                    ->join('tbl_kategori', 'tbl_kategori.id_kategori = tbl_buku.id_kategori', 'left')
                    ->join('tbl_rak', 'tbl_rak.id_rak = tbl_buku.id_rak', 'left')
                    // Kondisi where ini secara eksplisit merujuk ke tabel terkait
                    ->where('tbl_buku.is_delete_buku', 0) // Status buku
                    ->where('tbl_kategori.is_delete_kategori', 0) // Status kategori
                    ->where('tbl_rak.is_delete_rak', 0) // Status rak
                    ->orderBy('tbl_buku.judul_buku', 'ASC')
                    ->findAll(); 
    }
}