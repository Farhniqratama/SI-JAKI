<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan {{ $pt->nama_pt }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            margin: 0;
            font-size: 14px;
        }
        
        /* Grid layout untuk info laporan */
        .info-grid {
            display: block;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-row {
            width: 100%;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .info-left {
            float: left;
            width: 48%;
        }
        .info-right {
            float: right;
            width: 48%;
        }
        
        .info-label {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .info-value {
            margin: 0;
        }
        
        /* Komponen laporan */
        .laporan-item {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #c9c9c9;
            page-break-inside: avoid;
        }
        .laporan-item:last-child {
            border-bottom: none;
        }
        .laporan-title {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 5px;
            font-weight: bold;
        }
        
        /* Bagian komponen */
        .created-by, .pokja, .resume {
            margin-top: 8px;
            width: 100%;
        }

        .created-by p {
            margin: 0;
        }
        
        .section-title {
            font-weight: bold;
            display: block;
        }
        
        /* List untuk pokja */
        .pokja ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        /* Resume kegiatan */
        /* .resume {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #eee;
        } */
        
        /* Button controls */
        .no-print {
            margin-bottom: 20px;
        }
        .no-print button {
            padding: 8px 15px;
            margin-right: 10px;
            background-color: #4c6ef5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .no-print button:hover {
            background-color: #3b5bdb;
        }
        .no-print button:last-child {
            background-color: #868e96;
        }
        .no-print button:last-child:hover {
            background-color: #495057;
        }
        
        /* Timestamp */
        .timestamp {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #888;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 15px;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak Sekarang</button>
        <button onclick="window.close()">Tutup</button>
    </div>
    
    <div class="header">
        <h2>LAPORAN PEMBINAAN PERGURUAN TINGGI</h2>
        <p>{{ $pt->nama_pt }} ({{ $pt->kode_pt }})</p>
    </div>
    
    @if($laporan->isEmpty())
        <div style="text-align: center; padding: 50px 0; color: #888;">
            <p>Tidak ada data laporan yang sesuai dengan filter.</p>
        </div>
    @else
        <div style="margin-bottom: 10px;">
            <strong>Jumlah Laporan:</strong> {{ $laporan->count() }}
        </div>
        
        @foreach($laporan as $item)
        <div class="laporan-item">
            
            <div class="info-grid">
                <!-- Baris pertama: Tanggal kegiatan dan Tanggal dibuat -->
                <div class="info-row">
                    <div class="info-left">
                        <div class="info-label">Tanggal Kegiatan:</div>
                        <p class="info-value">{{ \Carbon\Carbon::parse($item->tanggal_kegiatan)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                    <div class="info-right">
                        <div class="info-label">Tanggal Dibuat Laporan:</div>
                        <p class="info-value">{{ \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
                    </div>
                </div>
                
                <!-- Baris kedua: Jenis kegiatan dan Tempat kegiatan -->
                <div class="info-row">
                    <div class="info-left">
                        <div class="info-label">Jenis Kegiatan:</div>
                        <p class="info-value">{{ $item->jenis_kegiatan }}</p>
                    </div>
                    <div class="info-right">
                        <div class="info-label">Tempat Kegiatan:</div>
                        <p class="info-value">{{ $item->tempat_kegiatan }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Dibuat oleh -->
            <div class="created-by">
                <span class="section-title">Dibuat Oleh:</span>
                <p>{{ $item->created_by }}</p>
            </div>
            
            <!-- Lingkup Tim Kerja -->
            <div class="pokja">
                <span class="section-title">Lingkup Tim Kerja:</span>
                @if(!empty($item->pokja) && count($item->pokja) > 0)
                <ul>
                    @foreach($item->pokja as $userId)
                        @php
                            $pokjaUser = App\Models\User::find($userId);
                        @endphp
                        @if($pokjaUser)
                        <li>{{ $pokjaUser->pokja }}</li>
                        @endif
                    @endforeach
                </ul>
                @else
                <p>Tidak ada anggota tim</p>
                @endif
            </div>
            
            <!-- Ringkasan Kegiatan -->
            <div class="resume">
                <span class="section-title">Ringkasan Kegiatan:</span>
                <div>{!! $item->resume !!}</div>
            </div>
        </div>
        @endforeach
    @endif
    
    <div class="timestamp">Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm:ss') }} WIB</div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Delay untuk memastikan halaman ter-render dengan baik
            setTimeout(function() {
                // Ini bisa diuncomment jika ingin langsung cetak saat halaman dibuka
                // window.print();
            }, 1000);
        };
    </script>
</body>
</html>