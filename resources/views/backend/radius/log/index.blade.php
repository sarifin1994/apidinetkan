<!DOCTYPE html>
<html>

<head>
    <title>Radius Log (Yajra)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
</head>

<body class="p-4">
    <h2 class="mb-4">Radius Log Viewer</h2>
    <button id="clearLog" class="btn btn-danger mb-3">🧹 Bersihkan Log</button>
    <table class="table table-bordered table-hover table-sm" id="logTable">
        <thead class="table-dark">
            <tr>
                <th>Timestamp</th>
                <th>Type</th>
                <th>Message</th>
            </tr>
        </thead>
    </table>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            let table = $('#logTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: '{{ url()->current() }}',
                columns: [{
                        data: 'timestamp',
                        name: 'timestamp'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'message',
                        name: 'message'
                    }
                ],
                createdRow: function(row, data) {
                    if (data.type === 'Auth') {
                        $('td', row).eq(1).addClass('text-success');
                    } else if (data.type === 'Error') {
                        $('td', row).eq(1).addClass('text-danger');
                    } else {
                        $('td', row).eq(1).addClass('text-muted');
                    }
                }
            });
        });

        $('#clearLog').on('click', function() {
            if (confirm("Yakin ingin menghapus seluruh isi log?")) {
                $.post('{{ url('/log-radius/clear') }}', {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    alert(response.message);
                    table.ajax.reload(); // reload datatable
                }).fail(function(xhr) {
                    alert('Gagal menghapus log: ' + xhr.responseJSON.message);
                });
            }
        });
    </script>
</body>

</html>
