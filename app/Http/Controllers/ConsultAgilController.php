<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCollection;
use App\Http\Requests\EditCollection;
use App\Models\Acervos;
use App\Models\CaDocumentos;
use App\Models\Divisao;
use App\Models\SubDivisao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUpdateDoc;
use App\Models\Documentos;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class ConsultAgilController extends Controller
{

    public function index($acervo)
    {

        // |Inicio| Declarando sessão de Menu e Variável para carregamento de Arquivos.

        $_SESSION['acervo'] = $acervo;

        $titulo = Acervos::with('divisao')->find($acervo);
        $documentos = CaDocumentos::get();

        // |Final|  Declarando sessão de Menu e Variável para carregamento de Arquivos".

        // Redirecionamento para a DashBoard do Consulta Ágil.

        return view('consult', compact('documentos', 'acervo', 'titulo'));
    }

    public function collection()
    {

        return view('collection');
    }

    public function addCollection(Request $request)
    {

        // |Inicio| Verificação do campo "Titulo do Acervo".

        $request->validate([
            'titulo_acervo' => 'required|max:255',
        ]);

        // |Final| Verificação do campo "Titulo do Acervo".

        // |Inicio| Verificação de Existência do Nome do Acervo no Banco.

        $checagem = Acervos::where('descricao', $request->titulo_acervo)->count();

            if($checagem !== 0)
            {
                return back()->withErrors([
                    'titulo_acervo' => 'O nome do acervo já existe!',
                ])->withInput();
            }

        // |Final| Verificação de Existência do Nome do Acervo no Banco.

        // |Inicio| Controlador de Cadastro do Acervo.

        if (!Storage::exists('ConsultaÁgil/'. $request->titulo_acervo)) {
            Storage::makeDirectory( 'ConsultaÁgil/'.$request->titulo_acervo);
        }

        $acervo = new Acervos;

        $acervo->descricao = $request->titulo_acervo;

        $acervo->save();

        // |Final| Controlador de Cadastro do Acervo.

        // |Inicio| Alerta de Criação de Acervo.

        Alert::success('Acervo criado!', '
            <div style="text-align: center;">Comece a criar as pastas clicando em "Adicionar Pasta"</div>')->persistent(false, false)
                ->showConfirmButton('<a href="#" data-toggle="modal" data-target="#modal-xl-3" style="min-width: 95px; margin-right:  5px; color: #fff;"
            type="button" class="btn" title="Adicionar Pasta">
            Adicionar Pasta</a>', '#3085d6')
                ->showCancelButton(' <a href="#" style="min-width: 95px; margin-right:  5px; color: #fff;"
            type="button" class="btn" title="Adiar">
            Adiar</a>', '#aaa')->toHtml()->reverseButtons()->width('900px');

        // |Final| Alerta de Criação de Acervo.

        // Declaração de Variaveis e redirecionamento para a DashBoard do Consulta Ágil.

        $acervo = Acervos::where('descricao', $request->titulo_acervo)->first();


        $_SESSION['acervo'] = $acervo->id_acervo;
        $titulo = Acervos::with('divisao')->find($acervo->id_acervo);
        $documentos = CaDocumentos::get();
        $acervo = $acervo->id_acervo;
        return redirect()->route('consult',[$acervo])->with('documentos',$documentos,'titulo',$titulo,'acervo',$acervo,);

    }

    public function editDocuments(Request $request, $type)
    {

        // |Inicio| Verificação dos campos "Nome do Documento" e "Referencia".

        if ($type == 1) {
            foreach($request->titulo as $titulo){
                    if($titulo == []){
                        Alert::error('Erro ao cadastrar as alterações!' , 'Não deixe campos em branco ao modificar a pasta "'.$request->folder.'".')->persistent(false,false);
                        return back();
                    }
                    else{
                        foreach($request->ref as $ref){
                            if($ref == []){
                                Alert::error('Erro ao cadastrar as alterações!' , 'Não deixe campos em branco ao modificar a pasta "'.$request->folder.'".')->persistent(false,false);
                                return back();
                            }
                        }
                    }
                }
            }
        if ($type == 2) {
            foreach($request->titulo as $titulo){
                if($titulo == []){
                    Alert::error('Erro ao cadastrar as alterações!' , 'Não deixe campos em branco ao modificar a pasta "'.$request->folder.'".')->persistent(false,false);
                    return back();
                }
                else{
                    foreach($request->ref as $ref){
                        if($ref == []){
                            Alert::error('Erro ao cadastrar as alterações!' , 'Não deixe campos em branco ao modificar a pasta "'.$request->folder.'".')->persistent(false,false);
                            return back();
                        }
                    }
                }
            }
        }

        // |Final| Verificação dos campos "Nome do Documento" e "Referencia".

        // |Inicio| Controlador de Edição de documentos de uma pasta.

        if ($type == 1) {

            if ($request->folder == null) {
                return back();
            }

            $dados = ['descricao' => filter_var($request->folder, FILTER_SANITIZE_SPECIAL_CHARS), 'updated_at' => now()];
            $lista = $request->divisao;
            $divisao = Divisao::find($lista);
            $acervo = Acervos::find($divisao->id_acervo);

            // $path = storage_path().'/app/public/'.'ConsultaÁgil/'.$acervo->descricao.'/'.$divisao->descricao;
            // $newPath = str_replace('\\', "/", $path);
            // $pathRename = storage_path().'/app/public/'.'ConsultaÁgil/'.$acervo->descricao.'/'.$request->folder;
            // $newPathRemane = str_replace('\\', "/", $pathRename);
            

            $pasta = Divisao::where([['descricao','=',$request->folder],['id_acervo','=',$acervo->id_acervo]])->count();

            if ($pasta > 1) {
                return back()->withErrors([
                    'Erro' => 'Já existe uma pasta com esse nome !',
                ]);
            }

            if($request->folder != $divisao->descricao){
                // rename($newPath,$newPathRemane);
                $divisao->update($dados);

            }

            for ($i = 0; $i < count($request->titulo); $i++) {

                

                if (isset($request->id[$i])) {

                    $documento = CaDocumentos::find($request->id[$i]);
                    $linkDoc = substr(strstr($documento->arquivo, "/"), 1);
                    $pasta = explode('/', $linkDoc);

                    $dados = ['documento' => filter_var($request->titulo[$i], FILTER_SANITIZE_SPECIAL_CHARS) , 'referencia' => $request->ref[$i], 'arquivo' => 'ConsultaÁgil/' . $acervo->descricao . '/' . $pasta[1] . '/' . $pasta[2]];
                    if ($dados['documento'] == null) {
                        break;
                    }

                    if (isset($request->link_doc[$i]) && $request->link_doc[$i]->isValid()) {

                        Storage::delete($documento->arquivo);
                        $nameFile = $request->link_doc[$i]->getClientOriginalName();
                        $url_doc = $request->link_doc[$i]->storeAs('ConsultaÁgil/' . $acervo->descricao . '/' . $pasta[1], $nameFile);
                        $dados['arquivo'] = $url_doc;
                    }
                    $documento->update($dados);
                }

                if (!isset($request->id[$i])) {

                    $documento = CaDocumentos::find($request->id[0]);
                    $linkDoc = substr(strstr($documento->arquivo, "/"), 1);
                    $pasta = explode('/', $linkDoc);

                    $documentos = new CaDocumentos;

                    if ($request->titulo[$i] == null) {
                        break;
                    }
                    
                    $titulo = filter_var($request->titulo[$i], FILTER_SANITIZE_SPECIAL_CHARS);
                    $documentos->documento = $titulo;
                    $documentos->referencia = $request->ref[$i];

                    if (isset($request->link_doc[$i]) && ($request->link_doc[$i] != 'null' && $request->link_doc[$i]->isValid())) {
                        $nameFile = $request->link_doc[$i]->getClientOriginalName();
                        $url_doc = $request->link_doc[$i]->storeAs('ConsultaÁgil/' . $acervo->descricao . '/' . $pasta[1], $nameFile);
                        $documentos->arquivo = $url_doc;
                    } else {
                        return back()->withErrors([
                            'link_doc' => 'O arquivo não pode está vazio!',
                        ])->withInput();
                    }

                    $documentos->id_divisao = $divisao->id_divisao;
                    $documentos->save();

                }
            }
        }

            // |Final| Controlador de Edição de documentos de uma Pasta.

            // |Inicio| Controlador de Edição de documentos de uma SubPasta.

        if ($type == 2) {

            if ($request->folder == null) {
                return back();
            }

            $dados = ['descricao' => filter_var($request->folder, FILTER_SANITIZE_SPECIAL_CHARS), 'updated_at' => now()];
            $lista = $request->divisao;

            $subdivisao = SubDivisao::find($lista);
            $divisao = Divisao::find($subdivisao->id_divisao);
            $acervo = Acervos::find($divisao->id_acervo);
            // $path = storage_path().'/app/public/'.'ConsultaÁgil/'.$acervo->descricao.'/'.$divisao->descricao.'/'.$subdivisao->descricao;
            // $newPath = str_replace('\\', "/", $path);
            // $pathRename = storage_path().'/app/public/'.'ConsultaÁgil/'.$acervo->descricao.'/'.$divisao->descricao.'/'.$request->folder;
            // $newPathRemane = str_replace('\\', "/", $pathRename);
            $pasta = Divisao::where([['descricao','=',$request->folder],['id_acervo','=',$divisao->id_acervo]])->count();

            if ($pasta > 1) {
                return back()->withErrors([
                    'Erro' => 'Já existe uma pasta com esse nome !',
                ]);
            }

            if($request->folder != $subdivisao->descricao){
                // rename($newPath,$newPathRemane);
                $subdivisao->update($dados);

            }

            for ($i = 0; $i < count($request->titulo); $i++) {

                if (isset($request->id[$i])) {

                    $documento = CaDocumentos::find($request->id[$i]);
                    $linkDoc = substr(strstr($documento->arquivo, "/"), 1);
                    $pasta = explode('/', $linkDoc);
                    $dados = ['documento' => filter_var($request->titulo[$i], FILTER_SANITIZE_SPECIAL_CHARS) , 'referencia' => $request->ref[$i], 'arquivo' => 'ConsultaÁgil/' . $acervo->descricao . '/' . $pasta[1] . '/' . $pasta[2] . '/' . $pasta[3]];
                    if ($dados['documento'] == null) {
                        break;
                    }

                    if (isset($request->link_doc[$i]) && $request->link_doc[$i]->isValid()) {


                        $nameFile = $request->link_doc[$i]->getClientOriginalName();
                        $url_doc = $request->link_doc[$i]->storeAs('ConsultaÁgil/' . $acervo->descricao . '/' . $pasta[1] . '/' . $pasta[2], $nameFile);
                        $dados['arquivo'] = $url_doc;
                    }
                    $documento->update($dados);
                }

                if (!isset($request->id[$i])) {

                    $documento = CaDocumentos::find($request->id[0]);
                    $linkDoc = substr(strstr($documento->arquivo, "/"), 1);
                    $pasta = explode('/', $linkDoc);
                    
                    $documentos = new CaDocumentos;

                    if ($request->titulo[$i] == null) {
                        break;
                    }
                    $titulo = filter_var($request->titulo[$i], FILTER_SANITIZE_SPECIAL_CHARS);
                    $documentos->documento = $titulo;
                    $documentos->referencia = $request->ref[$i];

                    if (isset($request->link_doc[$i]) && ($request->link_doc[$i] != 'null' && $request->link_doc[$i]->isValid())) {
                        $nameFile = $request->link_doc[$i]->getClientOriginalName();
                        $url_doc = $request->link_doc[$i]->storeAs('ConsultaÁgil/' . $acervo->descricao . '/' . $pasta[1] . '/' . $pasta[2], $nameFile);
                        $documentos->arquivo = $url_doc;
                    } else{
                        return back()->withErrors([
                            'link_doc' => 'O arquivo não pode está vazio!',
                        ])->withInput();
                    }
                    $documentos->id_divisao = $divisao->id_divisao;
                    $documentos->id_sub_divisao = $subdivisao->id_sub_divisao;
                    $documentos->save();
                }
            }
        }

        // |Final| Controlador de Edição de documentos de uma SubPasta.

        // Redirecionamento de Página para a DashBoard do Consulta Ágil.

        return redirect()->route('consult', ['acervo' => $acervo->id_acervo])->with('success', 'Pasta editada com sucesso');
    }


    public function createFolder(Request $request, $acervo)
    {
        $request->session()->put('pastaError', 'true');

        $rules = [
            'folder' => 'required',
            'titulo.*' => 'required',
            'link_doc' => 'required',
            'ref.*' => 'required',
        ];
        if (isset($request->link_doc) && count($request->titulo) > 1) {
            foreach($request->titulo as $key => $index) {
                $rules['link_doc.' . $key] = 'required';
            }
        }
        $this->validate($request, $rules);

        $pasta = Divisao::where([['descricao','=',$request->folder],['id_acervo','=',$acervo]])->count();

        if ($pasta != 0) {
            return back()->withErrors([
                'Erro' => 'Já existe uma pasta com esse nome !',
            ]);
        }

        if (!$acervo = Acervos::find($acervo)) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        $divisao = Divisao::create([
            'descricao' => filter_var($request->folder, FILTER_SANITIZE_SPECIAL_CHARS),
            'id_acervo' => $acervo->id_acervo,
            'created_at' => now()
        ]);

        $pasta = Divisao::where('descricao', $request->folder)->first();

        for ($i = 0; $i < count($request->titulo); $i++) {
            $documentos = new CaDocumentos;
            if ($request->titulo[$i] == null) {
                break;
            }
            $titulo = filter_var($request->titulo[$i], FILTER_SANITIZE_SPECIAL_CHARS);
            $documentos->documento = $titulo;
            $documentos->referencia = $request->ref[$i];

            if (isset($request->link_doc[$i]) && ($request->link_doc[$i] != 'null' && $request->link_doc[$i]->isValid())) {
                $nameFile = $request->link_doc[$i]->getClientOriginalName();
                $url_doc = $request->link_doc[$i]->storeAs('ConsultaÁgil/' . $acervo->descricao . '/Pasta' . $pasta->id_divisao, $nameFile);
                $documentos->arquivo = $url_doc;
            }
            $documentos->id_divisao = $divisao->id_divisao;
            $documentos->save();

        }

        $request->session()->forget('pastaError');
        return redirect()->route('consult', ['acervo' => $acervo->id_acervo])->with('success', 'Pasta cadastrada com sucesso');
    }

    public function createSubfolder(Request $request, $acervo)
    {
        $request->session()->put('subPastaError', $request->divisao);

        $rules = [
            'folder' => 'required',
            'titulo.*' => 'required',
            'link_doc' => 'required',
            'ref.*' => 'required',
        ];
        if (isset($request->link_doc) && count($request->titulo) > 1) {
            foreach($request->titulo as $key => $index) {
                $rules['link_doc.' . $key] = 'required';
            }
        }
        $this->validate($request, $rules);

        if (!$acervo = Acervos::find($acervo)) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        if (!$divisao = Divisao::find($request->divisao)) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        $subdivisao = SubDivisao::create([
            'descricao' => filter_var($request->folder, FILTER_SANITIZE_SPECIAL_CHARS),
            'id_divisao' => $divisao->id_divisao,
            'created_at' => now()
        ]);


        $subpasta = SubDivisao::where('descricao', $request->folder)->first();

        for ($i = 0; $i < count($request->titulo); $i++) {
            $documentos = new CaDocumentos;
            if ($request->titulo[$i] == null) {
                break;
            }

            $titulo = filter_var($request->titulo[$i], FILTER_SANITIZE_SPECIAL_CHARS);
            $documentos->documento = $titulo;
            $documentos->referencia = $request->ref[$i];

            if (isset($request->link_doc[$i]) && ($request->link_doc[$i] != 'null' && $request->link_doc[$i]->isValid())) {
                $nameFile = $request->link_doc[$i]->getClientOriginalName();
                $url_doc = $request->link_doc[$i]->storeAs('ConsultaÁgil/' . $acervo->descricao . '/Pasta' . $subdivisao->id_divisao . '/SubPasta' . $subpasta->id_sub_divisao, $nameFile);
                $documentos->arquivo = $url_doc;
            }
            $documentos->id_divisao = $divisao->id_divisao;
            $documentos->id_sub_divisao = $subdivisao->id_sub_divisao;
            $documentos->save();
        }

        $request->session()->forget('subPastaError');
        return redirect()->route('consult', ['acervo' => $acervo->id_acervo])->with('success', 'Pasta cadastrada com sucesso');
    }

    public function deleteFolder(Request $request, $acervo)
    {

        if (!$acervo = Acervos::find($acervo)) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        if (!$divisao = Divisao::where('id_divisao', $request->divisao)->with('subdivisao')->first()) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }
        

        $pasta = CaDocumentos::where('id_divisao', $request->divisao)->first();

        if(!$pasta = CaDocumentos::where('id_divisao', $request->divisao)->first()){
            $pasta = Divisao::where('id_divisao', $request->divisao)->first();
            $path = 'ConsultaÁgil/'. $acervo->descricao.'/Pasta'.$divisao->id_divisao;
        }
        else{
            $folder = explode('/', $pasta->arquivo);
            $path = 'ConsultaÁgil/'. $folder[1].'/'.$folder[2];
        }

        if (Storage::exists($path)) {
            Storage::deleteDirectory($path);
        }

        $documentos = CaDocumentos::where('id_divisao', $divisao->id_divisao)->get();

        if ($documentos) {
            foreach ($documentos as $documento) {
                $documento->delete();
            }
        }

        if ($divisao->subdivisao()->exists()) {

            foreach ($divisao->subdivisao as $sub) {
                
                $pasta = CaDocumentos::where('id_sub_divisao', $sub->id_sub_divisao)->first();

                if(!$pasta = CaDocumentos::where('id_sub_divisao', $sub->id_sub_divisao)->first()){
                    $pasta = SubDivisao::where('id_sub_divisao', $sub->id_sub_divisao)->first();
                    $divisao = Divisao::where('id_divisao', $sub->id_divisao)->first();
                    $path = 'ConsultaÁgil/'. $acervo->descricao.'/Pasta'.$divisao->id_divisao.'/SubPasta'.$pasta->id_sub_pasta;
                }
                else{
                    $folder = explode('/', $pasta->arquivo);
                    $path = 'ConsultaÁgil/'. $folder[1].'/'.$folder[2].'/'.$folder[3];
                }
                
                if (Storage::exists( $path)) {
                    Storage::deleteDirectory($path);
                }
                $subdocumentos = CaDocumentos::where('id_sub_divisao', $sub->id_sub_divisao)->where('id_divisao', $divisao->id_divisao)->get();
                foreach ($subdocumentos as $subdocumento) {
                    $subdocumento->delete();
                }
                $sub->delete();
            }
        }

        $divisao->delete();
        return redirect()->route('consult', ['acervo' => $acervo->id_acervo])->with('success', 'A pasta foi removida');
    }

    public function deleteSubFolder(Request $request, $acervo)
    {

        if (!$acervo = Acervos::find($acervo)) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        if (!$subdivisao = SubDivisao::where('id_sub_divisao', $request->subdivisao)->first()) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        if(!$pasta = CaDocumentos::where('id_sub_divisao', $subdivisao->id_sub_divisao)->first()){
            $pasta = SubDivisao::where('id_sub_divisao', $subdivisao->id_sub_divisao)->first();
            $divisao = Divisao::where('id_divisao', $subdivisao->id_divisao)->first();
            $path = 'ConsultaÁgil/'. $acervo->descricao.'/Pasta'.$divisao->id_divisao.'/SubPasta'.$pasta->id_sub_pasta;
        }
        else{
            $folder = explode('/', $pasta->arquivo);
            $path = 'ConsultaÁgil/'. $folder[1].'/'.$folder[2].'/'.$folder[3];
        }

        if (Storage::exists( $path)) {
            Storage::deleteDirectory($path);
        }

        $subdocumentos = CaDocumentos::where('id_sub_divisao', $subdivisao->id_sub_divisao)->get();

        if ($subdocumentos) {
            foreach ($subdocumentos as $subdocumento) {
                $subdocumento->delete();
            }
        }


        $subdivisao->delete();
        return redirect()->route('consult', ['acervo' => $acervo->id_acervo])->with('success', 'A subpasta foi removida');
    }

    public function deleteCadocuments(Request $request)
    {

        if ($documento = CaDocumentos::find($request->arquivo)) {

            switch ($request->type) {
                case 0:
                    if (CaDocumentos::where('id_divisao', $documento->id_divisao)->where('id_sub_divisao',null)->count() <= 1) {
                        Alert::error('Você não pode apagar todos os documentos de uma pasta!', 'Para apagar todos os documentos, exclua a pasta em questão.')->persistent(false,false);
                        return back();
                    }

                    break;
                case 1:
                    if (CaDocumentos::where('id_sub_divisao', $documento->id_sub_divisao)->count() <= 1) {
                        Alert::error('Você não pode apagar todos os documentos de uma pasta!', 'Para apagar todos os documentos, exclua a pasta em questão.')->persistent(false,false);
                        return back();
                    }
                    break;

                default:
                    return back();
                    break;
            }

            if (Storage::exists($documento->arquivo)) {
                Storage::delete($documento->arquivo);
            }

            $documento->delete();

            return back()->with('success', 'O arquivo foi apagado!');
        }

        return back()->withErrors([
            'Erro' => 'Algumas informações estão incorretas',
        ]);
    }

    public function editAcervo(Request $request)
    {

        if ($request->acervo_name == null) {
            return back();
        }

        $checagem = Acervos::where('descricao', $request->acervo_name)->count();

        if($checagem !== 0)
        {
            Alert::error('O nome do acervo já existe!')->persistent(false,false);
            return back();
        }

        $acervo = Acervos::find($request->acervo_id);
        
        $oldNameAcervo = $acervo->descricao;
        foreach($acervo->divisao as $divs){
           foreach($divs->documentos as $docs){
                $oldNameFile = $docs->arquivo;
                $newNameFile = str_replace($oldNameAcervo, $request->acervo_name, $oldNameFile );
                $docs->update(['arquivo' => $newNameFile]);

           }
        }

        $path = storage_path().'/app/public/'.'ConsultaÁgil/'.$acervo->descricao;
        $newPath = str_replace('\\', "/", $path);
        $pathRename = storage_path().'/app/public/'.'ConsultaÁgil/'.$request->acervo_name;
        $newPathRemane = str_replace('\\', "/", $pathRename);
        rename($newPath,$newPathRemane);

        $dados = ['descricao' => $request->acervo_name];
        $acervo->update($dados);

        return redirect()->route('consult', ['acervo' => $request->acervo_id])->with('success', 'Titulo do acervo editado com sucesso.');
    }

    public function deleteAcervo($acr)
    {

        if (!$acervo = Acervos::find($acr)) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        if (!$divisao = Divisao::where('id_acervo', $acr)->with('subdivisao')->get()) {
            return back()->withErrors([
                'Erro' => 'Algumas informações estão incorretas',
            ]);
        }

        foreach ($divisao as $div) {

            $documentos = CaDocumentos::where('id_divisao', $div->id_divisao)->get();
            if ($documentos) {
                foreach ($documentos as $documento) {
                    $documento->delete();
                }
            }

            if ($div->subdivisao()->exists()) {
                foreach ($div->subdivisao as $sub) {
                    $subdocumentos = CaDocumentos::where('id_sub_divisao', $sub->id_sub_divisao)->where('id_divisao', $div->id_divisao)->get();
                    foreach ($subdocumentos as $subdocumento) {

                        $subdocumento->delete();
                    }
                    $sub->delete();
                }
            }
            $div->delete();
        }
        $acervo->delete();

        $path = 'ConsultaÁgil/'.$acervo->descricao;

        if (Storage::exists( $path)) {
            Storage::deleteDirectory($path);
        }

        return redirect()->route('dashboard')->with('success', 'O acervo foi removido');
    }

    public function search(Request $request, $acervo){

        // |Início| Validação do campo de pesquisa.

                $request->validate([
                    'keyword' => 'required',
                ]);
        
        // |Final| Validação do campo de pesquisa.

        // |Início| Limpando a string chave de pesquisa e retornando incidências de correspondencias de documentos no banco.

                $abstracao  = explode(',', $request->keyword);
                $referencias = Acervos::with('divisao')->find($acervo);
                $_SESSION['acervo'] = $acervo;

                $titulo = array();

                foreach($abstracao as $refSearch){


                $documentos = CaDocumentos::where('referencia','LIKE','%'.$refSearch.'%')->orderByDesc('documento')->get();

                $divisoes = Divisao::where('id_acervo', $acervo)->get();
                $acr = Acervos::find($acervo);


                    foreach($divisoes as $div){
                        foreach($documentos as $doc){
                            if($div->id_divisao == $doc->id_divisao){
                                array_push($titulo, $doc);
                            }
                        }
                    }



                $documentos = CaDocumentos::where('documento','LIKE','%'.$refSearch.'%')->orderByDesc('documento')->get();


                    foreach($divisoes as $div){
                        foreach($documentos as $doc){
                            if($div->id_divisao == $doc->id_divisao){
                                array_push($titulo, $doc);
                            }
                        }
                    }
                }
                
            // |Final| Limpando a string chave de pesquisa e retornando incidências de correspondencias de documentos no banco.

            // |Início| Limpando o array de documentos prevenindo repetição.

                    $contador = 0;
                    $indice = 0;

                        foreach($titulo as $limpo){
                            foreach($titulo as $compara){
                                if($limpo->id_documento == $compara->id_documento){
                                    $contador+=1;
                                }
                            }
                            if($contador >= 2){
                                unset($titulo[$indice]);
                            }
                            $contador = 0;
                            $indice += 1;
                        }

            // |Final| Limpando o array de documentos prevenindo repetição.

            // Redirecionando para DashBoard do Consulta Ágil.

            return view('consult', compact('acr', 'documentos', 'acervo', 'titulo'));

    }

}
