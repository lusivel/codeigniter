<?php namespace App\Models;

use CodeIgniter\Model;

class M_Kategori extends Model
{
    protected $table      = 'tbl_kategori';
    protected $primaryKey = 'id_kategori';  

    protected $useAutoIncrement = false; 
    protected $returnType     = 'array'; 
    protected $useSoftDeletes = true;  
    protected $deletedField   = 'is_delete_kategori'; 

    // SANGAT PENTING: HAPUS 'is_delete_kategori', 'created_at', 'updated_at' dari allowedFields
    protected $allowedFields = [
        'id_kategori', 'nama_kategori'
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

    public function autoNumber()
    {
        return $this->selectMax($this->primaryKey, 'id_kategori')
                    ->orderBy($this->primaryKey, "DESC")
                    ->first(); 
    }

    public function getActiveKategori()
    {
        // Karena $useSoftDeletes = true, findAll() secara otomatis hanya mengambil yang aktif.
        // Tidak perlu lagi orWhere('is_delete_kategori IS NULL')
        return $this->orderBy('nama_kategori', 'ASC')
                    ->findAll(); 
    }

    // TIDAK PERLU override method delete() secara manual
    // Model akan mengurus soft delete secara otomatis jika $allowedFields sudah benar
    // public function isNamaKategoriExists(...) { ... } // Pindahkan ini ke tempat lain jika Anda memilikinya
}