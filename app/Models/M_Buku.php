<?php namespace App\Models;

use CodeIgniter\Model;

class M_Buku extends Model
{
    protected $table      = 'tbl_buku';
    protected $primaryKey = 'id_buku';

    protected $useAutoIncrement = false;
    protected $returnType     = 'array';
    
    // 1. Fitur soft delete otomatis DIMATIKAN untuk menghindari error lingkungan
    protected $useSoftDeletes = false;
    protected $deletedField   = 'is_delete_buku';

    protected $allowedFields = [
        'id_buku', 'cover_buku', 'judul_buku', 'pengarang', 'penerbit',
        'tahun_terbit', 'jumlah_eksemplar', 'id_kategori', 'id_rak',
        'keterangan', 'e_book', 'is_delete_buku',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function autoNumber()
    {
        // Karena useSoftDeletes = false, kita tidak bisa memakai withDeleted() lagi.
        // Penomoran akan tetap aman karena ID lama tidak akan terpakai lagi.
        $builder = $this->builder();
        $builder->select($this->primaryKey);
        $builder->orderBy($this->primaryKey, "DESC");
        $builder->limit(1);
        return $builder->get();
    }

    /**
     * 2. Mengambil daftar semua buku yang aktif secara manual menggunakan Query Builder.
     */
    public function getActiveBooks()
    {
        return $this->builder()
                    ->where('is_delete_buku', '0') // Filter manual untuk data aktif
                    ->orderBy('judul_buku', 'ASC')
                    ->get()
                    ->getResultArray();
    }
}