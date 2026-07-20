@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Manajemen Pengguna</h5>
        </div>
        <ul class="breadcrumb d-none d-md-flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Admin SI-JAKI</a></li>
            <li class="breadcrumb-item">Manajemen Pengguna</li>
        </ul>
    </div>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Manajemen Pengguna</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown" data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="Opsi">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('manage-users.create') }}" class="dropdown-item"><i class="feather-plus"></i>Tambah Pengguna</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Username</th>
                                {{-- <th>Email</th> --}}
                                <th>Tim Kerja</th>
                                <th>Akses</th>
                                <th>Login Terakhir</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @if(Auth::user()->isDev() || $user->akses != 'Dev')
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    {{-- <td>{{ $user->email }}</td> --}}
                                    <td>{{ $user->pokja ?: '-' }}</td>
                                    <td>
                                        @if($user->akses == 'Dev')
                                            <span class="badge bg-danger">Developer</span>
                                        @elseif($user->akses == 'Admin')
                                            <span class="badge bg-primary">Admin</span>
                                        @else
                                            <span class="badge bg-success">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->last_login)
                                            {{ \Carbon\Carbon::parse($user->last_login)->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY | HH:mm') }}
                                        @else
                                            Belum pernah login
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('manage-users.edit', $user->uuid) }}" class="avatar-text avatar-md">
                                                <i class="feather-edit"></i>
                                            </a>
                                            @if(!($user->akses == 'Dev' || $user->akses == 'Admin'))
                                                <form id="delete-form-{{ $user->id }}" action="{{ route('manage-users.destroy', $user->uuid) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <a href="javascript:void(0);" class="avatar-text avatar-md" onclick="if(confirm('Yakin ingin menghapus pengguna ini?')) { document.getElementById('delete-form-{{ $user->id }}').submit(); }">
                                                    <i class="feather-trash-2"></i>
                                                </a>
                                            @endif                            
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection