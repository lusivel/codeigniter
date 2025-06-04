<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah user sudah login di session
        // Asumsi session login Master disimpan di 'ses_id', 'ses_user', 'ses_level'
        if (!session()->has('ses_id') || session()->get('ses_id') == "") {
            session()->setFlashdata('error', 'Anda harus login untuk mengakses halaman ini!');
            // Redirect ke halaman login admin
            return redirect()->to(base_url('admin/login-admin'));
        }
        // Optional: Tambahkan logika cek level pengguna jika diperlukan
        // if (session()->get('ses_level') !== 'admin') {
        //     session()->setFlashdata('error', 'Anda tidak memiliki izin untuk mengakses halaman ini!');
        //     return redirect()->to(base_url('some-other-page')); // Arahkan ke halaman lain
        // }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed after the request (biasanya kosong untuk filter autentikasi)
    }
}