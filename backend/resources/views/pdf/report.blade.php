<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <h3>Laporan {{ $nama_surat }}</h3>
    <p>Periode: {{ $start_date }} / {{ $end_date }}</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td rowspan="{{ count($report) - 1 }}">{{ date('l, d-m-Y', strtotime($report['tanggal'])) }}</td>
                    @php
                        $report_name_index_1 = array_keys($report)[1];
                        $report_index_1 = array_values($report)[1];
                    @endphp
                    <td>{{ $report_name_index_1 }}</td>
                    <td>{{ $report_index_1 }}</td>
                </tr>
                @for ($i = 2; $i < count($report); $i++)
                    @php
                        $report_name_index = array_keys($report)[$i];
                        $report_index = array_values($report)[$i];
                    @endphp
                    <tr>
                        <td>{{ $report_name_index }}</td>
                        <td>{{ $report_index }}</td>
                    </tr>
                @endfor
            @endforeach
        </tbody>
    </table>
    <hr class="my-4">
    <h3>Summary</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summary as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script>
        window.print();
    </script>
</body>

</html>
