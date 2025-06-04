<?php namespace App\Models;

use CodeIgniter\Model;

class M_Peminjaman extends Model 
{ 
    // --- Properti untuk Tabel Utama Peminjaman (tbl_peminjaman) ---
    protected $table = 'tbl_peminjaman'; 
    protected $primaryKey = 'no_peminjaman'; 
    protected $useAutoIncrement = false; 
    protected $returnType = 'array'; 

    // Field yang diizinkan untuk diisi pada tabel tbl_peminjaman
    // SANGAT PENTING: SESUAIKAN DENGAN KOLOM REAL DI DATABASE MASTER
    protected $allowedFields = [
        'no_peminjaman', 
        'id_buku', 
        'id_anggota', 
        'id_admin', 
        'tgl_pinjam', 
        'tanggal_kembali_estimasi', 
        'tanggal_kembali_aktual', 
        'status_peminjaman', 
        'status_ambil_buku', 
        'total_pinjam', 
        'qr_code', 
        'is_delete_peminjaman', 
        'created_at', 
        'updated_at' 
    ];

    // Pengaturan Timestamp untuk created_at dan updated_at
    protected $useTimestamps = true; 
    protected $createdField  = 'created_at'; 
    protected $updatedField  = 'updated_at'; 
    protected $dateFormat    = 'datetime'; 

    // Pengaturan Soft Deletes
    protected $useSoftDeletes = true; 
    protected $deletedField  = 'is_delete_peminjaman'; 

    // --- Properti untuk Tabel Temporary Peminjaman (tbl_temp_peminjaman) ---
    protected $tableTmp = 'tbl_temp_peminjaman'; 
    protected $primaryKeyTmp = 'id_temp_peminjaman'; 
    protected $allowedFieldsTmp = ['id_anggota', 'id_buku', 'id_admin', 'jumlah'];
    
    // --- Properti untuk Tabel Detail Peminjaman (tbl_detail_peminjaman) ---
    protected $tableDetail = 'tbl_detail_peminjaman'; 
    protected $primaryKeyDetail = 'id_detail_peminjaman'; 
    protected $allowedFieldsDetail = ['no_peminjaman', 'id_buku', 'jumlah', 'denda_per_buku', 'status_pinjam', 'perpanjangan', 'tgl_kembali']; 

    // --- Konstanta untuk Tarif Denda per Hari ---
    const DENDA_PER_HARI = 1000; // Contoh: Rp 1000 per hari, Master bisa sesuaikan

    /**
     * Mengambil data peminjaman dari tabel tbl_peminjaman berdasarkan kondisi.
     * Termasuk filter soft delete.
     *
     * @param array|false $where Kondisi WHERE (opsional).
     * @return array Array of loan data.
     */
    public function getDataPeminjaman($where = false) 
    { 
        $builder = $this->db->table($this->table); 
        $builder->select('*'); 
        if ($where !== false) { 
            $builder->where($where); 
        } 
        $builder->where($this->deletedField, '0'); 
        $builder->orderBy($this->primaryKey, 'DESC'); 
        return $builder->get()->getResultArray(); 
    } 
      
    /**
     * Mengambil data peminjaman utama dengan join ke tabel anggota dan admin.
     * Termasuk filter soft delete.
     *
     * @param array|false $where Kondisi WHERE (opsional).
     * @return array Array of joined loan data.
     */
    public function getDataPeminjamanJoin($where = false) 
    { 
        $builder = $this->db->table($this->table); 
        $builder->select('tbl_peminjaman.*, tbl_anggota.nama_anggota, tbl_admin.nama_admin'); 
        $builder->join('tbl_anggota', 'tbl_anggota.id_anggota = tbl_peminjaman.id_anggota', 'LEFT'); 
        $builder->join('tbl_admin', 'tbl_admin.id_admin = tbl_peminjaman.id_admin', 'LEFT'); 
        if ($where !== false) { 
            $builder->where($where); 
        } 
        $builder->where('tbl_peminjaman.' . $this->deletedField, '0'); 
        $builder->orderBy('tbl_peminjaman.no_peminjaman', 'DESC'); 
        return $builder->get()->getResultArray(); 
    } 

