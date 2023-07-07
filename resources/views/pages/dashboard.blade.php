@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Paket Internet</p>
                                <h5 style="font-size:16px;" class="font-weight-bolder">
                                    {{$plan}}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total User</p>
                                <h5 style="font-size:16px;" class="font-weight-bolder">
                                    {{$user}}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Top Alternatif</p>
                                <h5 style="font-size:16px;" class="font-weight-bolder">
                                    @if (count($topOne) > 0)
                                    {{$topOne[0]->name}}
                                    @endif
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Sering digunakan</p>
                                <h5 style="font-size:16px;" class="font-weight-bolder">
                                    @if ($middle)
                                    {{$middle->name}}
                                    @endif
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paket Data Home --> -->

    <!-- <div class="row mt-4">
        <div class="col-xl-12 col-lg-5 col-md-7 mx-auto">
            <div class="card z-index-0">
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Ranking</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Kecepatan</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Jumlah Perangkat</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Jenis IP</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Jenis Layanan</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Rekomendasi Perangkat</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Download / Upload</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($data) > 0)
                                @foreach ($data as $p)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$p->name}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$p->perangkingan}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach ($kecepatan as $k)
                                    @if ($p->id == $k->id)
                                    <td>
                                        <div class="px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$k->keterangan}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    @endforeach
                                    @foreach ($jumlah_perangkat as $k)
                                    @if ($p->id == $k->id)
                                    <td>
                                        <div class="px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$k->keterangan}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    @endforeach
                                    @foreach ($jenis_ip as $k)
                                    @if ($p->id == $k->id)
                                    <td>
                                        <div class="px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$k->keterangan}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    @endforeach
                                    @foreach ($jenis_layanan as $k)
                                    @if ($p->id == $k->id)
                                    <td>
                                        <div class="px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$k->keterangan}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    @endforeach
                                    @foreach ($rekomendasi_perangkat as $k)
                                    @if ($p->id == $k->id)
                                    <td>
                                        <div class="px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$k->data_optional}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    @endforeach
                                    @foreach ($rasio_down_up as $k)
                                    @if ($p->id == $k->id)
                                    <td>
                                        <div class="px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$k->keterangan}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    @endforeach
                                    @foreach ($harga as $k)
                                    @if ($p->id == $k->id)
                                    <td>
                                        <div class="px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-center text-sm">{{$k->keterangan}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    @endforeach
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
            </div>
        </div>
    </div> -->

    <!-- // Top 5 Paket Internet and Kategori -->

    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card ">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Top 5 Paket Internet</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center ">
                        <tbody>

                            @if (count($topFive) > 0)
                            @foreach ($topFive as $f)
                            <tr>
                                <td class="w-30">
                                    <div class="d-flex px-2 py-1 align-items-center">
                                        <div class="ms-0">
                                            <p class="text-xs font-weight-bold mb-0">Paket Internet :</p>
                                            <h6 class="text-sm mb-0">{{$f->name}}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">Preferensi:</p>
                                        <h6 class="text-sm mb-0">{{$f->preferensi}}</h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">Ranking:</p>
                                        <h6 class="text-sm mb-0">{{$f->perangkingan}}</h6>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td>
                                    <div class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">Not Found</p>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Kriteria</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @if (count($kriteria) > 0)
                        @foreach ($kriteria as $k)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-tag text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">{{$k->nama}}</h6>
                                </div>
                            </div>
                        </li>
                        @endforeach
                        @else
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-tag text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Not Found</h6>
                                </div>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>



    @include('layouts.footers.auth.footer')
</div>
@endsection

@push('js')
<script src="./assets/js/plugins/chartjs.min.js"></script>
<script>
    var ctx1 = document.getElementById("chart-line").getContext("2d");

    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(251, 99, 64, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(251, 99, 64, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(251, 99, 64, 0)');
    new Chart(ctx1, {
        type: "line",
        data: {
            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Mobile apps",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#fb6340",
                backgroundColor: gradientStroke1,
                borderWidth: 3,
                fill: true,
                data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                maxBarThickness: 6

            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#fbfbfb',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#ccc',
                        padding: 20,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });
</script>
@endpush