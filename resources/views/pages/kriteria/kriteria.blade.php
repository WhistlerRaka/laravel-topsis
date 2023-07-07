@extends('layouts.app')

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Kriteria'])
<div class="row mt-4 mx-4">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center">
                    <h6>Kriteria</h6>
                    <!-- <a href="{{ route('kriteria.form') }}" class="btn btn-primary btn-sm ms-auto">Add New</a> -->
                    @if (auth()->user()->role == 'superadmin')
                        <button type="button" class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Add New
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table id="myTable" class="table align-items-center mb-0 table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kode</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Bobot</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Sifat</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($data) > 0)
                            @foreach ($data as $d)
                            <tr>
                                <td>
                                    <div class="d-flex px-3 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{$d->kode}}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0">{{$d->nama}}</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    @if (auth()->user()->role == 'superadmin')
                                    <p class="text-sm font-weight-bold mb-0">{{$d->bobot}}</p>
                                    @elseif (auth()->user()->role != 'superadmin' && $d->bobot_kriteria)
                                    <p class="text-sm font-weight-bold mb-0">{{$d->bobot_kriteria}}</p>
                                    @else
                                    <p class="text-sm font-weight-bold mb-0">{{$d->bobot}}</p>
                                    @endif
                                    
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <p class="text-sm font-weight-bold mb-0">{{$d->sifat}}</p>
                                </td>
                                <td class="align-middle text-end">
                                    <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                        <p class="text-sm font-weight-bold mb-0"><a href="/kriteria/edit/{{ $d->id }}">Edit</a></p>
                                        <!-- <p class="text-sm font-weight-bold mb-0 ps-2"><a href="/kriteria/hapus/{{ $d->id }}">Delete</a></p> -->
                                        <p class="text-sm font-weight-bold mb-0 ps-2"><a href="/kriteria/detail/{{ $d->id }}">Detail</a></p>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td>
                                    <div class="d-flex px-3 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Not Found</h6>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add New Kriteria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form" method="POST" action="{{ route('kriteria.create') }}" enctype="multipart/form-data">
                        <div class="modal-body">
                            
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="example-text-input" class="form-control-label">Nama Kriteria</label>
                                                <input class="form-control" type="text" name="nama" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="example-text-input" class="form-control-label">Bobot</label>
                                                <input class="form-control" type="number" name="bobot" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="example-text-input" class="form-control-label">Sifat Kriteria</label>
                                                <select class="form-select" name="sifat" aria-label="Default select example">
                                                    <option selected disabled>Pilih Sifat Kriteria</option>
                                                    <option value="Benefit">Benefit</option>
                                                    <option value="Cost">Cost</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <!-- <a href="{{ route('kriteria.create') }}" class="btn bg-gradient-primary">Save changes</a> -->
                            <button type="submit" class="btn bg-gradient-primary">Save changes</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection