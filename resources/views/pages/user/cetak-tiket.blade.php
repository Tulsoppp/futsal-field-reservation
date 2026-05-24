<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket / Resi Reservasi - {{ $reservasi->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .ticket-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #111;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            background-color: #198754;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td {
            text-align: left;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .details-table th {
            color: #777;
            width: 40%;
            font-weight: normal;
        }
        .details-table td {
            font-weight: bold;
            font-size: 16px;
        }
        .total-row th, .total-row td {
            border-top: 2px solid #333;
            border-bottom: none;
            padding-top: 15px;
            font-size: 18px;
        }
        .total-row td {
            color: #198754;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }
        .btn-print {
            display: block;
            width: 200px;
            margin: 30px auto 0;
            padding: 12px 20px;
            background-color: #212529;
            color: #fff;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .ticket-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="ticket-container">
        <div class="header">
            <h1>JAYA FUTSAL</h1>
            <p>Bukti Reservasi Lapangan</p>
            <div class="status-badge">LUNAS / BERHASIL</div>
        </div>

        <table class="details-table">
            <tr>
                <th>ID Reservasi</th>
                <td>#RSV-{{ str_pad($reservasi->id, 5, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <th>Nama Pemesan</th>
                <td>{{ $reservasi->user->nama ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Tanggal Main</th>
                <td>{{ \Carbon\Carbon::parse($reservasi->tanggal)->format('d F Y') }}</td>
            </tr>
            <tr>
                <th>Jam Sewa</th>
                <td>{{ substr($reservasi->jam_mulai, 0, 5) }} - {{ substr($reservasi->jam_selesai, 0, 5) }} WIB</td>
            </tr>
            <tr>
                <th>Pemesanan Dibuat</th>
                <td>{{ $reservasi->created_at->format('d M Y, H:i') }}</td>
            </tr>
            <tr class="total-row">
                <th>Total Pembayaran</th>
                <td>Rp{{ number_format($reservasi->total_harga, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Terima kasih telah mempercayakan Jaya Futsal. Harap tunjukkan bukti ini kepada petugas lapangan jika diminta.</p>
            <p>&copy; {{ date('Y') }} Jaya Futsal. All rights reserved.</p>
        </div>
    </div>

    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>

    <script>
        // Auto print prompt when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>