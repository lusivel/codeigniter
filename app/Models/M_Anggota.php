<?php namespace App\Models;

use CodeIgniter\Model;

class M_Anggota extends Model
{
    protected $table      = 'tbl_anggota'; // Nama tabel di database
    protected $primaryKey = 'id_anggota';  // Primary key tabel
    
    // Properti Model dasar CodeIgniter 4
    protected $useAutoIncrement = false; // ID anggota tidak auto-increment (misal: ANG001), ini penting
    protected $returnType     = 'array'; // Mengembalikan hasil dalam bentuk array
    protected $useSoftDeletes = false;  // Mengaktifkan soft delete bawaan CI4 Model
    protected $deletedField   = 'is_delete_anggota'; // Kolom di tabel untuk menandai soft delete (value 0/1)

    // Field yang diizinkan untuk diisi pada tabel tbl_anggota
    // SANGAT PENTING: SESUAIKAN DENGAN KOLOM REAL DI DATABASE MASTER
    protected $allowedFields = [
        'id_anggota', 'nama_anggota', 'jenis_kelamin', 'no_tlp', 'alamat',
        'email', 'password_anggota',
        'is_delete_anggota',
        'created_at', 'updated_at'
    ];

    // Pengaturan Timestamp untuk created_at dan updated_at
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // Pastikan format tanggal di DB sesuai (datetime/date/int)
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Pengaturan Validasi (Opsional, bisa juga di Controller)
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Callbacks (Opsional)
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
     * Mengambil ID anggota terakhir untuk auto-generate ID string (ANGxxx).
     * @return object Query Result object.
     */
    public function autoNumber()
    {
        // Menggunakan withDeleted(true) yang dipanggil dari instance Model ($this->)
        // untuk mengambil ID terakhir termasuk yang sudah di-soft delete
        // agar penomoran tidak bentrok jika ada id_anggota yang sama dengan yang dihapus.
        $builder = $this->withDeleted(true)->builder();
        $builder->select($this->primaryKey);
        $builder->orderBy($this->primaryKey, "DESC");
        $builder->limit(1);
        return $builder->get(); // Mengembalikan objek Query Result
    }

    /**
     * Mengambil daftar semua anggota yang aktif (is_delete_anggota = '0').
     * Ini adalah metode yang akan digunakan oleh controller untuk mendapatkan data aktif.
     *
     * @return array Array of active member data.
     */
    public function getActiveMembers()
    {
        // Menggunakan builder() yang sudah terkait dengan tabel ini
        // dan secara eksplisit memfilter is_delete_anggota = 0
        return $this->builder()
                    ->where('is_delete_anggota', '0') // Filter aktif
                    ->orderBy('nama_anggota', 'ASC')
                    ->get()
                    ->getResultArray();
    }
}