<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<h2>Data Item</h2>
<table id="itemTable" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Item Name</th>
        </tr>
    </thead>
</table>

<script>
    $(document).ready(function () {
        $('#itemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= base_url('item/ajax') ?>",
                type: "POST"
            },
            columns: [
                { data: 'id' },
                { data: 'item_name' }
            ]
        });
    });
</script>
<?= $this->endSection() ?>