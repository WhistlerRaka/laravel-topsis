<?php

namespace App\Http\Controllers;

use App\Models\PaketData;
use App\Models\Plan;
use App\Models\Perhitungan;
use App\Models\Perankingan;
use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\DetailKriteria;
use App\Models\Pembagi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redirect;

class PaketDataController extends Controller
{
    public function show()
    {
        $data = PaketData::get();

        return view('pages.paket-data', ['data' => $data]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        PaketData::create([
            'name' => $request->name
        ]);

        return redirect('/paket-data');
    }

    public function edit($id)
    {
        $dataPaket = PaketData::where('id', $id)->first();

        $data = PaketData::get();

        return view('pages.edit-paket-data', ['data' => $data, 'dataPaket' => $dataPaket]);
    }

    public function update(Request $request, $id)
    {
        $dataPaket = PaketData::where('id', $id)->first();

        $dataPaket->name = $request->name;
        $dataPaket->save();

        return redirect('/paket-data');
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $deletePlan = Plan::where('paket_id', $id)->get();

            foreach ($deletePlan as $d => $val) {
                Perhitungan::where('plan_id', $deletePlan[$d]->id)->delete();
                Perankingan::where('plan_id', $deletePlan[$d]->id)->delete();
            }

            $deletePlan->each->delete();

            PaketData::where('id', $id)->delete();

            //Memulai Perhitungan

            $kriteria = Kriteria::get();
            $allPlan = Plan::get();

            app(PerhitunganController::class)->nilaiPembagi($kriteria);

            app(PerhitunganController::class)->nilaiPositifNegatif($allPlan, $kriteria);

            DB::commit();

            return redirect('/paket-data');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }
}
