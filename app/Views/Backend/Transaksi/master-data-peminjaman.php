<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Transaksi</li>
            <li class="active">Master Data Peminjaman</li>
        </ol>
    </div><div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Master Data Peminjaman</h1>
        </div>
    </div><div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Daftar Transaksi Peminjaman
                    <a href="<?= base_url('admin/peminjaman/step-1'); ?>" class="btn btn-primary btn-sm pull-right">
                        <span class="glyphicon glyphicon-plus"></span> Input Peminjaman Baru
                    </a>
                </div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= session()->getFlashdata('success'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= session()->getFlashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="no_peminjaman" data-sort-order="desc">
                        <thead>
                            <tr>
                                <th data-field="no_peminjaman" data-sortable="true">No. Peminjaman</th>
                                <th data-field="nama_anggota" data-sortable="true">Nama Anggota</th>
                                <th data-field="nama_admin" data-sortable="true">Admin Bertugas</th>
                                <th data-field="tgl_pinjam" data-sortable="true">Tgl. Pinjam</th>
                                <th data-field="status_peminjaman" data-sortable="true">Status Peminjaman</th>
                                <th data-field="status_ambil_buku" data-sortable="true">Status Ambil Buku</th>
                                <th data-field="total_pinjam" data-sortable="true">Jumlah Buku</th>
                                <th data-field="denda" data-sortable="true">Denda (Jika ada)</th>
                                <th data-field="aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data_peminjaman)) : ?>
                                <?php foreach ($data_peminjaman as $peminjaman) : ?>
                                    <tr>
                                        <td><?= esc($peminjaman['no_peminjaman']); ?></td>
                                        <td><?= esc($peminjaman['nama_anggota'] ?? '-'); ?></td>
                                        <td><?= esc($peminjaman['nama_admin'] ?? '-'); ?></td>
                                        <td><?= esc($peminjaman['tgl_pinjam']); ?></td>
                                        <td><?= esc($peminjaman['status_peminjaman'] ?? '-'); ?></td>
                                        <td><?= esc($peminjaman['status_ambil_buku'] ?? '-'); ?></td>
                                        <td><?= esc($peminjaman['total_pinjam'] ?? '0'); ?></td>
                                        <td>
                                            <?php
                                            // Denda dihitung di model saat mengambil data pinjaman aktif
                                            // Namun, di master-data-peminjaman ini kita menampilkan semua transaksi (termasuk yang sudah dikembalikan)
                                            // Jadi denda hanya muncul untuk status 'dipinjam' dan jika ada keterlambatan.
                                            // Untuk denda pada pengembalian, akan dihitung real-time di halaman pengembalian.
                                            // Di sini, kita asumsikan denda dihitung di M_Peminjaman::getAllPeminjamanJoin() jika statusnya 'dipinjam'
                                            // Jika tidak ada kolom denda di hasil join ini, biarkan 0
                                            $denda = isset($peminjaman['denda']) ? $peminjaman['denda'] : 0;
                                            echo 'Rp ' . number_format($denda, 0, ',', '.');
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (($peminjaman['status_peminjaman'] ?? '') == 'dipinjam') : ?>
                                                <a href="<?= base_url('admin/peminjaman/pengembalian-buku/' . esc($peminjaman['no_peminjaman'])); ?>" class="btn btn-warning btn-sm" title="Proses Pengembalian">
                                                    Pengembalian
                                                </a>
                                            <?php else : ?>
                                                <span class="label label-success">Selesai</span>
                                            <?php endif; ?>
                                            
                                            <form action="<?= base_url('admin/peminjaman/hapus-data-peminjaman/' . esc($peminjaman['no_peminjaman'])); ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data peminjaman ini?');">
                                                <?= csrf_field(); ?>
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data peminjaman yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div></div>