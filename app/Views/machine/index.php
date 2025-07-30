<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1>
        <div class="page-icon">
            <i class="fas fa-cog"></i>
        </div>
        Machine Management
    </h1>
    <p class="mb-0 text-muted">View and manage manufacturing machines</p>
</div>

<div class="card">
    <div class="card-header bg-white border-0 pt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Machines List
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" id="refreshTable">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="machineTable" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="20%">Machine ID</th>
                        <th width="40%">Machine Name</th>
                        <th width="20%">Status</th>
                        <th width="20%">Actions</th>
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
        let table = $('#machineTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                emptyTable: '<div class="text-center"><i class="fas fa-cog fa-3x text-muted mb-3"></i><br>No machines found</div>',
                zeroRecords: '<div class="text-center"><i class="fas fa-search fa-3x text-muted mb-3"></i><br>No matching machines found</div>'
            },
            ajax: {
                url: '<?= base_url('machine/ajax') ?>',
                type: 'POST'
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
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<span class="badge bg-success">Active</span>';
                    }
                },
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
                                <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" title="Edit Machine">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Refresh table
        $('#refreshTable').on('click', function () {
            table.ajax.reload();
            showNotification('Table refreshed successfully!', 'success');
        });

        // View machine details
        $('#machineTable').on('click', '.btn-view', function () {
            const id = $(this).data('id');
            showNotification('View functionality not implemented yet', 'info');
        });

        // Edit machine
        $('#machineTable').on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            showNotification('Edit functionality not implemented yet', 'info');
        });
    });
</script>

<?= $this->endSection() ?>