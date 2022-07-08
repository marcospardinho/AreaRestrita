<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssembly;
use App\Models\Apuracao;
use Illuminate\Http\Request;
use App\Models\Assembleia;
use App\Models\Cadastro;
use App\Models\Endereco;
use App\Models\Enquete;
use App\Models\Participantes;
use App\Models\Siape;
use App\Models\UsuarioAssembleia;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class AssemblyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $assembleias = Assembleia::with('enquetes')->get();

        return view('assembly', compact('assembleias'));
    }

    public function questionary(Assembleia $assembleia){

        if(!$participante = Participantes::where([['cpf','=',Auth::guard("web")->user()->CPF_Cadastro],['id_assembleia','=',$assembleia->id_assembleia]])->first()){

            $participante = new Participantes();
            $participante->id_assembleia = $assembleia->id_assembleia;
            $participante->cpf = Auth::guard("web")->user()->CPF_Cadastro;
            $participante->nome = Auth::guard("web")->user()->Nome_Cadastro;
            $participante->status_siape = Auth::guard("web")->user()->siape[0]->Status_Siape;
            $participante->uf = Auth::guard("web")->user()->UFNaturalidade_Cadastro;
            $participante->save();

        }

        if(Assembleia::where([['id_assembleia','=',$assembleia->id_assembleia],['fim','>',now()]])->count() == 0){
            Alert::error('Esta assembleia já foi encerrada!', 'A ANFIP agradece sua participação.')->autoClose(5000);
            return redirect()->route('dashboard');
        }

        if(Apuracao::where([['id_participante','=',$participante->id_participante]])->count() !== 0){
            return redirect()->route('dashboard')->with('warning', 'Você já votou nesta assembleia!');

        }



        return view('questionary', compact('assembleia'));
    }

    public function resume(Assembleia $assembleia){

        return view('resume', compact('assembleia'));
    }

    public function questStore(Request $request, Assembleia $assembleia){

        if(!$participante = Participantes::where([['cpf','=',Auth::guard("web")->user()->CPF_Cadastro],['id_assembleia','=',$assembleia->id_assembleia]])->first()){
            return redirect()->route('dashboard')->with('warning', 'Você não está habilitado ao voto !');
        }

        if(Apuracao::where([['id_participante','=',$participante->id_participante]])->count() !== 0){
            return redirect()->route('dashboard')->with('warning', 'Você já votou nesta assembleia!');

        }

        if(Assembleia::where([['id_assembleia','=',$assembleia->id_assembleia],['fim','>',now()]])->count() == 0){
            Alert::error('Esta assembleia já foi encerrada!', 'A ANFIP agradece sua participação.')->autoClose(5000);
            return redirect()->route('dashboard');
        }

        foreach($assembleia->enquetes as $enquete){
            $apuracao = new Apuracao();
            $apuracao->id_enquete = $enquete->id_enquete;
            $apuracao->id_opcao = $request["$enquete->id_enquete"];
            $apuracao->id_participante = $participante->id_participante;
            $apuracao->save();

        }

        return redirect()->route('dashboard')->with('success', 'Voto Computado !');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('assemblyCreate');
    }

    public function active(Assembleia $assembleia, $type)
    {

        if($type == 0){
            $assembleia->status = 0;
            $assembleia->update();
            return redirect()->route('assembly.index')->with('success', '');
        }

        if($type == 1){
            if($assembleia->enquetes->count() == 0){
                return redirect()->route('assembly.index')->with('warning', 'Você não pode liberar uma Assembleia sem Enquetes prontas.');
            }
            $assembleia->status = 1;
            $assembleia->update();
            return redirect()->route('assembly.index')->with('success', '');
        }
        else{
            return redirect()->route('assembly.index')->with('warning', 'Tipo Inválido !');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssembly $request)
    {

        $datainicio = explode(" ", $request['dtinicio']);

        $datatermino = explode(" ", $request['dttermino']);

            $dataP = explode('/', $datainicio[0]);
            $datainicio[0] = $dataP[2].'-'.$dataP[1].'-'.$dataP[0];

            $datainicio = $datainicio[0] ." ". $datainicio[1] ;

            $dataP = explode('/', $datatermino[0]);
            $datatermino[0] = $dataP[2].'-'.$dataP[1].'-'.$dataP[0];

            $datatermino = $datatermino[0] ." ". $datatermino[1] ;

        $assembleia = Assembleia::create([
            'titulo' => $request['nomeassembleia'],
            'descricao' => $request['textoassembleia'],
            'status' => $request['status'],
            'inicio' => $datainicio,
            'fim' => $datatermino,
        ]);

        foreach ($request->usuarios as $usuario) {
            UsuarioAssembleia::create([
                'id_assembleia' => $assembleia->id_assembleia,
                'id_usuarios_habilitados' => $usuario,
            ]);
        }

        return redirect()->route('assembly.index')->with('success', 'Assembleia Cadastrada !');


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
    public function edit(Assembleia $assembleia)
    {
        $datainicio = explode(" ", $assembleia->inicio);

        $datatermino = explode(" ", $assembleia->fim);

                $dataP = explode('-', $datainicio[0]);
                $datainicio[0] = $dataP[2].'/'.$dataP[1].'/'.$dataP[0];

                $datainicio = $datainicio[0] ." ". $datainicio[1] ;

                $dataP = explode('-', $datatermino[0]);
                $datatermino[0] = $dataP[2].'/'.$dataP[1].'/'.$dataP[0];

                $datatermino = $datatermino[0] ." ". $datatermino[1] ;

        $assembleia->inicio = $datainicio;
        $assembleia->fim = $datatermino;

        return view('assemblyEdit', compact('assembleia'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAssembly $request, Assembleia $assembleia)
    {
        $assembleia->titulo = $request['nomeassembleia'];
        $assembleia->descricao = $request['textoassembleia'];
        $assembleia->status = $request['status'];

            $datainicio = explode(" ", $request['dtinicio']);

            $datatermino = explode(" ", $request['dttermino']);

                $dataP = explode('/', $datainicio[0]);
                $datainicio[0] = $dataP[2].'-'.$dataP[1].'-'.$dataP[0];

                $datainicio = $datainicio[0] ." ". $datainicio[1] ;

                $dataP = explode('/', $datatermino[0]);
                $datatermino[0] = $dataP[2].'-'.$dataP[1].'-'.$dataP[0];

                $datatermino = $datatermino[0] ." ". $datatermino[1] ;

        $assembleia->inicio = $datainicio;
        $assembleia->fim = $datatermino;

        if ($assembleia->permissao()->count() == count($request->usuarios)) {

            foreach ($assembleia->permissao as $key =>  $usuario) {
                $usuario->id_usuarios_habilitados = $request->usuarios[$key];
                $usuario->update();

            }

        }else{
            $assembleia->permissao()->delete();
            foreach ($request->usuarios as $userId) {
                UsuarioAssembleia::create([
                    'id_assembleia' => $assembleia->id_assembleia,
                    'id_usuarios_habilitados' => $userId,
                ]);

            }
        }

        $assembleia->update();

        return redirect()->route('assembly.index')->with('success', 'Assembleia Editada !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(!$assembleia = Assembleia::find($request["id"])){
            return redirect()->route('assembly.index')->with('warning', 'Assembléia Inexistente !');
        }

        foreach($assembleia->enquetes as $enquete){

            $enquete->opcoes()->delete();

            $enquete->delete();

        }

        $assembleia->delete();

        return redirect()->route('assembly.index', $enquete->id_assembleia)->with('success', 'Assembleia apagada!');

    }


}
