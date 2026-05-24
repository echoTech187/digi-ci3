<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Configure external payment channel mappings for merchants</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i> Mapping Configuration Form</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <!-- Alerts Standardized to Swal2 Premium -->
                    <script>
                        $(document).ready(function() {
                            <?php if ($this->session->flashdata('success')) : ?>
                                Swal.fire({
                                    title: 'Success!',
                                    text: '<?= $this->session->flashdata('success'); ?>',
                                    icon: 'success',
                                    customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                    buttonsStyling: false
                                });
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('error')) : ?>
                                Swal.fire({
                                    title: 'Error!',
                                    html: '<?= trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error'))); ?>',
                                    icon: 'error',
                                    customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                                    buttonsStyling: false
                                });
                            <?php endif; ?>
                        });
                    </script>

                    <form action="<?= base_url('external/cashin/add'); ?>" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        
                        <!-- Merchant Selection -->
                        <div class="section-title mb-4 mt-0 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-user-circle mr-2"></i> Merchant Selection
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700 small">Select Merchant</label>
                            <select name="ref_merchantId" class="form-control select2" required>
                                <option value="">Select merchant</option>
                                <?php foreach ($merchants as $m) : ?>
                                    <option value="<?= $m->id ?>"><?= $m->c_name ?> (ID: <?= $m->id ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">

                        <!-- Channel Configuration -->
                        <div class="section-title mb-4 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-network-wired mr-2"></i> Channel Configuration
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Channel Group</label>
                                <select name="c_cashinChannelGroup" id="c_cashinChannelGroup" class="form-control select2" required>
                                    <option value="">Select group</option>
                                    <?php foreach ($channel_groups as $cg) : ?>
                                        <option value="<?= $cg->c_channelGroup ?>"><?= $cg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">External ID Default</label>
                                <select name="c_externalIdDefault" id="c_externalIdDefault" class="form-control select2" required>
                                    <option value="">Select external ID</option>
                                    <?php foreach ($channel_external_id_defaults as $ext) : ?>
                                        <option value="<?= $ext->c_externalIdDefault ?>"><?= $ext->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Specific Channel ID</label>
                                <select name="ref_cashinChannelId" id="ref_cashinChannelId" class="form-control select2" required>
                                    <option value="">Select channel ID</option>
                                </select>
                                <small class="text-muted">Available IDs depend on Group and External ID selection</small>
                            </div>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">

                        <!-- Fee Structure -->
                        <div class="section-title mb-4 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-hand-holding-usd mr-2"></i> Fee Structure & Settlement
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Fee Type</label>
                                <select name="c_feeType" class="form-control select2" required>
                                    <option value="IDR">IDR (Fixed)</option>
                                    <option value="Percentage">Percentage (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Settlement Interval (Days)</label>
                                <input type="number" name="c_settlementInterval" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Fixed Fee (IDR)</label>
                                <input type="text" name="c_fee" class="input-rupiah form-control" placeholder="0" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Percentage Fee (%)</label>
                                <input type="text" name="c_feePercetange" class="input-percentage form-control" placeholder="0.00" required>
                            </div>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">

                        <!-- Limits & Status -->
                        <div class="section-title mb-4 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-sliders-h mr-2"></i> Limits & Status
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Amount Min</label>
                                <input type="text" name="c_amountMin" class="input-rupiah form-control" placeholder="1000" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Amount Max</label>
                                <input type="text" name="c_amountMax" class="input-rupiah form-control" placeholder="20000000" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Status</label>
                                <select name="c_status" class="form-control select2" required>
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5 pt-3 border-top">
                            <a href="<?= base_url('external/cashin'); ?>" class="btn btn-light px-4 py-2 mr-3 font-weight-bold small text-uppercase">Cancel</a>
                            <button type="submit" class="btn-dt-action btn-dt-action-success px-5 py-2">
                                <i class="fas fa-save mr-2"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-shape bg-white text-primary rounded-circle mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h5 class="font-weight-bold mb-0">Mapping Guide</h5>
                    </div>
                    <p class="small mb-4 opacity-75">Link a merchant to a specific payment channel provider. Ensure the channel group and external ID match your provider's configuration.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm dt-card">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold text-gray-800 mb-3 d-flex align-items-center">
                        <i class="fas fa-lightbulb text-warning mr-2"></i> Configuration Tips
                    </h6>
                    <ul class="list-unstyled mb-0 small text-muted" style="line-height: 1.8;">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                            <span>Multiple merchants can be linked to the same channel group.</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                            <span>The fee can be either a fixed amount or a percentage of the transaction.</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                            <span>Settlement interval is in days from the transaction date.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function updateChannelIds() {
            let group = $('#c_cashinChannelGroup').val();
            let external = $('#c_externalIdDefault').val();
            const $channelId = $('#ref_cashinChannelId');

            if (group && external) {
                $channelId.prop('disabled', true).html('<option value="">Loading...</option>');
                $.post('<?= base_url("merchant/setting-cashin-fee/groups") ?>', { 
                    c_cashinChannelGroup: group, 
                    c_externalIdDefault: external,
                    '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val() || '<?= $this->security->get_csrf_hash(); ?>'
                }, function(data) {
                    const options = typeof data === 'string' ? JSON.parse(data) : data;
                    $channelId.empty().append('<option disabled selected>Select channel ID</option>');
                    if (options.length > 0) {
                        options.forEach(item => $channelId.append(`<option value="${item.id}">${item.id}</option>`));
                        $channelId.prop('disabled', false);
                    } else {
                        $channelId.append('<option disabled>No channels found</option>').prop('disabled', true);
                    }
                    $channelId.trigger('change');
                });
            }
        }

        $('#c_cashinChannelGroup, #c_externalIdDefault').change(updateChannelIds);
    });
</script>
