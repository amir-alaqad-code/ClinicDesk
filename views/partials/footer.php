<footer class="main-footer">
    <strong>&copy; <?= date("Y") ?> <?= APP_NAME ?>.</strong>
    All rights reserved.
</footer>

</div>
<!-- End wrapper -->

<!-- jQuery from local AdminLTE files -->
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap JS from local AdminLTE files -->
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE main JS file -->
<script src="<?= BASE_URL ?>public/assets/adminlte/dist/js/adminlte.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<script>
    $(function() {
        $('.datatable').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            ordering: true
        });
    });
</script>
</body>

</html>