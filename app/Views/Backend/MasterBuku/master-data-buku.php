<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data</li>
            <li class="active">Data Buku</li>
        </ol>
    </div><div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Master Data Buku</h1>
        </div>
    </div><div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Daftar Buku
                    <a href="<?= base_url('admin/buku/input-data-buku'); ?>" class="btn btn-primary btn-sm pull-right">
                        <span class="glyphicon glyphicon-plus"></span> Input Data Buku
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

                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="id_buku" data-sort-order="asc">
                        <thead>
                            <tr>
                                <th data-sortable="true">No</th>
                                <th data-sortable="true">ID Buku</th>
                                <th data-sortable="true">Cover Buku</th>
                                <th data-sortable="true">Judul Buku</th>
                                <th data-sortable="true">Pengarang</th>
                                <th data-sortable="true">Penerbit</th>
                                <th data-sortable="true">Tahun Terbit</th>
                                <th data-sortable="true">Jumlah Eksemplar</th>
                                <th data-sortable="true">ID Kategori</th> <th data-sortable="true">ID Rak</th> <th data-sortable="true">Keterangan</th>
                                <th data-sortable="true">E-Book</th> <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data_buku)) : ?>
                                <?php $no = 1; foreach ($data_buku as $buku) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= esc($buku['id_buku']); ?></td>
                                        <td>
                                            <?php if (!empty($buku['cover_buku'])) : ?>
                                                <img src="<?= base_url('uploads/covers/' . esc($buku['cover_buku'])); ?>" alt="Cover Buku" width="50">
                                            <?php else : ?>
                                                Tidak Ada Cover
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($buku['judul_buku']); ?></td>
                                        <td><?= esc($buku['pengarang']); ?></td>
                                        <td><?= esc($buku['penerbit']); ?></td>
                                        <td><?= esc($buku['tahun_terbit']); ?></td>
                                        <td><?= esc($buku['jumlah_eksemplar']); ?></td>
                                        <td><?= esc($buku['id_kategori']); ?></td> <td><?= esc($buku['id_rak']); ?></td> <td><?= esc($buku['keterangan']); ?></td>
                                        <td>
                                            <?php if (!empty($buku['e_book'])) : ?>
                                                <a href="<?= base_url('uploads/ebooks/' . esc($buku['e_book'])); ?>" target="_blank" class="btn btn-xs btn-primary">Lihat E-Book</a>
                                            <?php else : ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/buku/edit-data-buku/' . esc($buku['id_buku'])); ?>" class="btn btn-sm btn-info" title="Edit Data">
                                                <i class="glyphicon glyphicon-pencil"></i> Edit
                                            </a>
                                            <form action="<?= base_url('admin/buku/hapus-data-buku/' . esc($buku['id_buku'])); ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data buku ini secara permanen?');">
                                                <?= csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                                    <i class="glyphicon glyphicon-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="13" class="text-center">Tidak ada data buku yang ditemukan.</td> </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div></div>