    /**
     * Mengambil semua data peminjaman yang aktif (belum dihapus dan belum dikembalikan)
     * dengan join ke tabel anggota dan admin.
     *
     * @return array Array of active loan data.
     */
    public function getAllPeminjamanJoin() 
    {
        return $this->db->table($this->table) 
                         ->select('tbl_peminjaman.*, tbl_anggota.nama_anggota, tbl_admin.nama_admin') 
                         ->join('tbl_anggota', 'tbl_anggota.id_anggota = tbl_peminjaman.id_anggota', 'LEFT') 
                         ->join('tbl_admin', 'tbl_admin.id_admin = tbl_peminjaman.id_admin', 'LEFT') 
                         ->where('tbl_peminjaman.' . $this->deletedField, '0') // Hanya data yang belum dihapus
                         ->where('tbl_peminjaman.status_peminjaman !=', 'dikembalikan') // Hanya yang belum dikembalikan
                         ->orderBy('tbl_peminjaman.created_at', 'DESC') 
                         ->get()
                         ->getResultArray();
    }

    /**
     * Menyimpan data peminjaman baru ke tabel tbl_peminjaman.
     * @param array $data Data yang akan disimpan.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function saveDataPeminjaman($data) 
    { 
        return $this->insert($data); 
    }

    /**
     * Memperbarui data peminjaman di tabel tbl_peminjaman.
     * @param string $id Primary key (no_peminjaman) dari baris yang akan diupdate.
     * @param array $data Data yang akan diperbarui.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateDataPeminjaman($id, $data) 
    { 
        return $this->update($id, $data); 
    } 
    
    /**
     * Melakukan soft delete pada data peminjaman (mengubah is_delete_peminjaman menjadi '1').
     * @param array $where Kondisi WHERE untuk menentukan baris yang akan di-soft delete.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function hapusDataPeminjaman($where)
    {
        $data = [$this->deletedField => '1', 'updated_at' => date('Y-m-d H:i:s')];
        return $this->db->table($this->table)->where($where)->update($data);
    }

    /**
     * Mengambil data peminjaman berdasarkan Primary Key (no_peminjaman).
     * @param string $noPeminjaman Nomor peminjaman yang dicari.
     * @return array|null Data peminjaman atau null jika tidak ditemukan.
     */
    public function getPeminjamanById($noPeminjaman)
    {
        return $this->where($this->primaryKey, $noPeminjaman)->first();
    }

    /**
     * Mengambil nomor peminjaman terakhir untuk auto-generate ID.
     * @return object Query Result object.
     */
    public function autoNumberPeminjaman() 
    { 
        return $this->db->table($this->table)
                         ->select($this->primaryKey)
                         ->orderBy($this->primaryKey, 'DESC')
                         ->limit(1)
                         ->get(); 
    }

    /**
     * Memeriksa apakah seorang anggota memiliki pinjaman yang masih aktif ('dipinjam').
     * @param string $idAnggota ID anggota yang akan diperiksa.
     * @return bool True jika memiliki pinjaman aktif, false jika tidak.
     */
    public function hasActiveLoans($idAnggota)
    {
        $count = $this->where('id_anggota', $idAnggota)
                      ->where('status_peminjaman', 'dipinjam')
                      ->countAllResults();

        return $count > 0;
    }

    /**
     * Mengambil detail semua pinjaman aktif ('dipinjam') untuk seorang anggota.
     * Termasuk detail buku yang dipinjam, perhitungan keterlambatan dan denda.
     * @param string $idAnggota ID anggota yang akan diperiksa.
     * @return array Array berisi detail pinjaman aktif.
     */
    public function getActiveLoansByAnggotaId($idAnggota)
    {
        $loans = $this->db->table($this->table)
                          ->select('tbl_peminjaman.no_peminjaman, tbl_peminjaman.tgl_pinjam, tbl_peminjaman.tanggal_kembali_estimasi, tbl_buku.judul_buku, tbl_buku.id_buku')
                          ->join('tbl_buku', 'tbl_buku.id_buku = tbl_peminjaman.id_buku', 'LEFT')
                          ->where('tbl_peminjaman.id_anggota', $idAnggota)
                          ->where('tbl_peminjaman.status_peminjaman', 'dipinjam')
                          ->where('tbl_peminjaman.' . $this->deletedField, '0') // Memastikan pinjaman tidak di-soft delete
                          ->orderBy('tbl_peminjaman.tgl_pinjam', 'ASC')
                          ->get()
                          ->getResultArray();

        $today = new \DateTime(); 
        foreach ($loans as &$loan) { 
            $estimasiKembali = new \DateTime($loan['tanggal_kembali_estimasi']);

            $loan['selisih_hari_terlambat'] = 0;
            $loan['denda'] = 0;
            $loan['status_keterlambatan'] = 'Tepat Waktu';

            if ($today > $estimasiKembali) {
                $interval = $today->diff($estimasiKembali);
                $terlambatHari = $interval->days;
                
                $loan['selisih_hari_terlambat'] = $terlambatHari;
                $loan['denda'] = $terlambatHari * self::DENDA_PER_HARI; 
                $loan['status_keterlambatan'] = 'Terlambat ' . $terlambatHari . ' hari';
            }
        }
        return $loans;
    }

