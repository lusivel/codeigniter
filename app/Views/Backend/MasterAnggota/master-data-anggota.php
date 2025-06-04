<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data</li>
            <li class="active">Data Anggota</li>
        </ol>
    </div><div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Master Data Anggota</h1>
        </div>
    </div><div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Daftar Anggota
                    <a href="<?= base_url('anggota/input-data-anggota'); ?>" class="btn btn-primary btn-sm pull-right">
                        <span class="glyphicon glyphicon-plus"></span> Input Data Anggota
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

                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="id_anggota" data-sort-order="asc">
                        <thead>
                            <tr>
                                <th data-sortable="true">No</th>
                                <th data-sortable="true">ID Anggota</th>
                                <th data-sortable="true">Nama Anggota</th>
                                <th data-sortable="true">Jenis Kelamin</th>
                                <th data-sortable="true">No. Telepon</th>
                                <th data-sortable="true">Email</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data_user)) : ?>
                                <?php $no = 1; foreach ($data_user as $anggota) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= esc($anggota['id_anggota']); ?></td>
                                        <td><?= esc($anggota['nama_anggota']); ?></td>
                                        <td><?= esc($anggota['jenis_kelamin']); ?></td>
                                        <td><?= esc($anggota['no_tlp']); ?></td>
                                        <td><?= esc($anggota['email']); ?></td>
                                        <td>
                                            <a href="<?= base_url('anggota/edit-data-anggota/' . esc($anggota['id_anggota'])); ?>" class="btn btn-sm btn-info" title="Edit Data">
                                                <i class="glyphicon glyphicon-pencil"></i> Edit
                                            </a>
                                            <form action="<?= base_url('anggota/hapus-data-anggota/' . esc($anggota['id_anggota'])); ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data anggota ini secara permanen?');">
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
                                    <td colspan="7" class="text-center">Tidak ada data anggota yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div></div>

