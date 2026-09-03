</div>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('assets/'); ?>vendor/jquery/jquery.min.js"></script>
<script src="<?= base_url('assets/'); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url('assets/'); ?>vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url('assets/'); ?>js/sb-admin-2.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    $(document).on('submit', 'form', function() {
        let $btn = $(this).find('button[type="submit"]');
        if (!$btn.hasClass('no-loader')) {
            let originalHtml = $btn.html();
            $btn.data('original-html', originalHtml);
            $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...');
        }
    });
</script>

</body>

</html>
