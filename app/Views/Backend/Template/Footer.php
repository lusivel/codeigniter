<script src="<?= base_url('assets/js/jquery-1.11.1.min.js'); ?>"></script> <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/chart.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/chart-data.js'); ?>"></script>
    <script src="<?= base_url('assets/js/easypiechart.js'); ?>"></script>
    <script src="<?= base_url('assets/js/easypiechart-data.js'); ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap-datepicker.js'); ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap-table.js'); ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.min.js'); ?>"></script>
    
    <script>
        // Skrip untuk fungsionalitas sidebar collapse (icon plus/minus)
        !function ($) {
            $(document).on("click","ul.nav li.parent > a > span.icon", function(){        
                $(this).find('em:first').toggleClass("glyphicon-minus");      
            }); 
            $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
        }(window.jQuery);

        // Skrip untuk mengelola sidebar collapse berdasarkan ukuran layar
        $(window).on('resize', function () {
          if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
        })
        $(window).on('resize', function () {
          if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
        })

        // Skrip untuk menampilkan pesan flashdata menggunakan SweetAlert2
        <?php if (session()->getFlashdata('success')) : ?>
        $(document).ready(function() {
            Swal.fire({
                title: "Berhasil!",
                text: "<?= session()->getFlashdata('success'); ?>",
                icon: "success",
                showConfirmButton: false,
                timer: 2000 // Notifikasi akan hilang setelah 2 detik
            });
        });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
        $(document).ready(function() {
            Swal.fire({
                title: "Error!",
                text: "<?= session()->getFlashdata('error'); ?>",
                icon: "error",
                showConfirmButton: true // Tombol OK akan muncul
            });
        });
        <?php endif; ?>

        <?php if (session()->getFlashdata('warning')) : ?>
        $(document).ready(function() {
            Swal.fire({
                title: "Perhatian!",
                text: "<?= session()->getFlashdata('warning'); ?>",
                icon: "warning",
                showConfirmButton: true
            });
        });
        <?php endif; ?>

        <?php if (session()->getFlashdata('info')) : ?>
        $(document).ready(function() {
            Swal.fire({
                title: "Informasi!",
                text: "<?= session()->getFlashdata('info'); ?>",
                icon: "info",
                showConfirmButton: false,
                timer: 2000
            });
        });
        <?php endif; ?>
    </script>    
</body>
</html>