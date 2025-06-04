<?php namespace App\Models;

use CodeIgniter\Model;

class M_Kategori extends Model
{
    protected $table      = 'tbl_kategori'; // Nama tabel di database
    protected $primaryKey = 'id_kategori';  // Primary key tabel
    
    // Properti Model dasar CodeIgniter 4
    protected $useAutoIncrement = false; // ID kategori tidak auto-increment (misal: KTG001)
    protected $returnType     = 'array'; // Mengembalikan hasil dalam bentuk array
    protected $useSoftDeletes = true;  // Mengaktifkan soft delete bawaan CI4 Model
    protected $deletedField   = 'is_delete_kategori'; // Kolom di tabel untuk menandai soft delete (value 0/1)

    // Field yang diizinkan untuk diisi pada tabel tbl_kategori
    // SANGAT PENTING: SESUAIKAN DENGAN KOLOM REAL DI DATABASE MASTER
    protected $allowedFields = [
        'id_kategori', 'nama_kategori',
        'is_delete_kategori',
        'created_at', 'updated_at'
    ];

    // Pengaturan Timestamp untuk created_at dan updated_at
    protected $useTimestamps = true; //
    protected $dateFormat    = 'datetime'; // Pastikan format tanggal di DB sesuai (datetime/date/int)
    protected $createdField  = 'created_at'; //
    protected $updatedField  = 'updated_at'; //

    // Pengaturan Validasi (Opsional, bisa juga di Controller)
    protected $validationRules    = []; //
    protected $validationMessages = []; //
    protected $skipValidation     = false; //

    // Callbacks (Opsional)
    protected $allowCallbacks = true; //
    protected $beforeInsert   = []; //
    protected $afterInsert    = []; //
    protected $beforeUpdate   = []; //
    protected $afterUpdate    = []; //
    protected $beforeFind     = []; //
    protected $afterFind      = []; //
    protected $beforeDelete   = []; //
    protected $afterDelete    = [];

    /**
     * Mengambil ID kategori terakhir untuk auto-generate ID string (KTGxxx).
     * @return object Query Result object.
     */
    public function autoNumber()
    {
        // Menggunakan withDeleted(true) yang dipanggil dari instance Model ($this->)
        // untuk mengambil ID terakhir termasuk yang sudah di-soft delete
        // agar penomoran tidak bentrok jika ada id_kategori yang sama dengan yang dihapus.
        $builder = $this->withDeleted(true)->builder();
        $builder->select($this->primaryKey);
        $builder->orderBy($this->primaryKey, "DESC");
        $builder->limit(1);
        return $builder->get(); // Mengembalikan objek Query Result
    }

    /**
     * Mengambil daftar semua kategori yang aktif (is_delete_kategori = '0').
     *
     * @return array Array of active kategori data.
     */
    public function getActiveKategori()
    {
        // Menggunakan builder() yang sudah terkait dengan tabel ini
        // dan secara eksplisit memfilter is_delete_kategori = 0
        return $this->builder()
                    ->where('is_delete_kategori', 0) // Filter aktif (integer 0 untuk TINYINT)
                    ->orderBy('nama_kategori', 'ASC')
                    ->get()
                    ->getResultArray();
    }
}