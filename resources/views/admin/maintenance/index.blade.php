@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>Pengaturan Mode Maintenance</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="mb-4">
                <h5>Status Saat Ini:</h5>
                <div class="p-3 bg-light rounded">
                    <p class="mb-1"><strong>Status:</strong>
                        @if($maintenance && $maintenance->is_active)
                            <span class="badge bg-danger">Aktif</span>
                        @else
                            <span class="badge bg-success">Tidak Aktif</span>
                        @endif
                    </p>

                    @if($maintenance && $maintenance->is_active)
                        <p class="mb-1"><strong>Tipe:</strong>
                            @if($maintenance->type === 'maintenance')
                                <span class="badge bg-warning">Under Maintenance</span>
                            @else
                                <span class="badge bg-info">Under Construction</span>
                            @endif
                        </p>
                    @endif

                    @if($maintenance && $maintenance->is_active && $maintenance->end_time)
                        <p class="mb-0"><strong>Berakhir pada:</strong> {{ $maintenance->end_time->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>
            
            <div class="mb-4 d-flex gap-2">
                @if($maintenance && $maintenance->is_active)
                    <form action="{{ route('admin.maintenance.end') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning">Akhiri Maintenance</button>
                    </form>
                @endif
                
                @if($maintenanceCount > 1)
                    <form action="{{ route('admin.maintenance.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus History</button>
                    </form>
                @endif
            </div>
            
            <form action="{{ route('admin.maintenance.update') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Status Maintenance</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="active_yes" value="1">
                        <label class="form-check-label" for="active_yes">
                            Aktifkan Mode Maintenance
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="active_no" value="0" checked>
                        <label class="form-check-label" for="active_no">
                            Nonaktifkan Mode Maintenance
                        </label>
                    </div>
                </div>

                <div class="mb-3" id="type-setting">
                    <label class="form-label">Tipe Mode</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type_maintenance" value="maintenance" checked>
                        <label class="form-check-label" for="type_maintenance">
                            Under Maintenance (Pemeliharaan Website)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type_construction" value="construction">
                        <label class="form-check-label" for="type_construction">
                            Under Construction (Website Dalam Pembangunan)
                        </label>
                    </div>
                </div>
                
                <div class="row mb-3" id="timer-setting">
                    <div class="col-md-8">
                        <label class="form-label">Durasi Maintenance</label>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="days" id="days" min="0" value="0">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="hours" id="hours" min="0" value="1">
                                    <span class="input-group-text">Jam</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="minutes" id="minutes" min="0" max="59" value="0">
                                    <span class="input-group-text">Menit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </form>
            
            @if($maintenanceCount > 0)
                <div class="mt-4">
                    <h5>Histori Maintenance:</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Status</th>
                                    <th>Tipe</th>
                                    <th>Waktu Mulai</th>
                                    <th>Waktu Berakhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(App\Models\MaintenanceMode::latest()->take(10)->get() as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="badge bg-danger">Aktif</span>
                                        @else
                                            <span class="badge bg-success">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->type === 'maintenance')
                                            <span class="badge bg-warning">Maintenance</span>
                                        @else
                                            <span class="badge bg-info">Construction</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $item->end_time ? $item->end_time->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activeYes = document.getElementById('active_yes');
        const activeNo = document.getElementById('active_no');
        const timerSetting = document.getElementById('timer-setting');
        const typeSetting = document.getElementById('type-setting');

        function toggleSettings() {
            if (activeYes.checked) {
                timerSetting.style.display = 'block';
                typeSetting.style.display = 'block';
            } else {
                timerSetting.style.display = 'none';
                typeSetting.style.display = 'none';
            }
        }

        activeYes.addEventListener('change', toggleSettings);
        activeNo.addEventListener('change', toggleSettings);

        // Inisialisasi
        toggleSettings();
    });
</script>
@endsection