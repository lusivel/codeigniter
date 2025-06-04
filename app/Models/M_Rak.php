<?php namespace App\Models;

use CodeIgniter\Model;

class M_Rak extends Model
{
    protected $table      = 'tbl_rak';
    protected $primaryKey = 'id_rak';
    
    protected $useAutoIncrement = false;
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField   = 'is_delete_rak';

    protected $allowedFields = [
        'id_rak', 'nama_rak',
        // 'lokasi_rak' dihapus karena tidak ada di DB
        'is_delete_rak',
        'created_at', 'updated_at'
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
     * Mengambil ID rak terakhir untuk auto-generate ID string (RAKxxx).
     * @return object Query Result object.
     */
    public function autoNumber()
    {
        $builder = $this->withDeleted(true)->builder();
        $builder->select($this->primaryKey);
        $builder->orderBy($this->primaryKey, "DESC");
        $builder->limit(1);
        return $builder->get();
    }

    /**
     * Mengambil daftar semua rak yang aktif (is_delete_rak = '0').
     *
     * @return array Array of active rak data.
     */
    public function getActiveRak()
    {
        return $this->builder()
                    ->where('is_delete_rak', 0)
                    ->orderBy('nama_rak', 'ASC')
                    ->get()
                    ->getResultArray();
    }
}