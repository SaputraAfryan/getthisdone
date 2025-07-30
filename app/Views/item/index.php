<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h2>Data Item</h2>

<button class="btn btn-success mb-3" id="btn-add">
    <i class="fas fa-plus"></i> Tambah Item
</button>

<table id="itemTable" class="display table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Action</th>
            <th>Nama</th>
            <th>Code</th>
            <th>Last Update</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th><input type="text" placeholder="Search Nama" class="column-search form-control" data-column="2"></th>
            <th><input type="text" placeholder="Search Code" class="column-search form-control" data-column="3"></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="modal fade" id="modal-item" tabindex="-1" aria-labelledby="modal-item-label" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-item" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-item-label">Tambah Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="item-id">

                <div class="mb-3">
                    <label for="item-name" class="form-label">Nama Item</label>
                    <input type="text" class="form-control" name="name" id="item-name" required>
                </div>

                <div class="mb-3">
                    <label for="item-code" class="form-label">Kode SKU</label>
                    <input type="text" class="form-control" name="code" id="item-code" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        let table = $('#itemTable').DataTable({ // Changed table ID to itemTable
            processing: true,
            serverSide: true,
            ajax: '/item/data',
            columns: [
                { data: 'no', name: 'no', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
                { data: 'last_update', name: 'last_update' }
            ],
            order: [[2, 'asc']] // Default order by 'Nama' column
        });

        // Initialize Bootstrap Modal
        const modal = new bootstrap.Modal(document.getElementById('modal-item'));

        // Handle Add Button Click
        $('#btn-add').on('click', function () {
            $('#form-item')[0].reset(); // Reset the form
            $('#item-id').val(''); // Clear hidden ID
            $('#modal-item-label').text('Tambah Item'); // Set modal title
            modal.show(); // Show the modal
        });

        // Handle Edit Button Click (Delegated event for dynamically added buttons)
        $('#itemTable').on('click', '.btn-edit', function () {
            const id = $(this).data('id');

            $.get(`/item/${id}`, function (res) {
                if (res) {
                    $('#item-id').val(res.id);
                    $('#item-name').val(res.name);
                    $('#item-code').val(res.code);
                    $('#modal-item-label').text('Edit Item');
                    modal.show();
                } else {
                    alert('Item not found!');
                }
            }).fail(function () {
                alert('Error fetching item data.');
            });
        });

        // Handle Form Submission (Add/Edit)
        $('#form-item').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const id = $('#item-id').val();
            const url = id ? `/item/${id}` : '/item';
            const method = id ? 'PUT' : 'POST'; // This is important for RESTful APIs

            // Serialize the form data
            let formData = $(this).serialize();

            // Append _method for PUT requests if using a framework that needs it (like CodeIgniter for form method spoofing)
            if (method === 'PUT') {
                formData += '&_method=PUT';
            }

            $.ajax({
                url: url,
                method: 'POST', // Always use POST for jQuery.ajax, and let the _method parameter handle the actual HTTP method if needed by your backend framework
                data: formData,
                success: function (response) {
                    // Assuming your backend sends a success response
                    if (response && response.status === 'success') {
                        modal.hide(); // Hide the modal
                        table.ajax.reload(null, false); // Reload DataTable, keeping current paging
                    } else {
                        alert(response.message || 'Terjadi kesalahan saat menyimpan data.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    alert('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
                }
            });
        });

        // Column Search functionality
        $('.column-search').on('keyup change', function () {
            table.column($(this).data('column'))
                .search(this.value)
                .draw();
        });
    });
</script>

<?= $this->endSection() ?>