<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan {{ $pt->nama_pt }}</title>
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
            color: #333;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: #333;
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
        
        /* Bagian komponen */
        .created-by, .pokja, .resume {
            margin-top: 8px;
            width: 100%;
        }

        .created-by p {
            margin: 0;
            color: #333;
        }
        
        .section-title {
            font-weight: bold;
            display: block;
            color: #333;
        }
        
        /* List untuk pokja */
        .pokja ul {
            margin: 5px 0;
            padding-left: 20px;
            color: #333;
        }
        
        /* Resume kegiatan */
        .resume div {
            color: #333;
        }
        
        /* Timestamp */
        .timestamp {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PEMBINAAN PERGURUAN TINGGI</h2>
        <p>{{ $pt->nama_pt }} ({{ $pt->kode_pt }})</p>
    </div>
    
    @if($laporan->isEmpty())
        <div style="text-align: center; padding: 50px 0; color: #888;">
            <p>Tidak ada data laporan yang sesuai dengan filter.</p>
        </div>
    @else
        <div style="margin-bottom: 10px; color: #333;">
            <strong>Jumlah Laporan:</strong> {{ $laporan->count() }}
        </div>
        
        @foreach($laporan as $item)
        <div class="laporan-item">
            
            <!-- Informasi dengan tabel HTML yang lebih kompatibel dengan DomPDF -->
            <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding: 5px 10px 5px 0; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 2px; color: #333;">Tanggal Kegiatan:</div>
                        <p style="margin: 0; color: #333;">{{ \Carbon\Carbon::parse($item->tanggal_kegiatan)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </td>
                    <td style="width: 50%; padding: 5px 0 5px 10px; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 2px; color: #333;">Tanggal Dibuat Laporan:</div>
                        <p style="margin: 0; color: #333;">{{ \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; padding: 5px 10px 5px 0; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 2px; color: #333;">Jenis Kegiatan:</div>
                        <p style="margin: 0; color: #333;">{{ $item->jenis_kegiatan }}</p>
                    </td>
                    <td style="width: 50%; padding: 5px 0 5px 10px; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 2px; color: #333;">Tempat Kegiatan:</div>
                        <p style="margin: 0; color: #333;">{{ $item->tempat_kegiatan }}</p>
                    </td>
                </tr>
            </table>
            
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
                <p style="color: #333;">Tidak ada anggota tim</p>
                @endif
            </div>
            
            <!-- Ringkasan Kegiatan -->
            <div class="resume">
                <span class="section-title">Ringkasan Kegiatan:</span>
                <div style="color: #333;">{!! nl2br(e($item->resume)) !!}</div>
            </div>
        </div>
        @endforeach
    @endif
    
    <div class="timestamp">Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm:ss') }} WIB</div>
</body>
</html>