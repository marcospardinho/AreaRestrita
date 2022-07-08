<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestion;
use App\Models\Assembleia;
use App\Models\Enquete;
use App\Models\Opcoes;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Assembleia $assembleia)
    {

        return view('questionCreate', compact('assembleia'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(StoreQuestion $request)
    {
        if (!Assembleia::find($request->assembleia)) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        $enquete = Enquete::create([
            'id_assembleia' => $request->assembleia,
            'titulo' => $request->titulo_enquete,
            'descricao' => $request->descricao ?? null,
        ]);

        foreach ($request->resposta as $opcao) {
            Opcoes::create([
                'id_enquete' => $enquete->id_enquete,
                'descricao' => $opcao,
            ]);
        }

        return redirect()->route('question.index',$request->assembleia)->with('success', 'Enquete cadastrada!');


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Assembleia $assembleia)
    {
        return view('question', compact('assembleia'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Enquete $question)
    {
        if($question->apuracao()->exists()){
            return redirect()->route('question.index',$question->id_assembleia)->with('warning', 'Não é possível editar uma enquete que já está sendo apurada!');

        }
        return view('questionEdit', compact('question'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreQuestion $request, Enquete $question)
    {
        $question->titulo = $request->titulo_enquete;
        $question->descricao = $request->descricao;
        $question->update();

        if ($question->opcoes()->count() == $request->quantidade_respostas) {

            foreach ($question->opcoes as $key =>  $opcao) {
                $opcao->descricao = $request->resposta[$key];
                $opcao->update();

            }

        }else{
            $question->opcoes()->delete();
            foreach ($request->resposta as $opcao) {
                Opcoes::create([
                    'id_enquete' => $question->id_enquete,
                    'descricao' => $opcao,
                ]);

            }
        }

        return redirect()->route('question.index', $question->id_assembleia)->with('success', 'Enquete atualizada!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (!$enquete = Enquete::find($request->id)) {
            return redirect()->back()->with('warning', 'Enquete Inexistente !');
        }
        $enquete->opcoes()->delete();

        $enquete->delete();

        return redirect()->route('question.index', $enquete->id_assembleia)->with('success', 'Enquete apagada!');

    }
}
