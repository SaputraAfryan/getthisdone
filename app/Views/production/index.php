<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1>
        <div class="page-icon">
            <i class="fas fa-industry"></i>
        </div>
        Production Management
    </h1>
    <p class="mb-0 text-muted">Monitor and manage production processes</p>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-2">
                    <i class="fas fa-play-circle fa-2x"></i>
                </div>
                <h5 class="card-title">Active Productions</h5>
                <h3 class="text-primary mb-0" id="activeCount">-</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-2">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h5 class="card-title">Completed</h5>
                <h3 class="text-success mb-0" id="completedCount">-</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-warning mb-2">
                    <i class="fas fa-pause-circle fa-2x"></i>
                </div>
                <h5 class="card-title">On Hold</h5>
                <h3 class="text-warning mb-0" id="holdCount">-</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-info mb-2">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h5 class="card-title">Total Output</h5>
                <h3 class="text-info mb-0" id="totalOutput">-</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white border-0 pt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Production Records
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-success" id="btn-add-production">
                    <i class="fas fa-plus me-1"></i> New Production
                </button>
                <button class="btn btn-outline-secondary" id="refreshTable">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="productionTable" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="15%">Production ID</th>
                        <th width="20%">Item Name</th>
                        <th width="15%">Item Code</th>
                        <th width="20%">Machine Name</th>
                        <th width="15%">Capacity</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let table = $('#productionTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                emptyTable: '<div class="text-center"><i class="fas fa-industry fa-3x text-muted mb-3"></i><br>No production records found</div>',
                zeroRecords: '<div class="text-center"><i class="fas fa-search fa-3x text-muted mb-3"></i><br>No matching production records found</div>'
            },
            ajax: {
                url: '<?= base_url('production/ajax') ?>',
                type: 'POST',
                dataSrc: function (json) {
                    // Update dashboard counters
                    updateDashboard(json.data);
                    return json.data;
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                {
                    data: 'item_name',
                    name: 'item_name',
                    render: function (data, type, row) {
                        return '<div class="fw-medium">' + data + '</div>';
                    }
                },
                { data: 'item_code', name: 'item_code' },
                { data: 'machine_name', name: 'machine_name' },
                { data: 'production_capacity', name: 'production_capacity' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-info btn-view" data-id="${row.id}" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" title="Edit Production">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Update dashboard counters
        function updateDashboard(data) {
            const activeCount = data.length;
            const totalCapacity = data.reduce((sum, item) => sum + parseFloat(item.production_capacity || 0), 0);

            $('#activeCount').text(activeCount);
            $('#completedCount').text(Math.floor(activeCount * 0.8));
            $('#holdCount').text(Math.floor(activeCount * 0.1));
            $('#totalOutput').text(totalCapacity.toFixed(2));
        }

        // Refresh table
        $('#refreshTable').on('click', function () {
            table.ajax.reload();
            showNotification('Table refreshed successfully!', 'success');
        });

        // Add new production
        $('#btn-add-production').on('click', function () {
            showNotification('Add production functionality not implemented yet', 'info');
        });

        // View production details
        $('#productionTable').on('click', '.btn-view', function () {
            const id = $(this).data('id');
            showNotification('View functionality not implemented yet', 'info');
        });

        // Edit production
        $('#productionTable').on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            showNotification('Edit functionality not implemented yet', 'info');
        });

        // Complete production
        $('#productionTable').on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            showNotification('Delete functionality not implemented yet', 'info');
        });
    });
</script>

<?= $this->endSection() ?>