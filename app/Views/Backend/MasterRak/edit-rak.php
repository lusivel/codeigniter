<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data Rak</li>
            <li class="active">Edit Data Rak</li>
        </ol>
    </div><div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Edit Rak</h3>
                    <hr />

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

                    <?php // Menampilkan error validasi dari controller (jika ada) ?>
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5>Terjadi Kesalahan Validasi:</h5>
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $field => $error): ?>
                                    <li><?= esc($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/rak/update-data-rak');?>" method="post">
                        <?= csrf_field(); // Penting untuk keamanan di CodeIgniter 4 ?>

                        <input type="hidden" name="id_rak" value="<?= esc($data_rak['id_rak'] ?? old('id_rak')); ?>">

                        <div class="form-group col-md-6">
                            <label>Nama Rak</label>
                            <input type="text" class="form-control" name="nama_rak" placeholder="Masukkan Nama Rak" required="required" value="<?= old('nama_rak', $data_rak['nama_rak'] ?? ''); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_rak'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['nama_rak']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="<?= base_url('admin/rak/master-data-rak');?>"><button type="button" class="btn btn-warning">Batal</button></a>
                        </div>
                        <div style="clear:both;"></div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div></div>