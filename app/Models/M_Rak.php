<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Rak extends Model
{
    protected $table = 'tbl_rak';

    public function getDataRak($where = false)
    {
        if ($where === false) {
            $builder = $this->db->table($this->table);
            $builder->select('*');
            $builder->orderBy('nama_rak', 'ASC');
            $query   = $builder->get();
            return $query;
        } else {
            $builder = $this->db->table($this->table);
            $builder->select('*');
            $builder->where($where);
            $builder->orderBy('nama_rak', 'ASC');
            $query   = $builder->get();
            return $query;
        }
    }

    public function saveDataRak($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function updateDataRak($data, $where)
    {
        $builder = $this->db->table($this->table);
        $builder->where($where);
        return $builder->update($data);
    }
}