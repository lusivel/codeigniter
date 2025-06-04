<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Transaksi</li>
            <li class="active">Peminjaman - Step 2</li>
        </ol>
    </div><div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Konfirmasi Peminjaman Buku</h3>
                    <hr />

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?= session()->getFlashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                           <div class="alert alert-success alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <?= session()->getFlashdata('success'); ?>
                           </div>
                    <?php endif; ?>

                    <?php if (!empty($data_active_loans)): // Tampilkan jika ada pinjaman aktif ?>
                        <div class="alert alert-warning" role="alert">
                            <h4 class="alert-heading">Perhatian!</h4>
                            <p>Anggota ini masih memiliki pinjaman buku yang belum dikembalikan.</p>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No Peminjaman</th>
                                        <th>Judul Buku</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Estimasi Kembali</th>
                                        <th>Keterlambatan</th>
                                        <th>Denda</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totalDenda = 0; ?>
                                    <?php foreach ($data_active_loans as $loan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($loan['no_peminjaman']); ?></td>
                                        <td><?= htmlspecialchars($loan['judul_buku'] ?? 'Judul tidak tersedia'); ?></td>
                                        <td><?= htmlspecialchars($loan['tgl_pinjam']); ?></td>
                                        <td><?= htmlspecialchars($loan['tanggal_kembali_estimasi']); ?></td>
                                        <td><?= htmlspecialchars($loan['status_keterlambatan']); ?></td>
                                        <td>Rp <?= number_format($loan['denda'], 0, ',', '.'); ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/peminjaman/proses-pengembalian/' . htmlspecialchars($loan['no_peminjaman'])); ?>" class="btn btn-sm btn-success" onclick="return confirm('Yakin ingin mengembalikan buku ini?')" title="Kembalikan Buku">
                                                Kembalikan
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $totalDenda += $loan['denda']; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Denda:</th>
                                        <th>Rp <?= number_format($totalDenda, 0, ',', '.'); ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <p class="mt-3">Anggota tidak dapat meminjam buku baru sebelum semua pinjaman di atas dikembalikan.</p>
                        </div>
                        <hr>
                    <?php endif; // Akhir dari if (!empty($data_active_loans)) ?>

                    <?php if (empty($data_active_loans)): // Tampilkan form konfirmasi jika tidak ada pinjaman aktif ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Detail Peminjaman Sementara</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>ID Anggota</th>
                                        <td><?= htmlspecialchars($data_peminjaman_sementara['temp_id_anggota'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nama Anggota</th>
                                        <td><?= htmlspecialchars($data_peminjaman_sementara['temp_nama_anggota'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>ID Buku</th>
                                        <td><?= htmlspecialchars($data_peminjaman_sementara['temp_id_buku'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Judul Buku</th>
                                        <td><?= htmlspecialchars($data_peminjaman_sementara['temp_judul_buku'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pinjam</th>
                                        <td><?= htmlspecialchars($data_peminjaman_sementara['temp_tanggal_pinjam'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Estimasi Kembali</th>
                                        <td><?= htmlspecialchars($data_peminjaman_sementara['temp_tanggal_kembali_estimasi'] ?? 'N/A'); ?></td>
                                    </tr>
                                </table>
                                <hr>
                                <a href="<?= base_url('admin/peminjaman/step-1'); ?>" class="btn btn-secondary">
                                    <span class="glyphicon glyphicon-arrow-left"></span> Kembali ke Step 1
                                </a>
                            </div>
                            </div>

                        <div class="col-md-12 text-right" style="margin-top: 20px;">
                            <a href="<?= base_url('admin/peminjaman/data-transaksi');?>" class="btn btn-warning">
                                <span class="glyphicon glyphicon-remove"></span> Batal Peminjaman
                            </a>
                            
                            <form action="<?= base_url('admin/peminjaman/konfirmasi-peminjaman'); ?>" method="post" style="display: inline-block;">
                                <?= csrf_field(); ?>
                                <button type="submit" class="btn btn-success" <?= empty($temp_peminjaman) ? 'disabled' : ''; ?> onclick="return confirm('Yakin ingin menyimpan transaksi peminjaman ini?')">
                                    <span class="glyphicon glyphicon-ok"></span> Selesaikan Peminjaman
                                </button>
                            </form>
                        </div>
                    <?php endif; // Akhir dari if (empty($data_active_loans)) ?>

                </div>
            </div>
        </div>
    </div></div><script>
    document.addEventListener('DOMContentLoaded', function() {
        // Karena bagian "Tambahkan Buku" dan "Keranjang Peminjaman" dihapus,
        // maka JavaScript terkait AJAX untuk menambah buku juga tidak diperlukan lagi.
        // Jika Master ingin mengaktifkan kembali fitur keranjang multi-item,
        // Master perlu menambahkan kembali HTML dan JavaScript yang sesuai.
    });
</script>