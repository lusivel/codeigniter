<?php

namespace App\Controllers;

use App\Models\M_Anggota;

class Anggota extends BaseController // Pastikan BaseController Anda memuat helper 'session' dan 'validation'
{
    protected $mAnggota;
    protected $session;

    public function __construct()
    {
        $this->mAnggota = new M_Anggota();
        helper(['form', 'url', 'date']);
        $this->session = \Config\Services::session();
    }

    /**
     * Menampilkan form input data anggota baru.
     * URL: /anggota/input-data-anggota
     */
    public function input_data_anggota()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        $data['web_title'] = "Input Data Anggota";
        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterAnggota/input-anggota', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Menyimpan data anggota baru.
     * URL: /anggota/simpan-data-anggota (POST)
     */
    public function simpan_data_anggota()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // --- DEFINISI ATURAN VALIDASI ---
        $rules = [
            'nama_anggota' => [
                'label' => 'Nama Anggota',
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.',
                    'max_length' => '{field} maksimal {param} karakter.'
                ]
            ],
            'jenis_kelamin' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required|in_list[Laki-laki,Perempuan]', // Sesuai dengan form HTML
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'in_list' => '{field} harus Laki-laki atau Perempuan.'
                ]
            ],
            'no_tlp' => [
                'label' => 'No. Telepon',
                'rules' => 'required|numeric|max_length[13]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'numeric' => '{field} harus berupa angka.',
                    'max_length' => '{field} maksimal {param} digit.'
                ]
            ],
            'alamat' => [
                'label' => 'Alamat',
                'rules' => 'required|min_length[5]|max_length[100]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.',
                    'max_length' => '{field} maksimal {param} karakter.'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[tbl_anggota.email,is_delete_anggota,0]', // Cek unik hanya yang aktif
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'valid_email' => '{field} harus format email valid.',
                    'is_unique' => '{field} sudah digunakan.'
                ]
            ],
            'password_anggota' => [
                'label' => 'Password Anggota',
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong.',
                    'min_length' => '{field} minimal {param} karakter.'
                ]
            ]
        ];

        // --- JALANKAN VALIDASI ---
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mengambil data dari POST request
        $nama_anggota = $this->request->getPost('nama_anggota');
        $jenis_kelamin = $this->request->getPost('jenis_kelamin');
        $no_tlp = $this->request->getPost('no_tlp');
        $alamat = $this->request->getPost('alamat');
        $email = $this->request->getPost('email');
        $password_anggota = $this->request->getPost('password_anggota');

        // Generate ID anggota otomatis (sesuai logika Anda untuk ANGxxx)
        $hasilAutoNumber = $this->mAnggota->autoNumber()->getRowArray();
        $id_anggota = "ANG001";
        if ($hasilAutoNumber && !empty($hasilAutoNumber['id_anggota'])) {
            $kode   = $hasilAutoNumber['id_anggota'];
            $noUrut = (int)substr($kode, 3);
            $noUrut++;
            $id_anggota = "ANG" . sprintf("%03s", $noUrut);
        }

        // Menyiapkan data untuk disimpan
        $dataSimpan = [
            'id_anggota'        => $id_anggota,
            'nama_anggota'      => $nama_anggota,
            'jenis_kelamin'     => $jenis_kelamin,
            'no_tlp'            => $no_tlp,
            'alamat'            => $alamat,
            'email'             => $email,
            'password_anggota'  => password_hash($password_anggota, PASSWORD_DEFAULT),
            'is_delete_anggota' => 0, // Menggunakan integer 0 untuk TINYINT(1)
            // created_at dan updated_at akan diisi otomatis oleh Model karena useTimestamps = true
        ];

        // Memanggil method insert() bawaan Model CodeIgniter 4
        if ($this->mAnggota->insert($dataSimpan)) {
            $this->session->setFlashdata('success', 'Data Anggota Berhasil Ditambahkan!');
            return redirect()->to(base_url('anggota/master-data-anggota'));
        } else {
            $this->session->setFlashdata('error', 'Gagal menyimpan data anggota ke database. Periksa log error.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menampilkan master data anggota.
     * URL: /anggota/master-data-anggota
     */
    public function master_data_anggota()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }
        
        $uri          = service('uri');
        
        // MENGAMBIL SEMUA DATA ANGGOTA YANG AKTIF MENGGUNAKAN METODE getActiveMembers()
        $dataAnggota = $this->mAnggota->getActiveMembers();

        $data['pages']     = $uri->getSegment(2) ?? 'master-data-anggota';
        $data['data_user'] = $dataAnggota; // Mengirim data ke view dengan nama 'data_user'
        $data['web_title'] = "Master Data Anggota";

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterAnggota/master-data-anggota', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Menampilkan form edit data anggota.
     * URL: /anggota/edit-data-anggota/{id_anggota} (GET)
     */
    public function edit_data_anggota($id_anggota = null)
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Validasi ID anggota dari URL
        if (empty($id_anggota)) {
            $this->session->setFlashdata('error', 'ID Anggota tidak valid.');
            return redirect()->to(base_url('anggota/master-data-anggota'));
        }

        // Mengambil data anggota berdasarkan ID (find() sudah menghormati soft delete)
        $dataAnggota = $this->mAnggota->find($id_anggota);

        // Memeriksa apakah data anggota ditemukan
        if (!$dataAnggota) { // find() akan mengembalikan null jika tidak ditemukan atau terhapus soft delete
            $this->session->setFlashdata('error', 'Data anggota tidak ditemukan atau sudah dihapus!');
            return redirect()->to(base_url('anggota/master-data-anggota'));
        }
        
        $data['page']         = 'edit-data-anggota';
        $data['web_title']    = "Edit Data Anggota";
        $data['data_anggota'] = $dataAnggota;

        echo view('Backend/Template/Header', $data);
        echo view('Backend/Template/Sidebar', $data);
        echo view('Backend/MasterAnggota/edit-anggota', $data);
        echo view('Backend/Template/Footer', $data);
    }

    /**
     * Memperbarui data anggota.
     * URL: /anggota/update-data-anggota (POST)
     */
    public function update_data_anggota()
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Ambil ID dari form hidden
        $idUpdate = $this->request->getPost('id_anggota');

        // Validasi ID anggota
        if (empty($idUpdate)) {
            $this->session->setFlashdata('error', 'ID Anggota tidak valid untuk update.');
            return redirect()->to(base_url('anggota/master-data-anggota'));
        }
        
        $nama          = $this->request->getPost('nama_anggota');
        $jenis_kelamin = $this->request->getPost('jenis_kelamin');
        $no_tlp        = $this->request->getPost('no_tlp');
        $alamat        = $this->request->getPost('alamat');
        $email         = $this->request->getPost('email');
        $password_baru = $this->request->getPost('password_anggota');

        // --- DEFINISI ATURAN VALIDASI UNTUK UPDATE ---
        $rules = [
            'nama_anggota' => 'required|min_length[3]|max_length[50]',
            'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'no_tlp' => 'required|numeric|max_length[13]',
            'alamat' => 'required|min_length[5]|max_length[100]',
            'email' => "required|valid_email|is_unique[tbl_anggota.email,id_anggota,{$idUpdate}]",
            'password_anggota' => 'permit_empty|min_length[6]',
        ];

        // --- JALANKAN VALIDASI ---
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Menyiapkan data untuk diperbarui
        $dataUpdate = [
            'nama_anggota'  => $nama,
            'jenis_kelamin' => $jenis_kelamin,
            'no_tlp'        => $no_tlp,
            'alamat'        => $alamat,
            'email'         => $email,
            // updated_at akan diisi otomatis oleh Model karena useTimestamps = true
        ];

        // Hash password baru jika diisi di form
        if (!empty($password_baru)) {
            $dataUpdate['password_anggota'] = password_hash($password_baru, PASSWORD_DEFAULT);
        }

        // Memanggil method update() bawaan Model CodeIgniter 4
        if ($this->mAnggota->update($idUpdate, $dataUpdate)) {
            $this->session->setFlashdata('success', 'Data Anggota Berhasil Diperbarui!');
            return redirect()->to(base_url('anggota/master-data-anggota'));
        } else {
            $this->session->setFlashdata('error', 'Gagal memperbarui data anggota. Periksa log error.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Menghapus data anggota (soft delete).
     * URL: /anggota/hapus-data-anggota/{id_anggota} (POST)
     */
    public function hapus_data_anggota($id_anggota = null)
    {
        // Pengecekan sesi login admin
        if (!$this->session->get('ses_id') || !$this->session->get('ses_user') || !$this->session->get('ses_level')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('admin/login-admin'));
        }

        // Validasi ID anggota dari URL
        if (empty($id_anggota)) {
            $this->session->setFlashdata('error', 'ID Anggota tidak valid untuk dihapus.');
            return redirect()->to(base_url('anggota/master-data-anggota'));
        }

        // Mengambil data anggota untuk memastikan keberadaan dan statusnya (aktif/dihapus)
        // Gunakan withDeleted() untuk mencari terlepas dari status soft delete
        $anggota = $this->mAnggota->withDeleted()->find($id_anggota); // Perbaikan: Gunakan withDeleted()

        // Memeriksa apakah data anggota ditemukan
        if (!$anggota) {
            $this->session->setFlashdata('error', 'Data anggota tidak ditemukan!'); // Pesan disederhanakan
            return redirect()->to(base_url('anggota/master-data-anggota'));
        }
        
        // Memeriksa apakah anggota sudah di-soft delete
        if (($anggota['is_delete_anggota'] ?? 0) == 1) { // Perbaikan: Gunakan 0/1 untuk TINYINT(1)
            $this->session->setFlashdata('error', 'Data anggota sudah dihapus!');
            return redirect()->to(base_url('anggota/master-data-anggota'));
        }

        // Memanggil method delete() dari model untuk melakukan soft delete
        // CodeIgniter Model akan otomatis memperbarui kolom 'is_delete_anggota' menjadi '1'
        if ($this->mAnggota->delete($id_anggota)) {
            $this->session->setFlashdata('success', 'Data Anggota Berhasil Dihapus!');
            return redirect()->to(base_url('anggota/master-data-anggota'));
        } else {
            $this->session->setFlashdata('error', 'Gagal menghapus data anggota. Periksa log error.');
            return redirect()->back();
        }
    }
}