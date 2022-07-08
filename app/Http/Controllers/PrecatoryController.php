<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrecatory;
use App\Models\Cadastro;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Departamentos;
use App\Models\Diretoria;
use App\Models\Funcionario;
use App\Models\Menu;
use App\Models\Precatorios;
use App\Models\Submenu;
use App\Models\Termo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class PrecatoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function editpre($id)
    {
        if (Auth::guard('admin')->check()) {

            $submenus = Submenu::get();
            $precatorio = Precatorios::with('tipo')->find($id);
            $assoc = DB::connection('sqlsrv')->select(DB::raw(" SELECT * FROM dbo.Cadastro ORDER BY Nome_Cadastro "));
            $tipospre = DB::connection('mysql')->select(DB::raw(" SELECT * FROM tipos_precatorio ORDER BY descricao "));

        foreach($submenus as $subs)
        {
            if($subs->nome_sub_menu == "Precatórios")
            {
                $menu = $subs->id_sub_menu;
            }
        }

            $abstracao = Menu::get();

            $precatorio['anexo'] = substr(strrchr($precatorio->anexo, "/"), 1);
            return view('precatoryEdit', compact( 'tipospre', 'assoc','precatorio', 'id', 'menu'));
        }

        return back();

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePrecatory $request, $menu)
    {
        $nome = Cadastro::find($request->associado);

        $valor = $request->valor;
        $valor = str_replace("R", "", $valor);
        $valor = str_replace("$", "", $valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", ".", $valor);



        if ($request->file('link_doc') != null && $request->file('link_doc')->isValid()) {

            $nameFile = $request->file('link_doc')->getClientOriginalName();

            $split = explode("-", $request->datadoprecatorio);

            $link_doc = $request->file('link_doc')->storeAs('Precatórios/' . $split[0] . '/' . $split[1], $nameFile);

            Precatorios::create([
                'id_Cadastro' => $nome->Id_Cadastro,
                'nome' => $nome->Nome_Cadastro,
                'descricao_precatorio' => $request->descricao,
                'numero_precatorio' => $request->numero,
                'valor_precatorio' => $valor,
                'data_precatorio' => $request->datadoprecatorio,
                'id_tipo_precatorio' => $request->tipoprecatorio,
                'numero_processo' => $request->processo,
                'anexo' => $link_doc,

            ]);

            return redirect()->route('list', [$menu])->with('success', 'Precatório Cadastrado Com Sucesso!');
        } else {



            Precatorios::create([
                'id_Cadastro' => $nome->Id_Cadastro,
                'nome' => $nome->Nome_Cadastro,
                'descricao_precatorio' => $request->descricao,
                'numero_precatorio' => $request->numero,
                'valor_precatorio' => $valor,
                'data_precatorio' => $request->data,
                'id_tipo_precatorio' => $request->tipoprecatorio,
                'numero_processo' => $request->processo,

            ]);

            return redirect()->route('list', [$menu])->with('success', 'Precatório Cadastrado Com Sucesso!');

        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $nome = Cadastro::find($request->nome);
        $submenus = Submenu::get();

        foreach($submenus as $subs)
        {
            if($subs->nome_sub_menu == "Precatórios")
            {
                $menu = $subs->id_sub_menu;
            }
        }

        if (!Auth::guard('admin')->check()) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }
        if (!$precatorio = Precatorios::find($id)) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }

        $dados = $request->except('_token', '_method');
        $dados['nome'] = $nome->Nome_Cadastro;

        if ($request->anexo && $request->file('anexo')->isValid()) {

            if (Storage::exists($precatorio->anexo)) {
                Storage::delete($precatorio->anexo);


            }

            $nameFile = $request->file('anexo')->getClientOriginalName();
            $split = explode("-", $request->data_precatorio);
            $dados['anexo'] = $request->file('anexo')->storeAs('Precatórios/' . $split[0] . '/' . $split[1], $nameFile);

        }
        $dados['id_Cadastro'] = $request->nome;
        $precatorio->update($dados);
        return redirect()->route('list', $menu)->with('success', 'Precatório Editado com sucesso!');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($precatorio = Precatorios::find($request->id)) {

            if (Storage::exists($precatorio->anexo)) {
                Storage::delete($precatorio->anexo);
            }

            $precatorio->delete();

            return back()->with('message', 'O precatório foi apagado!');
        }

        return back()->withErrors([
            abort(400, 'Nenhum precatório foi enviado.')
        ]);
    }
}
