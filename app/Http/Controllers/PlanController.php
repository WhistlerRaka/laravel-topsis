<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\PaketData;
use App\Models\DetailKriteria;
use App\Models\Pembagi;
use App\Models\Perankingan;
use App\Models\Perhitungan;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlanController extends Controller
{
    public function show()
    {
        $data = Plan::join('paket_data as pd', 'pd.id', 'plans.paket_id')
            ->select('plans.id', 'pd.name as nama', 'kecepatan', 'jumlah_perangkat', 'jenis_ip', 'jenis_layanan', 'rekomendasi_perangkat', 'rasio_down_up')
            ->get();

        $kecepatan = Plan::join('detail_kriterias as dk', 'dk.id', 'kecepatan')
            ->select('plans.id', 'dk.keterangan', 'poin')->get();

        $jumlah_perangkat = Plan::join('detail_kriterias as dk', 'dk.id', 'jumlah_perangkat')
            ->select('plans.id', 'dk.keterangan', 'poin')->get();

        $jenis_ip = Plan::join('detail_kriterias as dk', 'dk.id', 'jenis_ip')
            ->select('plans.id', 'dk.keterangan', 'poin')->get();

        $jenis_layanan = Plan::join('detail_kriterias as dk', 'dk.id', 'jenis_layanan')
            ->select('plans.id', 'dk.keterangan', 'poin')->get();

        $rekomendasi_perangkat = Plan::join('detail_kriterias as dk', 'dk.id', 'rekomendasi_perangkat')
            ->select('plans.id', 'dk.keterangan', 'dk.data_optional', 'poin_optional')->get();

        $rasio_down_up = Plan::join('detail_kriterias as dk', 'dk.id', 'rasio_down_up')
            ->select('plans.id', 'dk.keterangan', 'poin')->get();

        $harga = Plan::join('detail_kriterias as dk', 'dk.id', 'harga')
            ->select('plans.id', 'dk.keterangan', 'poin')->get();

        return view('pages.plan.plan', [
            'data' => $data, 'kecepatan' => $kecepatan,
            'jumlah_perangkat' => $jumlah_perangkat, 'jenis_ip' => $jenis_ip, 'jenis_layanan' => $jenis_layanan,
            'rekomendasi_perangkat' => $rekomendasi_perangkat, 'rasio_down_up' => $rasio_down_up, 'harga' => $harga
        ]);
    }

    public function createForm()
    {
        $data = PaketData::get();

        $kriteria = Kriteria::get();

        $detailKriteria = DetailKriteria::get();

        $kecepatan = Kriteria::join('detail_kriterias as dk', 'dk.kriteria_id', 'kriterias.id')
            ->where('kriterias.nama', 'Kecepatan')->get();

        return view('pages.plan.add-plan', [
            'data' => $data, 'kriteria' => $kriteria, 'detailKriteria' => $detailKriteria, 'kecepatan' => $kecepatan
        ]);
    }

    public function create(Request $request)
    {

        DB::beginTransaction();

        try {


            Plan::create([
                'paket_id' => $request->paket_id,
                'kecepatan' => $request->kecepatan,
                'jumlah_perangkat' => $request->jumlah_perangkat,
                'jenis_ip' => $request->jenis_ip,
                'jenis_layanan' => $request->jenis_layanan,
                'rekomendasi_perangkat' => $request->rekomendasi_perangkat,
                'rasio_down_up' => $request->rasio_down_up,
                'harga' => $request->harga
            ]);

            //Memulai Perhitungan

            $kriteria = Kriteria::get();
            $allPlan = Plan::get();

            app(PerhitunganController::class)->nilaiPembagi($kriteria);

            app(PerhitunganController::class)->nilaiPositifNegatif($allPlan, $kriteria);

            DB::commit();

            return redirect('/plan');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            Perankingan::where('plan_id', $id)->delete();

            Perhitungan::where('plan_id', $id)->delete();

            Plan::where('id', $id)->delete();

            //Memulai Perhitungan

            $kriteria = Kriteria::get();
            $allPlan = Plan::get();

            app(PerhitunganController::class)->nilaiPembagi($kriteria);

            app(PerhitunganController::class)->nilaiPositifNegatif($allPlan, $kriteria);

            DB::commit();

            return redirect('/plan');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function edit($id)
    {

        DB::beginTransaction();

        try {

            $data = Plan::join('paket_data as pd', 'pd.id', 'plans.paket_id')
                ->select('plans.id', 'plans.paket_id', 'pd.name as nama', 'kecepatan', 'jumlah_perangkat', 'jenis_ip', 'jenis_layanan', 'rekomendasi_perangkat', 'rasio_down_up')
                ->where('plans.id', $id)
                ->first();

            $dataPaket = PaketData::get();

            $kriteria = Kriteria::get();

            $detailKriteria = DetailKriteria::get();

            $kecepatan = Kriteria::join('detail_kriterias as dk', 'dk.kriteria_id', 'kriterias.id')
                ->where('kriterias.nama', 'Kecepatan')->get();

            DB::commit();

            return view('pages.plan.edit-plan', ['data' => $data, 'dataPaket' => $dataPaket, 'kecepatan' => $kecepatan, 'kriteria' => $kriteria, 'detailKriteria' => $detailKriteria]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $data = Plan::where('id', $id)->first();

            $data->paket_id = $request->paket_id;
            $data->kecepatan = $request->kecepatan;
            $data->jumlah_perangkat = $request->jumlah_perangkat;
            $data->jenis_ip = $request->jenis_ip;
            $data->jenis_layanan = $request->jenis_layanan;
            $data->rekomendasi_perangkat = $request->rekomendasi_perangkat;
            $data->rasio_down_up = $request->rasio_down_up;
            $data->harga = $request->harga;
            $data->save();

            //Memulai Perhitungan

            $kriteria = Kriteria::get();
            $allPlan = Plan::get();

            app(PerhitunganController::class)->nilaiPembagi($kriteria);

            app(PerhitunganController::class)->nilaiPositifNegatif($allPlan, $kriteria);

            DB::commit();

            return redirect('/plan');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function nilaiTernormalisasi()
    {

        $checkHitung = Perhitungan::where('user_id', auth()->user()->id)->first();

        if (auth()->user()->role == 'superadmin' || !$checkHitung) {

            $plan = Plan::join('paket_data as pd', 'pd.id', 'plans.paket_id')
                ->select('plans.id', 'pd.name')
                ->get();

            $data = Perhitungan::join('plans', 'plans.id', 'perhitungans.plan_id')
                ->join('kriterias', 'kriterias.id', 'perhitungans.kriteria_id')
                ->whereNull('perhitungans.user_id')
                ->select('perhitungans.plan_id', 'kriterias.nama', 'nilai_matriks_ternormalisasi')
                ->get();
        } else if ($checkHitung) {

            $plan = Plan::join('paket_data as pd', 'pd.id', 'plans.paket_id')
                ->select('plans.id', 'pd.name')
                ->get();

            $data = Perhitungan::join('plans', 'plans.id', 'perhitungans.plan_id')
                ->join('kriterias', 'kriterias.id', 'perhitungans.kriteria_id')
                ->where('perhitungans.user_id', auth()->user()->id)
                ->select('perhitungans.plan_id', 'kriterias.nama', 'nilai_matriks_ternormalisasi')
                ->get();
        }


        return view('pages.perhitungan.nilai-ternormalisasi', ['plan' => $plan, 'data' => $data]);
    }

    public function nilaiTernormalisasiBobot()
    {
        $checkHitung = Perhitungan::where('user_id', auth()->user()->id)->first();

        if (auth()->user()->role == 'superadmin' || !$checkHitung) {

            $plan = Plan::join('paket_data as pd', 'pd.id', 'plans.paket_id')
                ->select('plans.id', 'pd.name')
                ->get();

            $data = Perhitungan::join('plans', 'plans.id', 'perhitungans.plan_id')
                ->join('kriterias', 'kriterias.id', 'perhitungans.kriteria_id')
                ->whereNull('perhitungans.user_id')
                ->select('perhitungans.plan_id', 'kriterias.nama', 'nilai_ternormalisasi_terbobot')
                ->get();
        } else if ($checkHitung) {

            $plan = Plan::join('paket_data as pd', 'pd.id', 'plans.paket_id')
                ->select('plans.id', 'pd.name')
                ->get();

            $data = Perhitungan::join('plans', 'plans.id', 'perhitungans.plan_id')
                ->join('kriterias', 'kriterias.id', 'perhitungans.kriteria_id')
                ->where('perhitungans.user_id', auth()->user()->id)
                ->select('perhitungans.plan_id', 'kriterias.nama', 'nilai_ternormalisasi_terbobot')
                ->get();
        }


        return view('pages.perhitungan.nilai-ternormalisasi-terbobot', ['plan' => $plan, 'data' => $data]);
    }

    public function perankingan()
    {
        $checkRank = Perankingan::where('user_id', auth()->user()->id)->first();

        if (auth()->user()->role == 'superadmin' || !$checkRank) {

            $data = Perankingan::join('plans', 'plans.id', 'perankingans.plan_id')
                ->join('paket_data as pd', 'pd.id', 'plans.paket_id')
                ->whereNull('perankingans.user_id')
                ->select('pd.name', 'nilai_solusi_negatif', 'nilai_solusi_positif', 'preferensi', 'perangkingan')
                ->orderBy('perangkingan', 'ASC')
                ->get();
        } else if ($checkRank) {

            $data = Perankingan::join('plans', 'plans.id', 'perankingans.plan_id')
                ->join('paket_data as pd', 'pd.id', 'plans.paket_id')
                ->where('perankingans.user_id', auth()->user()->id)
                ->select('pd.name', 'nilai_solusi_negatif', 'nilai_solusi_positif', 'preferensi', 'perangkingan')
                ->orderBy('perangkingan', 'ASC')
                ->get();
        }

        return view('pages.perhitungan.perankingan', ['data' => $data]);
    }
}
