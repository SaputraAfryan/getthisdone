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

<!-- Modal untuk Add/Edit Item -->
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
                    <div class="invalid-feedback" id="name-error"></div>
                </div>

                <div class="mb-3">
                    <label for="item-code" class="form-label">Kode SKU</label>
                    <input type="text" class="form-control" name="code" id="item-code" required>
                    <div class="invalid-feedback" id="code-error"></div>
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
        let table = $('#itemTable').DataTable({
            processing: true,
            serverSide: true,
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
            order: [[2, 'asc']] // Default order by 'Nama' column
        });

        // Initialize Bootstrap Modal
        const modal = new bootstrap.Modal(document.getElementById('modal-item'));

        // Handle Add Button Click
        $('#btn-add').on('click', function () {
            $('#form-item')[0].reset();
            $('#item-id').val('');
            $('#modal-item-label').text('Tambah Item');
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
                    $('#modal-item-label').text('Edit Item');
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    modal.show();
                } else {
                    alert('Item not found!');
                }
            }).fail(function () {
                alert('Error fetching item data.');
            });
        });

        // Handle Delete Button Click (Delegated event)
        $('#itemTable').on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            
            if (confirm('Yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: '<?= base_url('item/delete') ?>/' + id,
                    method: 'DELETE',
                    success: function (response) {
                        if (response.status) {
                            table.ajax.reload(null, false);
                            alert('Data berhasil dihapus');
                        } else {
                            alert('Gagal menghapus data');
                        }
                    },
                    error: function () {
                        alert('Terjadi kesalahan saat menghapus data');
                    }
                });
            }
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
                        alert('Data berhasil disimpan');
                    } else {
                        // Show validation errors
                        if (response.errors) {
                            $.each(response.errors, function (field, message) {
                                $('#' + field + '-error').text(message);
                                $('[name="' + field + '"]').addClass('is-invalid');
                            });
                        } else {
                            alert('Terjadi kesalahan saat menyimpan data');
                        }
                    }
                },
                error: function () {
                    alert('Terjadi kesalahan saat menyimpan data');
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