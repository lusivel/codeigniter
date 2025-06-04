<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Dashboard</li>
        </ol>
    </div><div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Dashboard</h1>
        </div>
    </div><div class="row">
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-blue panel-widget ">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <em class="glyphicon glyphicon-book glyphicon-l"></em>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large"><?= $total_buku ?? '0'; ?></div> <div class="text-muted">Total Buku</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-orange panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <em class="glyphicon glyphicon-user glyphicon-l"></em>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large"><?= $total_anggota ?? '0'; ?></div> <div class="text-muted">Total Anggota</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-teal panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <em class="glyphicon glyphicon-tasks glyphicon-l"></em>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large"><?= $total_peminjaman_aktif ?? '0'; ?></div> <div class="text-muted">Pinjaman Aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-red panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <em class="glyphicon glyphicon-warning-sign glyphicon-l"></em>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large"><?= $total_denda_aktif ?? 'Rp 0'; ?></div> <div class="text-muted">Total Denda</div>
                    </div>
                </div>
            </div>
        </div>
    </div><div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Selamat Datang, <?= session()->get('ses_user') ?? 'Pengguna'; ?>!</div>
                <div class="panel-body">
                    <p>Ini adalah Dashboard Admin untuk Aplikasi Perpustakaan Anda.</p>
                    <p>Gunakan menu di samping untuk mengelola Master Data dan Transaksi Peminjaman.</p>
                </div>
            </div>
        </div>
    </div></div>