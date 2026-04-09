<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow border-left-info">
        <div class="card-header">
            <h3>Balance External Log</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                <?php endif; ?>

                <?php
                $error_message = '';
                if (isset($_SESSION['error_message'])) {
                    $error_message = $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                }
                ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert"><?= $error_message; ?></div>
                <?php endif; ?>

                <?php if (isset($alert_message)): ?>
                    <div class="alert alert-danger" role="alert"><?= $alert_message; ?></div>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table id="balanceTable" class="display" style="margin-top:20px; font-size: 14px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Gidi</th>
                            <th>Paylabs</th>
                            <th>Gv</th>
                            <th>Paydgn</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($balance_external_logs)): ?>
                        <tr>
                            <td colspan="5" align="center">No data to display</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($balance_external_logs as $index => $log): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $log->c_datetimeCreated; ?></td>
                                <td><?= $log->gidi !== null ? number_format((float)$log->gidi, 2, '.', ',') : '-'; ?></td>
                                <td><?= $log->paylabs !== null ? number_format((float)$log->paylabs, 2, '.', ',') : '-'; ?></td>
                                <td><?= $log->gv !== null ? number_format((float)$log->gv, 2, '.', ',') : '-'; ?></td>
                                <td><?= $log->paydgn !== null ? number_format((float)$log->paydgn, 2, '.', ',') : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>

            <!-- DataTables Scripts -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
            <script>
            $(document).ready(function() {
                $('#balanceTable').DataTable({
                    columnDefs: [
                        {
                            targets: [1],
                            render: function(data, type, row) {
                                if (type === 'sort' || type === 'type') {
                                    return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
                                }
                                return data;
                            }
                        }
                    ]
                });
            });
            </script>
        </div>
    </div>
</div>
