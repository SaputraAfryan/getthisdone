<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h2>Data Machine</h2>

<table id="machineTable" class="display table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Item Name</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('#machineTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('machine/ajax') ?>',
                type: 'POST'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'item_name', name: 'item_name' }
            ],
            order: [[0, 'asc']]
        });
    });
</script>

<?= $this->endSection() ?>