<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Dashboard Anggota</li>
        </ol>
    </div><!--/.row-->
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Dashboard Anggota</h1>
        </div>
    </div><!--/.row-->
    
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-blue panel-widget ">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <em class="glyphicon glyphicon-user glyphicon-l"></em>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">24</div>
                        <div class="text-muted">Anggota Baru</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-orange panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <em class="glyphicon glyphicon-envelope glyphicon-l"></em>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">52</div>
                        <div class="text-muted">Pesan Masuk</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-teal panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <em class="glyphicon glyphicon-calendar glyphicon-l"></em>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">8</div>
                        <div class="text-muted">Acara Mendatang</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-red panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <em class="glyphicon glyphicon-stats glyphicon-l"></em>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">1.2k</div>
                        <div class="text-muted">Kunjungan</div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--/.row-->
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Overview Aktivitas Anggota</div>
                <div class="panel-body">
                    <div class="canvas-wrapper">
                        <canvas class="main-chart" id="line-chart" height="200" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div><!--/.row-->
    
    <div class="row">
        <div class="col-xs-6 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body easypiechart-panel">
                    <h4>Pesan Baru</h4>
                    <div class="easypiechart" id="easypiechart-blue" data-percent="75"><span class="percent">75%</span></div>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body easypiechart-panel">
                    <h4>Tugas Selesai</h4>
                    <div class="easypiechart" id="easypiechart-orange" data-percent="60"><span class="percent">60%</span></div>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body easypiechart-panel">
                    <h4>Kehadiran</h4>
                    <div class="easypiechart" id="easypiechart-teal" data-percent="90"><span class="percent">90%</span></div>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body easypiechart-panel">
                    <h4>Aktivitas</h4>
                    <div class="easypiechart" id="easypiechart-red" data-percent="40"><span class="percent">40%</span></div>
                </div>
            </div>
        </div>
    </div><!--/.row-->
                            
    <div class="row">
        <div class="col-md-8">
        
            <div class="panel panel-default chat">
                <div class="panel-heading" id="accordion"><span class="glyphicon glyphicon-comment"></span> Chat Anggota</div>
                <div class="panel-body">
                    <ul>
                        <li class="left clearfix">
                            <span class="chat-img pull-left">
                                <img src="http://placehold.it/80/30a5ff/fff" alt="User Avatar" class="img-circle" />
                            </span>
                            <div class="chat-body clearfix">
                                <div class="header">
                                    <strong class="primary-font">Budi Santoso</strong> <small class="text-muted">15 menit lalu</small>
                                </div>
                                <p>
                                    Halo, apakah ada info terbaru untuk anggota?
                                </p>
                            </div>
                        </li>
                        <li class="right clearfix">
                            <span class="chat-img pull-right">
                                <img src="http://placehold.it/80/dde0e6/5f6468" alt="User Avatar" class="img-circle" />
                            </span>
                            <div class="chat-body clearfix">
                                <div class="header">
                                    <strong class="pull-left primary-font">Admin</strong> <small class="text-muted">5 menit lalu</small>
                                </div>
                                <p>
                                    Info terbaru sudah tersedia di halaman pengumuman.
                                </p>
                            </div>
                        </li>
                        <li class="left clearfix">
                            <span class="chat-img pull-left">
                                <img src="http://placehold.it/80/30a5ff/fff" alt="User Avatar" class="img-circle" />
                            </span>
                            <div class="chat-body clearfix">
                                <div class="header">
                                    <strong class="primary-font">Budi Santoso</strong> <small class="text-muted">15 menit lalu</small>
                                </div>
                                <p>
                                    Terima kasih atas informasinya.
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="panel-footer">
                    <div class="input-group">
                        <input id="btn-input" type="text" class="form-control input-md" placeholder="Tulis pesan Anda di sini..." />
                        <span class="input-group-btn">
                            <button class="btn btn-success btn-md" id="btn-chat">Kirim</button>
                        </span>
                    </div>
                </div>
            </div>
            
        </div><!--/.col-->
        
        <div class="col-md-4">
        
            <div class="panel panel-blue">
                <div class="panel-heading dark-overlay"><span class="glyphicon glyphicon-check"></span> Daftar Tugas</div>
                <div class="panel-body">
                    <ul class="todo-list">
                        <li class="todo-list-item">
                            <div class="checkbox">
                                <input type="checkbox" id="checkbox1" />
                                <label for="checkbox1">Rencanakan kegiatan hari ini</label>
                            </div>
                            <div class="pull-right action-buttons">
                                <a href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                <a href="#" class="flag"><span class="glyphicon glyphicon-flag"></span></a>
                                <a href="#" class="trash"><span class="glyphicon glyphicon-trash"></span></a>
                            </div>
                        </li>
                        <li class="todo-list-item">
                            <div class="checkbox">
                                <input type="checkbox" id="checkbox2" />
                                <label for="checkbox2">Perbarui profil anggota</label>
                            </div>
                            <div class="pull-right action-buttons">
                                <a href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                <a href="#" class="flag"><span class="glyphicon glyphicon-flag"></span></a>
                                <a href="#" class="trash"><span class="glyphicon glyphicon-trash"></span></a>
                            </div>
                        </li>
                        <li class="todo-list-item">
                            <div class="checkbox">
                                <input type="checkbox" id="checkbox3" />
                                <label for="checkbox3">Kirim laporan kegiatan</label>
                            </div>
                            <div class="pull-right action-buttons">
                                <a href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                <a href="#" class="flag"><span class="glyphicon glyphicon-flag"></span></a>
                                <a href="#" class="trash"><span class="glyphicon glyphicon-trash"></span></a>
                            </div>
                        </li>
                        <li class="todo-list-item">
                            <div class="checkbox">
                                <input type="checkbox" id="checkbox4" />
                                <label for="checkbox4">Minum kopi</label>
                            </div>
                            <div class="pull-right action-buttons">
                                <a href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                <a href="#" class="flag"><span class="glyphicon glyphicon-flag"></span></a>
                                <a href="#" class="trash"><span class="glyphicon glyphicon-trash"></span></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="panel-footer">
                    <div class="input-group">
                        <input id="btn-input" type="text" class="form-control input-md" placeholder="Tambah tugas baru" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-md" id="btn-todo">Tambah</button>
                        </span>
                    </div>
                </div>
            </div>
                            
        </div><!--/.col-->
    </div><!--/.row-->
</div>	<!--/.main-->
