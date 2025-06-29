<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
    <ul class="nav menu">
        <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-dashboard"></span> Dashboard</a></li>

        <li class="parent ">
            <a data-toggle="collapse" href="#sub-item-master">
                <span class="glyphicon glyphicon-list"></span> Master Data <span class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-master">
                <li>
                    <a href="<?= base_url('admin/master-data-admin'); ?>">
                        <span class="glyphicon glyphicon-share-alt"></span> Data Admin
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('anggota/master-data-anggota'); ?>"> <span class="glyphicon glyphicon-share-alt"></span> Data Anggota
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/rak/master-data-rak'); ?>"> <span class="glyphicon glyphicon-share-alt"></span> Data Rak
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/kategori/master-data-kategori'); ?>"> <span class="glyphicon glyphicon-share-alt"></span> Data Kategori
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/buku/master-data-buku'); ?>">
                        <span class="glyphicon glyphicon-share-alt"></span> Data Buku
                    </a>
                </li>
            </ul>
        </li>

        <li class="parent ">
            <a data-toggle="collapse" href="#sub-item-transaksi">
                <span class="glyphicon glyphicon-transfer"></span> Transaksi <span class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-transaksi">
                <li>
                    <a href="<?= base_url('admin/peminjaman/data-transaksi'); ?>">
                        <span class="glyphicon glyphicon-share-alt"></span> Data Peminjaman
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/peminjaman/step-1'); ?>">
                        <span class="glyphicon glyphicon-share-alt"></span> Input Peminjaman
                    </a>
                </li>
                 <li>
                    <a href="<?= base_url('admin/peminjaman/daftar-pengembalian'); ?>">
                        <span class="glyphicon glyphicon-share-alt"></span> Pengembalian Buku
                    </a>
                </li>
            </ul>
        </li>

        <li role="presentation" class="divider"></li>

        <li><a href="<?= base_url('admin/logout'); ?>"><span class="glyphicon glyphicon-user"></span> Logout</a></li>
    </ul>
    <div class="attribution">Template by <a href="http://www.medialoot.com/item/lumino-admin-bootstrap-template/">Medialoot</a></div>
</div>