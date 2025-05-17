<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data Anggota</li>
            <li class="active">Edit Data Anggota</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Edit Anggota</h3>
                    <hr />
                    <form action="<?php echo base_url('anggota/update-anggota');?>" method="post">
                        <div class="form-group col-md-6">
                            <label>Nama Anggota</label>
                            <input type="text" class="form-control" name="nama_anggota" placeholder="Masukkan Nama Anggota" value="<?php echo $data_anggota['nama_anggota'];?>" required="required">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Jenis Kelamin</label>
                            <select class="form-control" name="jenis_kelamin" required="required">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" <?php if($data_anggota['jenis_kelamin']=="Laki-laki"){ echo "selected"; } ?>>Laki-laki</option>
                                <option value="Perempuan" <?php if($data_anggota['jenis_kelamin']=="Perempuan"){ echo "selected"; } ?>>Perempuan</option>
                            </select>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>No. Telepon</label>
                            <input type="text" class="form-control" name="no_tlp" placeholder="Masukkan No. Telepon" value="<?php echo $data_anggota['no_tlp'];?>" required="required">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat" placeholder="Masukkan Alamat" required="required"><?php echo $data_anggota['alamat'];?></textarea>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Masukkan Email" value="<?php echo $data_anggota['email'];?>" required="required" readonly>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <button type="submit" class="btn btn-primary">Update</button> 
                            <a href="<?php echo base_url('anggota/master-data-anggota');?>">
                                <button type="button" class="btn btn-danger">Batal</button>
                            </a>
                        </div>
                        <div style="clear:both;"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
