<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Admin extends Model
{
    protected $table      = 'tbl_admin'; // Pastikan nama tabel ini sesuai dengan yang Anda gunakan di database
    protected $primaryKey = 'id_admin'; // Deklarasikan primary key
    protected $useAutoIncrement = true; // Set true jika primary key adalah AUTO_INCREMENT

    protected $returnType     = 'array'; // Atau 'object'
    protected $useSoftDeletes = false; // Set true jika Anda menggunakan soft delete bawaan CI4 pada tabel ini

    // Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignment)
    protected $allowedFields = [
        'nama_admin',
        'username_admin',
        'password_admin',
        'akses_level',
        'is_delete_admin',
        'created_at',
        'updated_at'
    ];

    // Timestamp fields
    protected $useTimestamps = true; // Set true jika Anda menggunakan created_at dan updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Jika Anda menggunakan soft delete CI4

    // Aturan validasi (opsional, bisa juga diletakkan di Controller)
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Callbacks (opsional)
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function getDataAdmin($where = false)
    {
        // Menggunakan metode find() bawaan model jika hanya mencari berdasarkan primary key
        // atau findAll() untuk semua data
        if ($where === false) {
            // Mengambil semua data dengan orderBy dan filter is_delete_admin = 0
            // $builder = $this->db->table($this->table); // Tidak perlu builder jika pakai Model CI4
            // $builder->select('*');
            // $builder->orderBy('nama_admin', 'ASC');
            // $query   = $builder->get();
            // return $query;
            return $this->where('is_delete_admin', '0') // Filter aktif
                        ->orderBy('nama_admin', 'ASC')
                        ->findAll(); // Menggunakan findAll() bawaan CI4 Model
        } else {
            // Mengambil data berdasarkan kondisi WHERE
            // $builder = $this->db->table($this->table); // Tidak perlu builder jika pakai Model CI4
            // $builder->select('*');
            // $builder->where($where);
            // $builder->orderBy('nama_admin', 'ASC');
            // $query   = $builder->get();
            // return $query;
            return $this->where($where)
                        ->orderBy('nama_admin', 'ASC')
                        ->findAll(); // Menggunakan findAll() bawaan CI4 Model
        }
    }

    public function saveDataAdmin($data)
    {
        // Model CI4 memiliki metode insert() bawaan yang lebih sederhana
        // return $builder->insert($data);
        return $this->insert($data); // Menggunakan insert() bawaan CI4 Model
    }

    public function updateDataAdmin($data, $where)
    {
        // Model CI4 memiliki metode update() bawaan yang lebih sederhana
        // return $builder->update($data);
        return $this->where($where)->update(null, $data); // Menggunakan update() bawaan CI4 Model
    }

    // Metode autoNumber() dihapus karena id_admin sekarang AUTO_INCREMENT
    // dan dikelola oleh database.
}