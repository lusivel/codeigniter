<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Anggota extends Model
{
    protected $table = 'tbl_anggota'; // Nama tabel anggota

    public function getDataAnggota($where = false)
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        if ($where !== false) {
            $builder->where($where);
        }
        $builder->orderBy('nama_anggota', 'ASC');
        $query = $builder->get();
        return $query;
    }

    public function saveDataAnggota($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function updateDataAnggota($data, $where)
    {
        $builder = $this->db->table($this->table);
        $builder->where($where);
        return $builder->update($data);
    }

    public function autoNumber()
    {
        $builder = $this->db->table($this->table);
        $builder->select("id_anggota");
        $builder->orderBy("id_anggota", "DESC");
        $builder->limit(1);
        $query = $builder->get();
        return $query;
    }
}
