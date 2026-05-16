<footer class="main-footer">
    <strong><?= e(APP_NAME) ?></strong> - PHP Final Project
</footer>
</div>

<script src="<?= asset('adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<script src="<?= asset('adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= asset('adminlte/plugins/chart.js/Chart.min.js') ?>"></script>
<script src="<?= asset('adminlte/dist/js/adminlte.min.js') ?>"></script>
<script>
    if (window.jQuery) {
        $(function () {
            $('.datatable').DataTable({
                responsive: true,
                autoWidth: false
            });
        });
    }
</script>
</body>
</html>
