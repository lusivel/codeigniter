<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data</li>
            <li class="active">Edit Data Buku</li>
        </ol>
    </div><div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit Data Buku</h1>
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

                    <form action="<?= base_url('admin/buku/update-data-buku');?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field(); // Penting untuk keamanan di CodeIgniter 4 ?>

                        <input type="hidden" name="id_buku" value="<?= esc($data_buku['id_buku'] ?? old('id_buku')); ?>"> <div class="form-group col-md-6">
                            <label>ID Buku</label>
                            <input type="text" class="form-control" value="<?= esc($data_buku['id_buku'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Judul Buku</label>
                            <input type="text" class="form-control" name="judul_buku" placeholder="Masukkan Judul Buku" required="required" value="<?= old('judul_buku', $data_buku['judul_buku'] ?? ''); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['judul_buku'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['judul_buku']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Pengarang</label>
                            <input type="text" class="form-control" name="pengarang" placeholder="Masukkan Nama Pengarang" value="<?= old('pengarang', $data_buku['pengarang'] ?? ''); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['pengarang'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['pengarang']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Penerbit</label>
                            <input type="text" class="form-control" name="penerbit" placeholder="Masukkan Nama Penerbit" value="<?= old('penerbit', $data_buku['penerbit'] ?? ''); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['penerbit'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['penerbit']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-3">
                            <label>Tahun Terbit (YYYY)</label>
                            <input type="text" class="form-control" name="tahun_terbit" placeholder="YYYY" maxlength="4" pattern="\d{4}" title="Masukkan tahun dalam format 4 digit (YYYY)" required="required" value="<?= old('tahun_terbit', $data_buku['tahun_terbit'] ?? ''); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['tahun_terbit'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['tahun_terbit']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Jumlah Eksemplar</label>
                            <input type="number" class="form-control" name="jumlah_eksemplar" placeholder="Jumlah" min="0" required="required" value="<?= old('jumlah_eksemplar', $data_buku['jumlah_eksemplar'] ?? '0'); ?>">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['jumlah_eksemplar'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['jumlah_eksemplar']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Kategori Buku</label>
                            <select name="id_kategori" class="form-control" required="required">
                                <option value="">Pilih Kategori</option>
                                <?php if (!empty($kategori_list)): // Perbaikan: Gunakan $kategori_list ?>
                                    <?php foreach($kategori_list as $kategori): ?>
                                    <option value="<?= esc($kategori['id_kategori']); ?>" <?= old('id_kategori', $data_buku['id_kategori'] ?? '') == $kategori['id_kategori'] ? 'selected' : ''; ?>><?= esc($kategori['nama_kategori']); ?></option>
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
                            <label>Rak</label>
                            <select name="id_rak" class="form-control" required="required">
                                <option value="">Pilih Rak</option>
                                <?php if (!empty($rak_list)): // Perbaikan: Gunakan $rak_list ?>
                                    <?php foreach($rak_list as $rak): ?>
                                    <option value="<?= esc($rak['id_rak']); ?>" <?= old('id_rak', $data_buku['id_rak'] ?? '') == $rak['id_rak'] ? 'selected' : ''; ?>><?= esc($rak['nama_rak']); ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Tidak ada rak aktif.</option>
                                <?php endif; ?>
                            </select>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_rak'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['id_rak']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Cover Buku (Max: 1MB, Tipe: JPG, PNG, GIF)</label> <input type="file" class="form-control" name="cover_buku" accept="image/jpeg,image/png,image/gif">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['cover_buku'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['cover_buku']); ?></small>
                            <?php endif; ?>
                            <?php if (!empty($data_buku['cover_buku'])): ?>
                                <p><small>Cover saat ini: <a href="<?= base_url('uploads/covers/' . esc($data_buku['cover_buku'])); ?>" target="_blank"><?= esc($data_buku['cover_buku']); ?></a></small></p>
                                <input type="hidden" name="existing_cover" value="<?= esc($data_buku['cover_buku']); ?>"> <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>File E-Book (Max: 10MB, Tipe: PDF)</label> <input type="file" class="form-control" name="e_book" accept="application/pdf"> <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['e_book'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['e_book']); ?></small>
                            <?php endif; ?>
                            <?php if (!empty($data_buku['e_book'])): ?>
                                <p><small>E-Book saat ini: <a href="<?= base_url('uploads/ebooks/' . esc($data_buku['e_book'])); ?>" target="_blank"><?= esc($data_buku['e_book']); ?></a></small></p>
                                <input type="hidden" name="existing_ebook" value="<?= esc($data_buku['e_book']); ?>"> <?php endif; ?>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3" placeholder="Masukkan Keterangan Tambahan (maks. 500 karakter)" maxlength="500"><?= old('keterangan', $data_buku['keterangan'] ?? ''); ?></textarea>
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['keterangan'])): ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['keterangan']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="<?= base_url('admin/buku/master-data-buku');?>"><button type="button" class="btn btn-warning">Batal</button></a>
                        </div>
                        <div style="clear:both;"></div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div></div>