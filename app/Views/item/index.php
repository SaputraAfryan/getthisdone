<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1>
        <div class="page-icon">
            <i class="fas fa-box"></i>
        </div>
        Item Management
    </h1>
    <p class="mb-0 text-muted">Manage your inventory items</p>
</div>

<div class="card">
    <div class="card-header bg-white border-0 pt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Items List
            </h5>
            <button class="btn btn-success" id="btn-add">
                <i class="fas fa-plus me-1"></i> Add New Item
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Search Filters -->
        <div class="search-container">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search by Name</label>
                    <input type="text" placeholder="Enter item name..." class="column-search form-control" data-column="2">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Search by Code</label>
                    <input type="text" placeholder="Enter item code..." class="column-search form-control" data-column="3">
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table id="itemTable" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Actions</th>
                        <th width="30%">Item Name</th>
                        <th width="25%">Item Code</th>
                        <th width="25%">Last Update</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Add/Edit Item -->
<div class="modal fade" id="modal-item" tabindex="-1" aria-labelledby="modal-item-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="form-item" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-item-label">
                    <i class="fas fa-plus me-2"></i>
                    Add New Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="item-id">

                <div class="mb-3">
                    <label for="item-name" class="form-label">
                        <i class="fas fa-tag me-1"></i>
                        Item Name
                    </label>
                    <input type="text" class="form-control" name="name" id="item-name" placeholder="Enter item name" required>
                    <div class="invalid-feedback" id="name-error"></div>
                </div>

                <div class="mb-3">
                    <label for="item-code" class="form-label">
                        <i class="fas fa-barcode me-1"></i>
                        Item Code (SKU)
                    </label>
                    <input type="text" class="form-control" name="code" id="item-code" placeholder="Enter item code" required>
                    <div class="invalid-feedback" id="code-error"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>
                    Save Item
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        let table = $('#itemTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                emptyTable: '<div class="text-center"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><br>No items found</div>',
                zeroRecords: '<div class="text-center"><i class="fas fa-search fa-3x text-muted mb-3"></i><br>No matching items found</div>'
            },
            ajax: {
                url: '<?= base_url('item/ajax') ?>',
                type: 'POST'
            },
            columns: [
                { data: 'no', name: 'no', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
                { data: 'last_update', name: 'last_update' }
            ],
            order: [[2, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Initialize Bootstrap Modal
        const modal = new bootstrap.Modal(document.getElementById('modal-item'));

        // Handle Add Button Click
        $('#btn-add').on('click', function () {
            $('#form-item')[0].reset();
            $('#item-id').val('');
            $('#modal-item-label').html('<i class="fas fa-plus me-2"></i>Add New Item');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            modal.show();
        });

        // Handle Edit Button Click (Delegated event)
        $('#itemTable').on('click', '.btn-edit', function () {
            const id = $(this).data('id');

            $.get('<?= base_url('item/get') ?>/' + id, function (res) {
                if (res) {
                    $('#item-id').val(res.id);
                    $('#item-name').val(res.name);
                    $('#item-code').val(res.code);
                    $('#modal-item-label').html('<i class="fas fa-edit me-2"></i>Edit Item');
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    modal.show();
                } else {
                    showNotification('Item not found!', 'error');
                }
            }).fail(function () {
                showNotification('Error fetching item data.', 'error');
            });
        });

        // Handle Delete Button Click (Delegated event)
        $('#itemTable').on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            
            // Create custom confirmation modal
            const confirmModal = `
                <div class="modal fade" id="confirmModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Confirm Delete
                                </h5>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0">Are you sure you want to delete this item? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDelete">
                                    <i class="fas fa-trash me-1"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(confirmModal);
            const confirmModalInstance = new bootstrap.Modal(document.getElementById('confirmModal'));
            confirmModalInstance.show();
            
            $('#confirmDelete').on('click', function() {
                $.ajax({
                    url: '<?= base_url('item/delete') ?>/' + id,
                    method: 'DELETE',
                    success: function (response) {
                        if (response.status) {
                            table.ajax.reload(null, false);
                            showNotification('Item deleted successfully!', 'success');
                        } else {
                            showNotification('Failed to delete item', 'error');
                        }
                        confirmModalInstance.hide();
                        $('#confirmModal').remove();
                    },
                    error: function () {
                        showNotification('Error occurred while deleting item', 'error');
                        confirmModalInstance.hide();
                        $('#confirmModal').remove();
                    }
                });
            });
        });

        // Handle Form Submission (Add/Edit)
        $('#form-item').on('submit', function (e) {
            e.preventDefault();

            // Clear previous errors
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            $.ajax({
                url: '<?= base_url('item/store') ?>',
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.status) {
                        modal.hide();
                        table.ajax.reload(null, false);
                        const message = $('#item-id').val() ? 'Item updated successfully!' : 'Item created successfully!';
                        showNotification(message, 'success');
                    } else {
                        // Show validation errors
                        if (response.errors) {
                            $.each(response.errors, function (field, message) {
                                $('#' + field + '-error').text(message);
                                $('[name="' + field + '"]').addClass('is-invalid');
                            });
                        } else {
                            showNotification('Error occurred while saving item', 'error');
                        }
                    }
                },
                error: function () {
                    showNotification('Error occurred while saving item', 'error');
                }
            });
        });

        // Column Search functionality
        $('.column-search').on('keyup change', debounce(function () {
            const column = $(this).data('column');
            const value = this.value;
            
            table.column(column).search(value).draw();
        }, 500));
        
        // Debounce function for search
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Clear search filters
        $('#clearFilters').on('click', function() {
            $('.column-search').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>

<?= $this->endSection() ?>