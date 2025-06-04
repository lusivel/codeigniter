<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Transaksi</li>
            <li class="active">Pengembalian Buku</li>
        </ol>
    </div><div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Daftar Buku yang Sedang Dipinjam
                </div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?= session()->getFlashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?= session()->getFlashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($loans_to_return)): ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No Peminjaman</th>
                                    <th>ID Anggota</th>
                                    <th>ID Buku</th>
                                    <th>Judul Buku</th> <th>Tanggal Pinjam</th>
                                    <th>Estimasi Kembali</th>
                                    <th>Keterlambatan</th> <th>Denda</th> <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $totalDendaAllLoans = 0; // Untuk total denda keseluruhan ?>
                                <?php foreach ($loans_to_return as $loan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($loan['no_peminjaman']); ?></td>
                                        <td><?= htmlspecialchars($loan['id_anggota']); ?></td>
                                        <td><?= htmlspecialchars($loan['id_buku']); ?></td>
                                        <td><?= htmlspecialchars($loan['judul_buku'] ?? 'Judul tidak tersedia'); ?></td>
                                        <td><?= htmlspecialchars($loan['tgl_pinjam']); ?></td>
                                        <td><?= htmlspecialchars($loan['tanggal_kembali_estimasi']); ?></td>
                                        <td><?= htmlspecialchars($loan['status_keterlambatan'] ?? '-'); ?></td> <td>Rp <?= number_format($loan['denda'] ?? 0, 0, ',', '.'); ?></td> <td>
                                            <a href="<?= base_url('admin/peminjaman/proses-pengembalian/' . htmlspecialchars($loan['no_peminjaman'])); ?>" class="btn btn-sm btn-success" onclick="return confirm('Yakin ingin mengembalikan buku ini?')" title="Kembalikan Buku">
                                                Kembalikan
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $totalDendaAllLoans += ($loan['denda'] ?? 0); ?>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-right">Total Denda Keseluruhan:</th>
                                        <th>Rp <?= number_format($totalDendaAllLoans, 0, ',', '.'); ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">Tidak ada buku yang sedang dipinjam.</div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div></div>