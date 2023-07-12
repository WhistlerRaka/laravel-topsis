<?php

namespace App\Http\Controllers;

use App\Models\KriteriaUser;
use App\Models\Pembagi;
use App\Models\Perankingan;
use App\Models\Perhitungan;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PerhitunganController extends Controller
{
    //

    // public function nilaiPembagi($kriteria)
    // {
    //     $columns = Schema::getColumnListing('plans');

    //     foreach ($kriteria as $i => $val) {
    //         foreach ($columns as $c => $val) {

    //             if (
    //                 str_replace(array(' ', '/'), '_', strtolower($kriteria[$i]->nama)) == $columns[$c]
    //                 && $columns[$c] != 'created_at' && $columns[$c] != 'updated_at' && $columns[$c] != 'id'
    //             ) {

    //                 //Mencari nilai pembagi

    //                 if ($columns[$c] == 'rekomendasi_perangkat') {
    //                     $plan = Plan::join('detail_kriterias as dk', 'dk.id', $columns[$c])
    //                         ->select(DB::raw("(sum(pow(dk.poin_optional,2))) AS amount"))
    //                         ->first();
    //                 } else {
    //                     $plan = Plan::join('detail_kriterias as dk', 'dk.id', $columns[$c])
    //                         // ->select($columns[$c], 'dk.poin')
    //                         ->select(DB::raw("(sum(pow(dk.poin,2))) AS amount"))
    //                         ->first();
    //                 }
    //             }
    //         }

            if (auth()->user()->role == 'superadmin') {
                $pembagi = Pembagi::where('kriteria_id', $kriteria[$i]->id)->first();

                if ($pembagi) {
                    $pembagi->nilai = (float)sqrt($plan->amount);
                    $pembagi->save();
                } else {
                    Pembagi::create([
                        'kriteria_id' => $kriteria[$i]->id,
                        'nilai' => (float)sqrt($plan->amount)
                    ]);
                }
            } else {
                $pembagi = Pembagi::where('kriteria_id', $kriteria[$i]->id)->where('user_id', auth()->user()->id)->first();

                if ($pembagi) {
                    $pembagi->nilai = (float)sqrt($plan->amount);
                    $pembagi->user_id = auth()->user()->id;
                    $pembagi->save();
                } else {
                    Pembagi::create([
                        'kriteria_id' => $kriteria[$i]->id,
                        'nilai' => (float)sqrt($plan->amount),
                        'user_id' => auth()->user()->id
                    ]);
                }
            }



            $namaCol = str_replace(array(' ', '/'), '_', strtolower($kriteria[$i]->nama));
            $allPlan = Plan::join('detail_kriterias as dk', 'dk.id', $namaCol)->select("$namaCol", 'plans.id', 'dk.poin', 'dk.poin_optional')->get();

            $forK = $kriteria[$i];

            $normal = app(PerhitunganController::class)->nilaiMatriksNormalBobot($allPlan, $namaCol, $plan, $forK);
            $nilaiMaxMin = app(PerhitunganController::class)->nilaiMaxMin($forK);
        }
    }

    public function nilaiMatriksNormalBobot($allPlan, $namaCol, $plan, $forK)
    {

        //Mencari nilai matriks ternormalisasi

        $kriteriaUser = KriteriaUser::where('kriteria_id', $forK->id)->where('user_id', auth()->user()->id)->first();

        foreach ($allPlan as $a => $valu) {

            //mencari nilai ternormalisasi & nilai ternormalisasi terbobot

            if (auth()->user()->role == 'superadmin') {

                $perhitungan = Perhitungan::where('plan_id', $allPlan[$a]->id)->whereNull('user_id')->where('kriteria_id', $forK->id)->first();

                if ($perhitungan) {

                    if ($namaCol == 'rekomendasi_perangkat') {
                        $perhitungan->nilai_matriks_ternormalisasi = (float)$allPlan[$a]->poin_optional / sqrt($plan->amount);
                        $perhitungan->nilai_ternormalisasi_terbobot = ((float)$allPlan[$a]->poin_optional / sqrt($plan->amount)) * $forK->bobot;
                        $perhitungan->save();
                    } else {
                        $perhitungan->nilai_matriks_ternormalisasi = (float)$allPlan[$a]->poin / sqrt($plan->amount);
                        $perhitungan->nilai_ternormalisasi_terbobot = ((float)$allPlan[$a]->poin / sqrt($plan->amount)) * $forK->bobot;
                        $perhitungan->save();
                    }
                } else {

                    if ($namaCol == 'rekomendasi_perangkat') {
                        Perhitungan::create([
                            'plan_id' => $allPlan[$a]->id,
                            'kriteria_id' => $forK->id,
                            'nilai_matriks_ternormalisasi' => (float)$allPlan[$a]->poin_optional / sqrt($plan->amount),
                            'nilai_ternormalisasi_terbobot' => ((float)$allPlan[$a]->poin_optional / sqrt($plan->amount)) * $forK->bobot,
                        ]);
                    } else {
                        Perhitungan::create([
                            'plan_id' => $allPlan[$a]->id,
                            'kriteria_id' => $forK->id,
                            'nilai_matriks_ternormalisasi' => (float)$allPlan[$a]->poin / sqrt($plan->amount),
                            'nilai_ternormalisasi_terbobot' => ((float)$allPlan[$a]->poin / sqrt($plan->amount)) * $forK->bobot,
                        ]);
                    }
                }
            } else if ($kriteriaUser) {

                $perhitungan = Perhitungan::where('plan_id', $allPlan[$a]->id)->where('user_id', auth()->user()->id)->where('kriteria_id', $forK->id)->first();

                if ($perhitungan) {

                    if ($namaCol == 'rekomendasi_perangkat') {
                        $perhitungan->nilai_matriks_ternormalisasi = (float)$allPlan[$a]->poin_optional / sqrt($plan->amount);
                        $perhitungan->nilai_ternormalisasi_terbobot = ((float)$allPlan[$a]->poin_optional / sqrt($plan->amount)) * $kriteriaUser->bobot_kriteria;
                        $perhitungan->user_id = auth()->user()->id;
                        $perhitungan->save();
                    } else {
                        $perhitungan->nilai_matriks_ternormalisasi = (float)$allPlan[$a]->poin / sqrt($plan->amount);
                        $perhitungan->nilai_ternormalisasi_terbobot = ((float)$allPlan[$a]->poin / sqrt($plan->amount)) * $kriteriaUser->bobot_kriteria;
                        $perhitungan->user_id = auth()->user()->id;
                        $perhitungan->save();
                    }
                } else {



                    if ($namaCol == 'rekomendasi_perangkat') {
                        Perhitungan::create([
                            'plan_id' => $allPlan[$a]->id,
                            'kriteria_id' => $kriteriaUser->kriteria_id,
                            'nilai_matriks_ternormalisasi' => (float)$allPlan[$a]->poin_optional / sqrt($plan->amount),
                            'nilai_ternormalisasi_terbobot' => ((float)$allPlan[$a]->poin_optional / sqrt($plan->amount)) * $kriteriaUser->bobot_kriteria,
                            'user_id' => auth()->user()->id
                        ]);
                    } else {
                        Perhitungan::create([
                            'plan_id' => $allPlan[$a]->id,
                            'kriteria_id' => $kriteriaUser->kriteria_id,
                            'nilai_matriks_ternormalisasi' => (float)$allPlan[$a]->poin / sqrt($plan->amount),
                            'nilai_ternormalisasi_terbobot' => ((float)$allPlan[$a]->poin / sqrt($plan->amount)) * $kriteriaUser->bobot_kriteria,
                            'user_id' => auth()->user()->id
                        ]);
                    }
                }
            } else {

                $perhitungan = Perhitungan::where('plan_id', $allPlan[$a]->id)->where('user_id', auth()->user()->id)->where('kriteria_id', $forK->id)->first();

                if ($perhitungan) {

                    if ($namaCol == 'rekomendasi_perangkat') {
                        $perhitungan->nilai_matriks_ternormalisasi = (float)$allPlan[$a]->poin_optional / sqrt($plan->amount);
                        $perhitungan->nilai_ternormalisasi_terbobot = ((float)$allPlan[$a]->poin_optional / sqrt($plan->amount)) * $forK->bobot;
                        $perhitungan->user_id = auth()->user()->id;
                        $perhitungan->save();
                    } else {
                        $perhitungan->nilai_matriks_ternormalisasi = (float)$allPlan[$a]->poin / sqrt($plan->amount);
                        $perhitungan->nilai_ternormalisasi_terbobot = ((float)$allPlan[$a]->poin / sqrt($plan->amount)) * $forK->bobot;
                        $perhitungan->user_id = auth()->user()->id;
                        $perhitungan->save();
                    }
                } else {



                    if ($namaCol == 'rekomendasi_perangkat') {
                        Perhitungan::create([
                            'plan_id' => $allPlan[$a]->id,
                            'kriteria_id' => $forK->id,
                            'nilai_matriks_ternormalisasi' => (float)$allPlan[$a]->poin_optional / sqrt($plan->amount),
                            'nilai_ternormalisasi_terbobot' => ((float)$allPlan[$a]->poin_optional / sqrt($plan->amount)) * $forK->bobot,
                            'user_id' => auth()->user()->id
                        ]);
                    } else {
                        Perhitungan::create([
                            'plan_id' => $allPlan[$a]->id,
                            'kriteria_id' => $forK->id,
                            'nilai_matriks_ternormalisasi' => (float)$allPlan[$a]->poin / sqrt($plan->amount),
                            'nilai_ternormalisasi_terbobot' => ((float)$allPlan[$a]->poin / sqrt($plan->amount)) * $forK->bobot,
                            'user_id' => auth()->user()->id
                        ]);
                    }
                }
            }
        }
    }

    public function nilaiMaxMin($forK)
    {
        //Mencari nilai max dan nilai min

        $addMaxMin = Pembagi::where('kriteria_id', $forK->id)->whereNull('user_id')->first();

        if (auth()->user()->role == 'superadmin') {

            if ($addMaxMin) {
                $addMaxMin->nilai_min = (float)Perhitungan::where('kriteria_id', $forK->id)->whereNull('perhitungans.user_id')->min('nilai_ternormalisasi_terbobot');
                $addMaxMin->nilai_max = (float)Perhitungan::where('kriteria_id', $forK->id)->whereNull('perhitungans.user_id')->max('nilai_ternormalisasi_terbobot');
                $addMaxMin->save();
            }
        } else {

            $addMaxMinUser = Pembagi::where('kriteria_id', $forK->id)->where('user_id', auth()->user()->id)->first();

            if ($addMaxMinUser) {
                $addMaxMinUser->nilai_min = (float)Perhitungan::where('kriteria_id', $forK->id)->where('perhitungans.user_id', auth()->user()->id)->min('nilai_ternormalisasi_terbobot');
                $addMaxMinUser->nilai_max = (float)Perhitungan::where('kriteria_id', $forK->id)->where('perhitungans.user_id', auth()->user()->id)->max('nilai_ternormalisasi_terbobot');
                $addMaxMinUser->save();
            } else {
                Pembagi::create([
                    'kriteria_id' => $forK->id,
                    'nilai' => $addMaxMin->pembagi,
                    'nilai_min' => (float)Perhitungan::where('kriteria_id', $forK->id)->where('perhitungans.user_id', auth()->user()->id)->min('nilai_ternormalisasi_terbobot'),
                    'nilai_max' => (float)Perhitungan::where('kriteria_id', $forK->id)->where('perhitungans.user_id', auth()->user()->id)->max('nilai_ternormalisasi_terbobot'),
                    'user_id' => auth()->user()->id,
                ]);
            }
        }
    }

    public function nilaiPositifNegatif($allPlan, $kriteria)
    {
        foreach ($allPlan as $a => $val) {

            $nilaiPositif = (float)0;
            $nilaiNegatif = (float)0;

            foreach ($kriteria as $i => $val) {

                if (auth()->user()->role == 'superadmin') {
                    $nilaiMin = (float)Perhitungan::where('kriteria_id', $kriteria[$i]->id)->whereNull('user_id')->min('nilai_ternormalisasi_terbobot');
                    $nilaiMax = (float)Perhitungan::where('kriteria_id', $kriteria[$i]->id)->whereNull('user_id')->max('nilai_ternormalisasi_terbobot');

                    $pos = Perhitungan::where('plan_id', $allPlan[$a]->id)->whereNull('user_id')->where('kriteria_id', $kriteria[$i]->id)->select(DB::raw("(pow(($nilaiMax - nilai_ternormalisasi_terbobot),2)) AS nilaiPos"))->first();
                    $neg = Perhitungan::where('plan_id', $allPlan[$a]->id)->whereNull('user_id')->where('kriteria_id', $kriteria[$i]->id)->select(DB::raw("(pow((nilai_ternormalisasi_terbobot - $nilaiMin),2)) AS nilaiNeg"))->first();

                    $nilaiPositif += (float)$pos->nilaiPos;
                    $nilaiNegatif += (float)$neg->nilaiNeg;
                } else {
                    $perhitungan = Perhitungan::where('kriteria_id', $kriteria[$i]->id)->where('user_id', auth()->user()->id)->first();

                    if ($perhitungan) {
                        $nilaiMin = (float)Perhitungan::where('kriteria_id', $kriteria[$i]->id)->where('user_id', auth()->user()->id)->min('nilai_ternormalisasi_terbobot');
                        $nilaiMax = (float)Perhitungan::where('kriteria_id', $kriteria[$i]->id)->where('user_id', auth()->user()->id)->max('nilai_ternormalisasi_terbobot');

                        $pos = Perhitungan::where('plan_id', $allPlan[$a]->id)->where('user_id', auth()->user()->id)->where('kriteria_id', $kriteria[$i]->id)->select(DB::raw("(pow(($nilaiMax - nilai_ternormalisasi_terbobot),2)) AS nilaiPos"))->first();
                        $neg = Perhitungan::where('plan_id', $allPlan[$a]->id)->where('user_id', auth()->user()->id)->where('kriteria_id', $kriteria[$i]->id)->select(DB::raw("(pow((nilai_ternormalisasi_terbobot - $nilaiMin),2)) AS nilaiNeg"))->first();

                        $nilaiPositif += (float)$pos->nilaiPos;
                        $nilaiNegatif += (float)$neg->nilaiNeg;
                    } else {

                        $nilaiMin = (float)Perhitungan::where('kriteria_id', $kriteria[$i]->id)->whereNull('user_id')->min('nilai_ternormalisasi_terbobot');
                        $nilaiMax = (float)Perhitungan::where('kriteria_id', $kriteria[$i]->id)->whereNull('user_id')->max('nilai_ternormalisasi_terbobot');

                        $pos = Perhitungan::where('plan_id', $allPlan[$a]->id)->whereNull('user_id')->where('kriteria_id', $kriteria[$i]->id)->select(DB::raw("(pow(($nilaiMax - nilai_ternormalisasi_terbobot),2)) AS nilaiPos"))->first();
                        $neg = Perhitungan::where('plan_id', $allPlan[$a]->id)->whereNull('user_id')->where('kriteria_id', $kriteria[$i]->id)->select(DB::raw("(pow((nilai_ternormalisasi_terbobot - $nilaiMin),2)) AS nilaiNeg"))->first();

                        $nilaiPositif += (float)$pos->nilaiPos;
                        $nilaiNegatif += (float)$neg->nilaiNeg;
                    }
                }
            }

            $forA = $allPlan[$a];

            app(PerhitunganController::class)->nilaiPreferensi($nilaiPositif, $nilaiNegatif, $forA);
        }

        app(PerhitunganController::class)->ranking();
    }

    public function nilaiPreferensi($nilaiPositif, $nilaiNegatif, $forA)
    {
        $ranking = Perankingan::where('plan_id', $forA->id)->whereNull('user_id')->first();

        if (auth()->user()->role == 'superadmin') {

            if ($ranking) {
                $ranking->nilai_solusi_positif = (float)sqrt($nilaiPositif);
                $ranking->nilai_solusi_negatif = (float)sqrt($nilaiNegatif);
                if ((float)sqrt($nilaiNegatif) != 0) {
                    $ranking->preferensi = (float)(sqrt($nilaiNegatif)) / (sqrt($nilaiNegatif) + sqrt($nilaiPositif));
                }
                $ranking->save();
            } else {
                if ((float)sqrt($nilaiNegatif) != 0) {
                    Perankingan::create([
                        'plan_id' => $forA->id,
                        'nilai_solusi_positif' => (float)sqrt($nilaiPositif),
                        'nilai_solusi_negatif' => (float)sqrt($nilaiNegatif),
                        'preferensi' => (float)(sqrt($nilaiNegatif)) / (sqrt($nilaiNegatif) + sqrt($nilaiPositif)),
                    ]);
                } else {
                    Perankingan::create([
                        'plan_id' => $forA->id,
                        'nilai_solusi_positif' => (float)sqrt($nilaiPositif),
                        'nilai_solusi_negatif' => (float)sqrt($nilaiNegatif),
                        'preferensi' => 0
                    ]);
                }
            }
        } else {

            $rankUser = Perankingan::where('plan_id', $forA->id)->where('user_id', auth()->user()->id)->first();

            if ($rankUser) {
                $rankUser->nilai_solusi_positif = (float)sqrt($nilaiPositif);
                $rankUser->nilai_solusi_negatif = (float)sqrt($nilaiNegatif);
                if ((float)sqrt($nilaiNegatif) != 0) {
                    $rankUser->preferensi = (float)(sqrt($nilaiNegatif)) / (sqrt($nilaiNegatif) + sqrt($nilaiPositif));
                }
                $rankUser->save();
            } else {
                if ((float)sqrt($nilaiNegatif) != 0) {
                    Perankingan::create([
                        'plan_id' => $forA->id,
                        'nilai_solusi_positif' => (float)sqrt($nilaiPositif),
                        'nilai_solusi_negatif' => (float)sqrt($nilaiNegatif),
                        'preferensi' => (float)(sqrt($nilaiNegatif)) / (sqrt($nilaiNegatif) + sqrt($nilaiPositif)),
                        'user_id' => auth()->user()->id
                    ]);
                } else {
                    Perankingan::create([
                        'plan_id' => $forA->id,
                        'nilai_solusi_positif' => (float)sqrt($nilaiPositif),
                        'nilai_solusi_negatif' => (float)sqrt($nilaiNegatif),
                        'preferensi' => 0,
                        'user_id' => auth()->user()->id
                    ]);
                }
            }
        }
    }

    public function ranking()
    {
        // perangkingan hasil

        if (auth()->user()->role == 'superadmin') {
            $ranked = Perankingan::orderBy('preferensi', 'desc')->whereNull('user_id')->get();

            foreach ($ranked as $r => $val) {
                $rank = Perankingan::where('id', $ranked[$r]->id)->first();
                $rank->perangkingan = $r + 1;
                $rank->save();
            }
        } else {
            $ranked = Perankingan::orderBy('preferensi', 'desc')->where('user_id', auth()->user()->id)->get();

            foreach ($ranked as $r => $val) {
                $rank = Perankingan::where('id', $ranked[$r]->id)->first();
                $rank->perangkingan = $r + 1;
                $rank->save();
            }
        }
    }
}
