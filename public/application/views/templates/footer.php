<!-- </div> -->
</div>
<!-- Required Jquery -->
<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/popper.js/popper.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/bootstrap/js/bootstrap.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- modernizr js -->
<script type="text/javascript" src="<?= base_url(); ?>assets/js/modernizr/modernizr.js"></script>
<!-- am chart -->
<script src="<?= base_url(); ?>assets/pages/widget/amchart/amcharts.min.js"></script>
<script src="<?= base_url(); ?>assets/pages/widget/amchart/serial.min.js"></script>
<!-- Todo js -->
<script type="text/javascript " src="<?= base_url(); ?>assets/pages/todo/todo.js "></script>
<!-- Custom js -->
<script type="text/javascript" src="<?= base_url(); ?>assets/pages/dashboard/custom-dashboard.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/script.js"></script>
<script type="text/javascript " src="<?= base_url(); ?>assets/js/SmoothScroll.js"></script>
<script src="<?= base_url(); ?>assets/js/pcoded.min.js"></script>
<script src="<?= base_url(); ?>assets/js/demo-12.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?= base_url(); ?>assets/fontawesome/js/fontawesome.min.js"></script>
<script src="<?= base_url(); ?>assets/js/moment.min.js"></script>
<!-- data table -->
<script src="<?= base_url(); ?>assets/tables/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>assets/tables/js/datatable/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/tables/js/datatable-init/datatable-basic.min.js"></script>
<script src="<?= base_url(); ?>assets/tables/js/datatable/dataTables.buttons.min.js"></script>
<script src="<?= base_url(); ?>assets/tables/js/buttons.html5.min.js"></script>
<script src="<?= base_url(); ?>assets/tables/js/buttons.print.min.js"></script>

<script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/searchpanes/1.4.0/js/dataTables.searchPanes.min.js"></script>
<!-- data table -->

<script src="<?= base_url(); ?>assets/build/js/bootstrap-datetimepicker.min.js"></script>
<script>
    var $window = $(window);
    var nav = $('.fixed-button');
    $window.scroll(function() {
        if ($window.scrollTop() >= 200) {
            nav.addClass('active');
        } else {
            nav.removeClass('active');
        }
    });
</script>

<script>
    // upload file gambar
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    $('.form-check-input').on('click', function() {
        const id_menu = $(this).data('menu');
        const id_role = $(this).data('role');

        $.ajax({
            url: "<?= base_url('Admin/menu/gantiaccess'); ?>",
            type: 'post',
            data: {
                id_menu: id_menu,
                id_role: id_role
            },
            success: function() {
                document.location.href = "<?= base_url('Admin/menu/role/'); ?>" + id_role;
            }
        });
    });

    jQuery(document).ready(function($) {
        if (window.jQuery().datetimepicker) {
            $('.datetimepicker').datetimepicker({
                // Formats
                // follow MomentJS docs: https://momentjs.com/docs/#/displaying/format/
                format: 'YYYY-MM-DD H:mm',

                // Your Icons
                // as Bootstrap 4 is not using Glyphicons anymore
                icons: {
                    time: 'fa fa-clock',
                    date: 'fa fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-check',
                    clear: 'fa fa-trash',
                    close: 'fa fa-times'
                }
            });
        }
    });
</script>

<script>
    var base_url = '<?php echo base_url() ?>';
</script>
<script src="<?= base_url(); ?>assets/js/custom.js"></script>
<?php
$query_times = 0;
foreach ($this->db->query_times as $query_time) {
    $query_times += $query_time;
}
echo '<p class="px-3 text-right">Query Execution Time : ' . round($query_times, 4) . ' s. ';
echo 'Total Execution Time : ' . ($this->benchmark->elapsed_time()) . ' s.</p>';

?>
</body>

</html>