    // --- Metode untuk Tabel Temporary Peminjaman (tbl_temp_peminjaman) ---

    /**
     * Mengambil data dari tabel keranjang sementara (tbl_temp_peminjaman).
     *
     * @param array|false $where Kondisi WHERE (opsional).
     * @return array Array of temporary loan data.
     */
    public function getDataTemp($where = false) 
    { 
        $builder = $this->db->table($this->tableTmp); 
        $builder->select('*'); 
        if ($where !== false) { 
            $builder->where($where); 
        } 
        return $builder->get()->getResultArray(); 
    } 
      
    /**
     * Mengambil data dari tabel keranjang sementara dengan join ke tabel buku.
     *
     * @param array|false $where Kondisi WHERE (opsional).
     * @return array Array of temporary loan data with book details.
     */
    public function getDataTempJoin($where = false) 
    { 
        $builder = $this->db->table($this->tableTmp); 
        $builder->select('tbl_temp_peminjaman.*, tbl_buku.judul_buku, tbl_buku.id_buku AS buku_id_asli'); 
        $builder->join('tbl_buku', 'tbl_buku.id_buku = tbl_temp_peminjaman.id_buku', 'LEFT'); 
        if ($where !== false) { 
            $builder->where($where); 
        } 
        return $builder->get()->getResultArray(); 
    } 

    /**
     * Menyimpan data ke tabel keranjang sementara (tbl_temp_peminjaman).
     * @param array $data Data yang akan disimpan.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function saveDataTemp($data) 
    { 
        return $this->db->table($this->tableTmp)->insert($data); 
    } 
    
    /**
     * Menghapus data dari tabel keranjang sementara (tbl_temp_peminjaman).
     * @param array $where Kondisi WHERE untuk menentukan baris yang akan dihapus.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function hapusDataTemp($where) 
    { 
        return $this->db->table($this->tableTmp)->delete($where); 
    } 

    // --- Metode untuk Tabel Detail Peminjaman (tbl_detail_peminjaman) ---

    /**
     * Mengambil data dari tabel detail peminjaman (tbl_detail_peminjaman).
     *
     * @param array|false $where Kondisi WHERE (opsional).
     * @return array Array of loan detail data.
     */
    public function getDataDetailPeminjaman($where = false)
    {
        $builder = $this->db->table($this->tableDetail);
        $builder->select('*');
        if ($where !== false) {
            $builder->where($where);
        }
        return $builder->get()->getResultArray();
    }

    /**
     * Menyimpan data ke tabel detail peminjaman (tbl_detail_peminjaman).
     * @param array $data Data yang akan disimpan.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function saveDataDetail($data) 
    { 
        return $this->db->table($this->tableDetail)->insert($data); 
    } 
    
    /**
     * Memperbarui data di tabel detail peminjaman (tbl_detail_peminjaman).
     * @param string $id Primary key dari baris yang akan diupdate.
     * @param array $data Data yang akan diperbarui.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateDataDetail($id, $data) 
    { 
        return $this->update($id, $data); 
    } 
    
    /**
     * Menghapus data dari tabel detail peminjaman (tbl_detail_peminjaman).
     * @param array $where Kondisi WHERE untuk menentukan baris yang akan dihapus.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function hapusDataDetail($where)
    {
        return $this->db->table($this->tableDetail)->delete($where);
    }
}
