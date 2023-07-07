<?php

namespace App\Http\Controllers;

use App\Models\DetailKriteria;
use App\Models\Kriteria;
use App\Models\KriteriaUser;
use App\Models\Pembagi;
use App\Models\Perankingan;
use App\Models\Perhitungan;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KriteriaController extends Controller
{
    public function show()
    {
        // $data = Kriteria::get();

        $data = Kriteria::leftJoin('kriteria_users as ku', 'ku.kriteria_id', 'kriterias.id')
            ->select(
                'kriterias.id',
                'kriterias.bobot',
                'ku.bobot_kriteria',
                'kriterias.nama',
                'kriterias.kode',
                'kriterias.sifat'
            )
            ->get();

        return view('pages.kriteria.kriteria', ['data' => $data]);
    }

    public function formCreate()
    {
        return view('pages.kriteria.add-kriteria');
    }

    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'bobot' => 'required|numeric',
            'sifat' => 'required',
        ]);

        $getKriteria = Kriteria::orderBy('id', 'desc')->first();

        if ($getKriteria) {

            $lastIncreament = substr($getKriteria->kode, -3);
            $newKode = 'KRT' . str_pad($lastIncreament + 1, 3, 0, STR_PAD_LEFT);
        } else {

            $newKode = 'KRT001';
        }

        Kriteria::create([
            'kode' => $newKode,
            'nama' => $request->nama,
            'bobot' => $request->bobot,
            'sifat' => $request->sifat
        ]);

        return redirect('/kriteria');
    }

    public function delete($id)
    {
        DetailKriteria::where('kriteria_id', $id)->delete();

        Kriteria::where('id', $id)->delete();

        return redirect('/kriteria');
    }

    public function edit($id)
    {
        $data = Kriteria::where('id', $id)->first();

        $dataKriteria = KriteriaUser::where('kriteria_id', $id)->where('user_id', auth()->user()->id)->first();

        return view('pages.kriteria.edit-kriteria', ['data' => $data, 'dataKriteria' => $dataKriteria]);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            if (auth()->user()->role == 'superadmin') {
                $data = Kriteria::where('id', $id)->first();

                $data->nama = $request->nama;
                $data->bobot = $request->bobot;
                $data->sifat = $request->sifat;
                $data->save();
            } else {
                $dataKriteria = KriteriaUser::where('kriteria_id', $id)->where('user_id', auth()->user()->id)->first();

                if ($dataKriteria) {
                    $dataKriteria->bobot_kriteria = $request->bobot_kriteria;
                    $dataKriteria->save();
                } else {
                    KriteriaUser::create([
                        'kriteria_id' => $id,
                        'bobot_kriteria' => $request->bobot_kriteria,
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }

            //Memulai Perhitungan

            $kriteria = Kriteria::get();
            $allPlan = Plan::get();

            app(PerhitunganController::class)->nilaiPembagi($kriteria);

            app(PerhitunganController::class)->nilaiPositifNegatif($allPlan, $kriteria);

            DB::commit();

            $data = Kriteria::leftJoin('kriteria_users as ku', 'ku.kriteria_id', 'kriterias.id')
                ->select(
                    'kriterias.id',
                    'kriterias.bobot',
                    'ku.bobot_kriteria',
                    'kriterias.nama',
                    'kriterias.kode',
                    'kriterias.sifat'
                )
                ->get();

            return view('pages.kriteria.kriteria', ['data' => $data]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function detail($id)
    {
        $data = Kriteria::where('id', $id)->first();

        $detail = DetailKriteria::where('kriteria_id', $id)->get();

        return view('pages.kriteria.detail-kriteria', ['data' => $data, 'detail' => $detail]);
    }

    public function addDetail(Request $request)
    {
        $request->validate([
            'poin' => 'required|numeric',
            'keterangan' => 'required',
        ]);

        DetailKriteria::create([
            'kriteria_id' => $request->kriteria_id,
            'poin' => $request->poin,
            'keterangan' => $request->keterangan,
            'poin_optional' => $request->poin_optional,
            'data_optional' => $request->data_optional
        ]);

        return redirect()->back();
    }

    public function editDetail($id)
    {
        $kriteria = DetailKriteria::where('id', $id)->first();

        $data = Kriteria::where('id', $kriteria->kriteria_id)->first();

        $detail = DetailKriteria::where('kriteria_id', $kriteria->kriteria_id)->get();

        return view('pages.kriteria.edit-detail-kriteria', ['data' => $data, 'detail' => $detail, 'kriteria' => $kriteria]);
    }

    public function updateDetail(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $data = DetailKriteria::where('id', $id)->first();

            $data->poin = $request->poin;
            $data->keterangan = $request->keterangan;
            $data->poin_optional = $request->poin_optional;
            $data->data_optional = $request->data_optional;
            $data->save();

            if ($request->poin || $request->poin_optional) {

                //Memulai Perhitungan

                $kriteria = Kriteria::get();
                $allPlan = Plan::get();

                app(PerhitunganController::class)->nilaiPembagi($kriteria);

                app(PerhitunganController::class)->nilaiPositifNegatif($allPlan, $kriteria);
            }

            DB::commit();


            return redirect()->route('kriteria.detail', $data->kriteria_id);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function deleteDetail($id)
    {
        DB::beginTransaction();

        try {

            $detail = DetailKriteria::where('id', $id)->first();

            $kriteria = Kriteria::where('id', $detail->kriteria_id)->first();

            $checkData = Plan::join('detail_kriterias as dk', 'dk.id', str_replace(array(' ', '/'), '_', strtolower($kriteria->nama)))
                ->where(str_replace(array(' ', '/'), '_', strtolower($kriteria->nama)), $id)
                ->update([str_replace(array(' ', '/'), '_', strtolower($kriteria->nama)) => null]);

            //Memulai Perhitungan

            $kriteria = Kriteria::get();
            $allPlan = Plan::get();

            app(PerhitunganController::class)->nilaiPembagi($kriteria);

            app(PerhitunganController::class)->nilaiPositifNegatif($allPlan, $kriteria);

            $detail->delete();

            DB::commit();

            return redirect()->back();
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }
}
