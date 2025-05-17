<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data Rak</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Master Data Rak
                        <a href="<?= base_url('rak/input-data-rak');?>"><button type="button" class="btn btn-sm btn-primary pull-right">Input Data Rak
                        </button></a>
                    </h3>
                    <hr />
                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="name" data-sort-order="desc">
                        <thead>
                            <tr>
                                <th data-sortable="true">#</th>
                                <th data-sortable="true">Nama Rak</th>
                                <th data-sortable="true">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            foreach($data_rak as $data){
                            ?>
                            <tr>
                                <td data-sortable="true"><?= $no=$no+1;?></td>
                                <td data-sortable="true"><?= $data['nama_rak'];?></td>
                                <td data-sortable="true">
                                    <?php
                                    if(session()->get('ses_level')=="1" || session()->get('ses_level')=="3"){
                                    ?>
                                    <a href="<?= base_url('rak/edit-data-rak/'.sha1($data['id_rak']));?>"><button type="button" class="btn btn-sm btn-success">Edit</button></a>
                                    <a href="#" onclick="doDelete('<?= sha1($data['id_rak']);?>')"><button type="button" class="btn btn-sm btn-danger">Hapus</button></a>
                                    <?php }
                                    else echo "#"; ?>
                                 </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                     </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function doDelete(idDelete){
        swal({
            title : "Hapus Data Rak?",
            text : "Data ini akan terhapus secara permanen!!",
            icon : "warning",
            buttons : true,
            dangerMode : false,
        })
        .then(ok => {
            if(ok){
                window.location.href = '<?= base_url();?>/rak/hapus-data-rak/' + idDelete;
            }
            else{
                $(this).removeAttr('disabled')
            }
        })
    }
</script>