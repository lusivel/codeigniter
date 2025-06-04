<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data Anggota</li>
            <li class="active">Edit Data Anggota</li>
        </ol>
    </div><div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Edit Anggota</h3>
                    <hr />

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

                    <form action="<?= base_url('anggota/update-data-anggota');?>" method="post">
                        <?= csrf_field(); // Penting untuk keamanan di CodeIgniter 4 ?>

                        <input type="hidden" name="id_anggota" value="<?= esc($data_anggota['id_anggota'] ?? old('id_anggota')); ?>">

                        <div class="form-group col-md-6">
                            <label>Nama Anggota</label>
                            <input type="text" class="form-control" name="nama_anggota" placeholder="Masukkan Nama Anggota" required="required" value="<?= old('nama_anggota', $data_anggota['nama_anggota'] ?? ''); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_anggota'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['nama_anggota']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Jenis Kelamin</label>
                            <select class="form-control" name="jenis_kelamin" required="required">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" <?= old('jenis_kelamin', $data_anggota['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?= old('jenis_kelamin', $data_anggota['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['jenis_kelamin'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['jenis_kelamin']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>No. Telepon</label>
                            <input type="text" class="form-control" name="no_tlp" placeholder="Masukkan No. Telepon" required="required" value="<?= old('no_tlp', $data_anggota['no_tlp'] ?? ''); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['no_tlp'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['no_tlp']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat" placeholder="Masukkan Alamat" required="required"><?= old('alamat', $data_anggota['alamat'] ?? ''); ?></textarea>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['alamat'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['alamat']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Masukkan Email" required="required" value="<?= old('email', $data_anggota['email'] ?? ''); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['email']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Password Anggota (Kosongkan jika tidak ingin diubah)</label>
                            <input type="password" class="form-control" name="password_anggota" placeholder="Kosongkan jika tidak ingin diubah">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password_anggota'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['password_anggota']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="<?= base_url('anggota/master-data-anggota');?>" class="btn btn-warning">Batal</a>
                        </div>
                        <div style="clear:both;"></div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div></div>