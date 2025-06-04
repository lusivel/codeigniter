<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Transaksi</li>
            <li class="active">Peminjaman - Step 1</li>
        </ol>
    </div><div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Input Peminjaman Buku - Step 1</h3>
                    <hr />

                    <?php 
                    // Menampilkan pesan error atau sukses jika ada dari controller
                    if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
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

                    <form action="<?= base_url('admin/peminjaman/proses-step-1'); ?>" method="post">
                        <?= csrf_field(); // Penting untuk keamanan di CodeIgniter 4 ?>
                        
                        <div class="form-group col-md-6">
                            <label>ID Anggota</label>
                            <input type="text" class="form-control" name="id_anggota" placeholder="Masukkan ID Anggota atau Scan QR" required="required" autofocus value="<?= old('id_anggota'); ?>">
                            <?php 
                            if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_anggota'])) : ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['id_anggota']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>ID Buku / Judul Buku</label>
                            <input type="text" class="form-control" name="id_buku" placeholder="Masukkan ID Buku atau Scan Barcode" required="required" value="<?= old('id_buku'); ?>">
                            <?php 
                            if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_buku'])) : ?>
                                <small class="text-danger"><?= esc(session()->getFlashdata('errors')['id_buku']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <button type="submit" class="btn btn-primary">Next</button>
                            <a href="<?= base_url('admin/peminjaman/data-transaksi');?>" class="btn btn-danger">Batal</a>
                        </div>
                        <div style="clear:both;"></div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div></div>