<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data</li>
            <li class="active">Data Admin</li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Master Data Admin</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Daftar Admin
                    <a href="<?= base_url('admin/input-data-admin'); ?>" class="btn btn-primary btn-sm pull-right">
                        <span class="glyphicon glyphicon-plus"></span> Input Data Admin
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

                    <table data-toggle="table"
                           data-show-refresh="true"
                           data-show-toggle="true"
                           data-show-columns="true"
                           data-search="true"
                           data-select-item-name="toolbar1"
                           data-pagination="true"
                           data-sort-name="id_admin"
                           data-sort-order="asc"
                           class="table table-bordered">
                        <thead>
                            <tr>
                                <th data-sortable="true">No</th>
                                <th data-sortable="true">ID Admin</th>
                                <th data-sortable="true">Nama Admin</th>
                                <th data-sortable="true">Username</th>
                                <th data-sortable="true">Akses Level</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data_user)) : ?>
                                <?php $no = 1; foreach ($data_user as $admin) : ?>
                                    <?php if ($admin['is_delete_admin'] == 0): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= esc($admin['id_admin']); ?></td>
                                            <td><?= esc($admin['nama_admin']); ?></td>
                                            <td><?= esc($admin['username_admin']); ?></td>
                                            <td><?= esc($admin['akses_level']); ?></td>
                                            <td>
                                                <a href="<?= base_url('admin/edit-data-admin/' . esc($admin['id_admin'])); ?>" class="btn btn-sm btn-info" title="Edit Data">
                                                    <i class="glyphicon glyphicon-pencil"></i> Edit
                                                </a>
                                                <form action="<?= base_url('admin/hapus-data-admin/' . esc($admin['id_admin'])); ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data admin ini secara permanen?');">
                                                    <?= csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                                        <i class="glyphicon glyphicon-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data admin yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
