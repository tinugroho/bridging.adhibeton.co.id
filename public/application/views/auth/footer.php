<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/popper.js/popper.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/bootstrap/js/bootstrap.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- modernizr js -->
<script type="text/javascript" src="<?= base_url(); ?>assets/js/modernizr/modernizr.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/modernizr/css-scrollbars.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/common-pages.js"></script>
<script>
    $('#checkbox').click(function() {
        if ($(this).is(':checked')) {
            $('#password').attr('type', 'text');
        } else {
            $('#password').attr('type', 'password');
        }
    })
</script>
</body>

</html>