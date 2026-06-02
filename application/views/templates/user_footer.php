            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Gidi.co.id <?= date('Y'); ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-premium overflow-hidden" style="border-radius: 24px; background: var(--bg-card);">
                <div class="modal-body p-0">
                    <div class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; border-radius: 24px; background: rgba(231, 74, 59, 0.1); color: #e74a3b;">
                            <i class="fas fa-sign-out-alt fa-3x"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2" id="logoutModalLabel">Ready to Leave?</h4>
                        <p class="text-muted px-3" style="font-size: 0.95rem; line-height: 1.6;">
                            Select <strong>"Logout"</strong> below if you are ready to end your current session and secure your account.
                        </p>
                    </div>
                    <div class="px-4 pb-4 d-flex flex-column gap-2">
                        <a class="btn btn-block py-3 font-weight-bold" href="<?= base_url('auth/logout'); ?>" style="border-radius: 14px; background: #e74a3b; color: white; border: none; box-shadow: 0 4px 15px rgba(231, 74, 59, 0.25); font-size: 1rem;">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout Account
                        </a>
                        <button class="btn btn-block py-3 font-weight-bold text-muted border-0 mt-2" type="button" data-dismiss="modal" style="border-radius: 14px; background: var(--gray-100); font-size: 0.9rem;">
                            Maybe later
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Centralized Flashdata SweetAlert2 Premium Notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/'); ?>js/sb-admin-2.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="<?= base_url('assets/'); ?>js/demo/datatables-demo.js"></script>

    <!-- input formatter -->
    <script src="<?= base_url('assets/js/input-formatter.js') ?>"></script>

    <!-- Date Range Picker Component -->
    <script src="<?= base_url('assets/js/datepicker.js?v=' . time()) ?>"></script>

    <script>
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('.form-check-input').on('click', function() {
            const menuId = $(this).data('menu');
            const roleId = $(this).data('role');

            $.ajax({
                url: "<?= base_url('access-control/roles/change-access'); ?>",
                type: 'post',
                data: {
                    menuId: menuId,
                    roleId: roleId
                },
                success: function() {
                    document.location.href = "<?= base_url('access-control/roles/access/'); ?>" + roleId;
                }
            });

        });
    </script>


    <script>
        $(document).ready(function() {
            <?php if ($this->session->flashdata('success')) : ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?= addslashes(trim(str_replace(["\r", "\n"], '', $this->session->flashdata('success')))); ?>',
                    icon: 'success',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')) : ?>
                Swal.fire({
                    title: 'Error!',
                    html: '<?= addslashes(trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error')))); ?>',
                    icon: 'error',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('message')) : ?>
                Swal.fire({
                    title: 'Information',
                    html: '<?= addslashes(trim(str_replace(["\r", "\n"], '', $this->session->flashdata('message')))); ?>',
                    icon: 'info',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('info')) : ?>
                Swal.fire({
                    title: 'Information',
                    html: '<?= addslashes(trim(str_replace(["\r", "\n"], '', $this->session->flashdata('info')))); ?>',
                    icon: 'info',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('warning')) : ?>
                Swal.fire({
                    title: 'Warning!',
                    html: '<?= addslashes(trim(str_replace(["\r", "\n"], '', $this->session->flashdata('warning')))); ?>',
                    icon: 'warning',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('question')) : ?>
                Swal.fire({
                    title: 'Confirmation',
                    html: '<?= addslashes(trim(str_replace(["\r", "\n"], '', $this->session->flashdata('question')))); ?>',
                    icon: 'question',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            // Global Copy Reference ID functionality
            $(document).on('click', '.copy-ref-btn', function() {
                var target = $(this).data('target');
                var text = $(target).text().trim();
                if (text && text !== '-' && text !== 'Loading...' && text !== '...') {
                    var $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(text).select();
                    document.execCommand("copy");
                    $temp.remove();
                    
                    var $icon = $(this).find('i');
                    $icon.removeClass('fas fa-copy').addClass('fas fa-check text-success');
                    setTimeout(function() {
                        $icon.removeClass('fas fa-check text-success').addClass('fas fa-copy');
                    }, 1500);
                }
            });
        });
    </script>

    <!-- Extracted Global JS (Sidebar, UX, Monitoring) -->
    <script>
        window.BASE_URL = "<?= base_url(); ?>";
    </script>
    <script src="<?= base_url('assets/js/gidi-core.js?v=' . time()) ?>"></script>
    
</body>

</html>
