<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Update external payment channel mapping for merchant #<?= $mapping['ref_merchantId'] ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm dt-card">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i> Update Mapping Configuration</h6>
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

                    <form action="<?= base_url('external/cashin/update'); ?>" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="id" value="<?= $mapping['id'] ?>">
                        
                        <!-- Merchant Selection -->
                        <div class="section-title mb-4 mt-0 text-primary font-weight-bold small text-uppercase letter-spacing-1">
                            <i class="fas fa-user-circle mr-2"></i> Merchant Selection
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700 small">Select Merchant</label>
                            <select name="ref_merchantId" class="form-control select2" required>
                                <option value="">Select merchant</option>
                                <?php foreach ($merchants as $m) : ?>
                                    <option value="<?= $m->id ?>" <?= ($m->id == $mapping['ref_merchantId']) ? 'selected' : '' ?>>
                                        <?= $m->c_name ?> (ID: <?= $m->id ?>)
                                    </option>
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
                                        <option value="<?= $cg->c_channelGroup ?>" <?= ($cg->c_channelGroup == $mapping['c_cashinChannelGroup']) ? 'selected' : '' ?>>
                                            <?= $cg->c_channelGroup ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">External ID Default</label>
                                <select name="c_externalIdDefault" id="c_externalIdDefault" class="form-control select2" required>
                                    <option value="">Select external ID</option>
                                    <?php foreach ($channel_external_id_defaults as $ext) : ?>
                                        <option value="<?= $ext->c_externalIdDefault ?>" <?= ($ext->c_externalIdDefault == $mapping['c_externalIdDefault']) ? 'selected' : '' ?>>
                                            <?= $ext->c_externalIdDefault ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Specific Channel ID</label>
                                <select name="ref_cashinChannelId" id="ref_cashinChannelId" class="form-control select2" required>
                                    <option value="<?= $mapping['ref_cashinChannelId'] ?>" selected><?= $mapping['ref_cashinChannelId'] ?></option>
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
                                    <option value="IDR" <?= ($mapping['c_feeType'] == 'IDR') ? 'selected' : '' ?>>IDR (Fixed)</option>
                                    <option value="Percentage" <?= ($mapping['c_feeType'] == 'Percentage') ? 'selected' : '' ?>>Percentage (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Settlement Interval (Days)</label>
                                <input type="number" name="c_settlementInterval" class="form-control" value="<?= floor(floatval($mapping['c_settlementInterval'])) ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Fixed Fee (IDR)</label>
                                <input type="text" name="c_fee" class="input-rupiah form-control" value="<?= floor(floatval($mapping['c_fee'])) ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Percentage Fee (%)</label>
                                <input type="text" name="c_feePercetange" class="input-percentage form-control" value="<?= $mapping['c_feePercetange'] ?>" required>
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
                                <input type="text" name="c_amountMin" class="input-rupiah form-control" value="<?= floor(floatval($mapping['c_amountMin'])) ?>" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Amount Max</label>
                                <input type="text" name="c_amountMax" class="input-rupiah form-control" value="<?= floor(floatval($mapping['c_amountMax'])) ?>" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-gray-700 small">Status</label>
                                <select name="c_status" class="form-control select2" required>
                                    <option value="Active" <?= ($mapping['c_status'] == 'Active') ? 'selected' : '' ?>>Active</option>
                                    <option value="Not Active" <?= ($mapping['c_status'] == 'Not Active') ? 'selected' : '' ?>>Not Active</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5 pt-3 border-top">
                            <a href="<?= base_url('external/cashin'); ?>" class="btn btn-light px-4 py-2 mr-3 font-weight-bold small text-uppercase">Cancel</a>
                            <button type="submit" class="btn-dt-action btn-dt-action-success px-5 py-2">
                                <i class="fas fa-save mr-2"></i> Save Changes
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
                            <i class="fas fa-edit"></i>
                        </div>
                        <h5 class="font-weight-bold mb-0">Current Mapping</h5>
                    </div>
                    
                    <div class="mb-4">
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Mapping ID</div>
                        <div class="h5 mb-0 font-weight-bold">#<?= $mapping['id'] ?></div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Channel Provider</div>
                        <div class="mb-0 font-weight-bold"><?= $mapping['c_externalIdDefault'] ?></div>
                    </div>
                    
                    <div>
                        <div class="small opacity-75 mb-1 text-uppercase letter-spacing-1 font-weight-bold">Channel Group</div>
                        <div class="mb-0 font-weight-bold"><?= $mapping['c_cashinChannelGroup'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Instructions Guide -->
            <div class="card border-0 shadow-sm dt-card mt-4">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-book-open mr-2"></i> Instructions Guide</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <p class="text-muted small mb-4">Modify the mapping parameters for the selected merchant and channel group.</p>
                    <div class="p-3 mb-3" style="background:rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.05); border-radius:12px;">
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-hand-holding-usd text-primary mr-2"></i> Fee Updates</div>
                        <p class="text-muted small mb-0">Changes to the fixed fee or percentage fee apply to all future incoming transactions for this merchant on this channel.</p>
                    </div>
                    <div class="p-3" style="background:rgba(0,0,0,0.02); border:1px solid rgba(0,0,0,0.05); border-radius:12px;">
                        <div class="font-weight-bold mb-1 text-dark" style="font-size: 13px;"><i class="fas fa-exclamation-triangle text-primary mr-2"></i> Limits & Status</div>
                        <p class="text-muted small mb-0">If marked "Not Active", transactions through this mapping will be blocked immediately.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#c_cashinChannelGroup').on('change', function() {
            const group = $(this).val();
            $('#c_externalIdDefault').val('').trigger('change.select2');
            $('#ref_cashinChannelId').val('').trigger('change.select2');
            fetchOptions(group, '', true);
        });

        $('#c_externalIdDefault').on('change', function() {
            const group = $('#c_cashinChannelGroup').val();
            const external_id = $(this).val();
            $('#ref_cashinChannelId').val('').trigger('change.select2');
            fetchOptions(group, external_id, false);
        });

        function fetchOptions(group, external_id, updateProvider) {
            const csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
            const csrfHash = $('input[name="' + csrfName + '"]').val() || '<?= $this->security->get_csrf_hash(); ?>';

            if (!group) return;

            if (updateProvider) {
                $('#c_externalIdDefault').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
            }
            $('#ref_cashinChannelId').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');

            $.ajax({
                url: "<?= base_url('external/cashin/get-filter-options') ?>",
                type: "POST",
                data: { group: group, external_id: external_id, [csrfName]: csrfHash },
                dataType: "json",
                success: function(data) {
                    if (updateProvider) {
                        let providerOptions = '<option value="">Select external ID</option>';
                        data.providers.forEach(function(item) {
                            providerOptions += `<option value="${item}">${item}</option>`;
                        });
                        $('#c_externalIdDefault').html(providerOptions).prop('disabled', false).trigger('change.select2');
                    }

                    let channelOptions = '<option value="">Select channel ID</option>';
                    data.channels.forEach(function(item) {
                        channelOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#ref_cashinChannelId').html(channelOptions).prop('disabled', false).trigger('change.select2');
                },
                error: function() {
                    if (updateProvider) $('#c_externalIdDefault').prop('disabled', false).html('<option value="">Select external ID</option>').trigger('change.select2');
                    $('#ref_cashinChannelId').prop('disabled', false).html('<option value="">Select channel ID</option>').trigger('change.select2');
                }
            });
        }
    });
</script>
