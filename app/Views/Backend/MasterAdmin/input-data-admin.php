<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data</li>
            <li class="active">Input Data Admin</li>
        </ol>
    </div><div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Input Data Admin</h1>
        </div>
    </div><div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
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

                    <form action="<?= base_url('admin/simpan-data-admin');?>" method="post">
                        <?= csrf_field(); // Penting untuk keamanan di CodeIgniter 4 ?>

                        <div class="form-group col-md-6">
                            <label>Nama Admin</label>
                            <input type="text" class="form-control" name="nama_admin" placeholder="Masukkan Nama Admin" required="required" value="<?= old('nama_admin'); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_admin'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['nama_admin']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Username Admin</label>
                            <input type="text" class="form-control" name="username_admin" placeholder="Masukkan Username Admin" required="required" value="<?= old('username_admin'); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['username_admin'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['username_admin']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Password Admin</label>
                            <input type="password" class="form-control" name="password_admin" placeholder="Masukkan Password Admin" required="required">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password_admin'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['password_admin']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Akses Level</label>
                            <select class="form-control" name="akses_level" required="required">
                                <option value="">-- Pilih Akses Level --</option>
                                <option value="1" <?= old('akses_level') == '1' ? 'selected' : ''; ?>>1 (Super Admin)</option>
                                <option value="2" <?= old('akses_level') == '2' ? 'selected' : ''; ?>>2 (Admin)</option>
                                <option value="3" <?= old('akses_level') == '3' ? 'selected' : ''; ?>>3 (Petugas)</option>
                            </select>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['akses_level'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['akses_level']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                            <a href="<?= base_url('admin/master-data-admin');?>"><button type="button" class="btn btn-warning">Batal</button></a>
                        </div>
                        <div style="clear:both;"></div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div></div>