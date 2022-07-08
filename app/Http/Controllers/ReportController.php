<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Apuracao;
use App\Models\Assembleia;
use App\Models\Enquete;
use App\Models\Participantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function reportParticipants(Assembleia $assembleia)
    {


        $participantes = $assembleia->participantes()->whereHas('apuracao')->get();
        $titulo = $assembleia->titulo;
        return view('participantsReport', compact('participantes', 'titulo'));
    }

    public function reportQuestion(Assembleia $assembleia)
    {

        $contagem = 0;
        foreach ($assembleia->enquetes as $enquete) {
            if ($enquete->opcoes->count() > $contagem) {
                $item = $enquete;
                $contagem = $enquete->opcoes->count();
            }
        }
        return view('questionsReport', compact('assembleia', 'item', 'contagem'));
    }

    public function reportVotesPerUF(Assembleia $assembleia)
    {

        $grouped = Participantes::where('id_assembleia',$assembleia->id_assembleia)->get();
        $participantes = $grouped->groupBy('uf');

        return view('votesperUFReport', compact('participantes','assembleia'));
    }
}
