<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data Buku</li>
            <li class="active">Input Data Buku</li>
        </ol>
    </div><div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Input Buku</h3>
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

                    <form action="<?= base_url('admin/buku/simpan-data-buku');?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field(); // Penting untuk keamanan di CodeIgniter 4 ?>

                        <div class="form-group col-md-6">
                            <label>Judul Buku</label>
                            <input type="text" class="form-control" name="judul_buku" placeholder="Masukkan Judul Buku" required="required" value="<?= old('judul_buku'); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['judul_buku'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['judul_buku']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Pengarang</label>
                            <input type="text" class="form-control" name="pengarang" placeholder="Masukkan Nama Pengarang" required="required" value="<?= old('pengarang'); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['pengarang'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['pengarang']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Penerbit</label>
                            <input type="text" class="form-control" name="penerbit" placeholder="Masukkan Nama Penerbit" required="required" value="<?= old('penerbit'); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['penerbit'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['penerbit']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Tahun Terbit</label>
                            <input type="number" class="form-control" name="tahun_terbit" placeholder="Masukkan Tahun Terbit (Contoh: 2023)" required="required" value="<?= old('tahun_terbit'); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['tahun_terbit'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['tahun_terbit']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Jumlah Eksemplar</label>
                            <input type="number" class="form-control" name="jumlah_eksemplar" placeholder="Masukkan Jumlah Eksemplar" required="required" min="1" value="<?= old('jumlah_eksemplar'); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['jumlah_eksemplar'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['jumlah_eksemplar']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Kategori Buku</label>
                            <select class="form-control" name="id_kategori" required="required">
                                <option value="">-- Pilih Kategori --</option>
                                <?php if (!empty($kategori_list)): // Memastikan $kategori_list tidak kosong ?>
                                    <?php foreach ($kategori_list as $kategori): ?>
                                        <option value="<?= esc($kategori['id_kategori']); ?>" <?= old('id_kategori') == $kategori['id_kategori'] ? 'selected' : ''; ?>>
                                            <?= esc($kategori['nama_kategori']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Tidak ada kategori aktif.</option>
                                <?php endif; ?>
                            </select>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_kategori'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['id_kategori']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Rak Buku</label>
                            <select class="form-control" name="id_rak" required="required">
                                <option value="">-- Pilih Rak --</option>
                                <?php if (!empty($rak_list)): // Memastikan $rak_list tidak kosong ?>
                                    <?php foreach ($rak_list as $rak): ?>
                                        <option value="<?= esc($rak['id_rak']); ?>" <?= old('id_rak') == $rak['id_rak'] ? 'selected' : ''; ?>>
                                            <?= esc($rak['nama_rak']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Tidak ada rak aktif.</option>
                                <?php endif; ?>
                            </select>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_rak'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['id_rak']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-12">
                            <label>Keterangan (Opsional)</label>
                            <textarea class="form-control" name="keterangan" placeholder="Keterangan tambahan tentang buku" rows="3"><?= old('keterangan'); ?></textarea>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['keterangan'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['keterangan']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Cover Buku</label>
                            <input type="file" class="form-control" name="cover_buku">
                            <p class="help-block">Format file yang diizinkan: .jpg, .jpeg, .png, .gif. Maksimal ukuran 1 MB.</p>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['cover_buku'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['cover_buku']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>E-Book (PDF)</label>
                            <input type="file" class="form-control" name="e_book">
                            <p class="help-block">Format file yang diizinkan: .pdf. Maksimal ukuran 10 MB.</p>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['e_book'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['e_book']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                            <a href="<?= base_url('admin/buku/master-data-buku');?>"><button type="button" class="btn btn-warning">Batal</button></a>
                        </div>
                        <div style="clear:both;"></div>
                    </form>

                </div>
            </div>
        </div>
    </div></div>