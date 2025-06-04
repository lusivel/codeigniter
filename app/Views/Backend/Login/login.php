<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin | Perpustakaan</title>

    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/styles.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/sweetalert2.min.css'); ?>" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
</head>
<body>

    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">Log in Admin</div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?= session()->getFlashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('info')): ?>
                        <div class="alert alert-info alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?= session()->getFlashdata('info'); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/autentikasi-login'); ?>" method="post">
                        <?= csrf_field(); // Penting untuk keamanan CodeIgniter 4 ?>
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="Username atau Email" name="username" type="text" autofocus="" required="" value="<?= old('username'); ?>">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password" required="">
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/jquery-1.11.1.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.min.js'); ?>"></script>

    <script>
        $(document).ready(function() {
            <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                title: "Berhasil!",
                text: "<?= session()->getFlashdata('success'); ?>",
                icon: "success",
                showConfirmButton: false,
                timer: 2000
            });
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                title: "Error!",
                text: "<?= session()->getFlashdata('error'); ?>",
                icon: "error",
                showConfirmButton: true
            });
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')) : ?>
            Swal.fire({
                title: "Informasi!",
                text: "<?= session()->getFlashdata('info'); ?>",
                icon: "info",
                showConfirmButton: false,
                timer: 2000
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>