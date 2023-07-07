@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Detail Kritera'])

<div class="container-fluid py-4">
    <div class="row">
        @if (auth()->user()->role == 'superadmin')
        <div class="col-md-6">
            <div class="card">
                <form role="form" method="POST" action={{ route('kriteria.addDetail') }} enctype="multipart/form-data">
                    @csrf
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">Detail Kriteria {{$data->nama}}</p>
                            <button type="submit" class="btn btn-primary btn-sm ms-auto">Save</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Poin</label>
                                    <input class="form-control" type="number" name="poin" value="">
                                </div>
                            </div>
                            <input class="form-control" type="hidden" name="kriteria_id" value="{{$data->id}}">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Keterangan</label>
                                    <input class="form-control" type="text" name="keterangan" value="">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Poin Optional</label>
                                    <input class="form-control" type="text" name="poin_optional" value="">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Optional</label>
                                    <input class="form-control" type="text" name="data_optional" value="">
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        @endif
        <div class="col-md-6">
            <div class="card">
                <div style="padding: 20px 0px;" class="table-responsive">
                    <table id="myTable" class="table align-items-center mb-0 table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Poin</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Keterangan</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Optional</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- @if (count($detail) > 0) -->
                            @foreach ($detail as $d)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-xs text-center">{{ $d->poin }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-xs text-center">{{ $d->keterangan }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-xs text-center">{{ $d->data_optional }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <a href="/kriteria/edit/detail/{{ $d->id }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                                        Edit
                                    </a> |
                                    <a href="/kriteria/hapus/detail/{{ $d->id }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            <!-- @else
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-xs text-center">Not found</h6>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle">
                                </td>
                            </tr>
                            @endif -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth.footer')
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" />
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>