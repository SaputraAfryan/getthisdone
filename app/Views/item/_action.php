<div class="btn-group" role="group" aria-label="Action Buttons">
    <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="<?= esc($id) ?>" title="Edit">
        <i class="fas fa-edit"></i> Edit
    </button>

    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= esc($id) ?>" title="Delete">
        <i class="fas fa-trash-alt"></i> Delete
    </button>
</div>

<script>
    $('#table-item').on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        // Buka modal dan isi data item dengan id ini via AJAX
    });

    $('#table-item').on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        if (confirm('Yakin ingin menghapus data ini?')) {
            // Kirim AJAX DELETE ke endpoint soft delete
        }
    });
</script>