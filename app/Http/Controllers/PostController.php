<?php
namespace App\Http\Controllers;

/*
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Endereco;
use App\Models\Cadastro;
*/

use App\Http\Requests\StoreUpdateDoc;
use App\Models\Apuracao;
use App\Models\Assembleia;
use App\Models\Cadastro;
use App\Models\Departamentos;
use App\Models\Diretoria;
use App\Models\Documentos;
use App\Models\Funcionario;
use App\Models\ControleAcessos;
use App\Models\Menu;
use App\Models\Participantes;
use App\Models\SubdoSub;
use App\Models\PasswordResets;
use App\Models\Precatorios;
use App\Models\Submenu;
use App\Models\TiposDocumento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;


class PostController extends Controller


{
    protected $request, $user;

    public function __construct(Request $request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function show($menu)
    {

        $documentos = Documentos::where('id_sub_menu', $menu)->orderBy('data', 'DESC')->get();
        $titulo = Submenu::find($menu);
        $tipos = TiposDocumento::get();
        $contador = 0;

        if($titulo->nome_sub_menu == 'Precatórios'){
            $_SESSION['submenu'] = $titulo->id_sub_menu;

            $valor = $titulo->id_menu;
            $menuguia = Menu::find($valor);
            $valorguia = $menuguia->id_menu;

            $_SESSION['valor'] = $valorguia;
            $_SESSION['subdosub'] = $menu;
            $precatorios = Precatorios::where('id_Cadastro', Auth::guard('web')->user()->Id_Cadastro)->latest()->get();
            return view('precatoryDetail', compact('menu'), compact('titulo','precatorios'));
        }

        if( Auth::guard('admin')->check() ){

            $subdosub = SubdoSub::find($menu);

            if($subdosub == null){
                return back();
            }

            $subguia = $subdosub->id_sub_menu;
            $submenu = Submenu::find($subguia);
            $valor = $submenu->id_menu;
            $menuguia = Menu::find($valor);
            $valorguia = $menuguia->id_menu;


            $menus = Menu::get();

            $pivo = $menuguia->id_diretoria;
            $diretoria = Diretoria::find($pivo);
            $diretorias = Diretoria::get();

            $checagem = DB::connection('mysql')->select(DB::raw("SELECT * FROM grupos_acessos WHERE id_diretoria = $pivo"));

            foreach($checagem as $check)
            {
            if($check->id_setor == Auth::guard('admin')->user()->departamento->id_setor)
                {
                    $contador+=1;
                }
            }

            if($contador == 0){
                return back();
            }
            else{
                $contador = 0;
            }

            $_SESSION['subdosub'] = $menu;
            $_SESSION['submenu'] = $submenu->id_sub_menu;
            $_SESSION['valor'] = $valorguia;


            $titulo = SubdoSub::find($menu);
            $docs = Documentos::where('id_sub_s_menu', $menu)->orderBy('data', 'DESC')->get();
            $funcionarios = Funcionario::latest()->get();
            $diretorias = Diretoria::find($menu);
            $submenus = Submenu::get();
            $tipos = TiposDocumento::get();



            return view('show', compact('menu'), compact( 'documentos', 'titulo' ,'funcionarios', 'diretorias','docs', 'submenus', 'tipos', 'contador','menu','valorguia','diretoria'));

        }



        $submenu = Submenu::find($menu);

        if($submenu == null){
            return back();
        }

        $valor = $submenu->id_menu;
        $menuguia = Menu::find($valor);
        $valorguia = $menuguia->id_menu;
        $menus = Menu::get();

        $pivo = $menuguia->id_diretoria;
        $diretoria = Diretoria::find($pivo);
        $diretorias = Diretoria::get();

        $_SESSION['submenu'] = $submenu->id_sub_menu;
        $_SESSION['valor'] = $valorguia;

        return view('show', compact('contador','menu','valorguia','diretoria', 'diretorias' ,'tipos','documentos', 'titulo'));
    }


    public function subview($menu)
    {
        $documentos = Documentos::where('id_sub_menu', $menu)->orderBy('data', 'DESC')->get();
        $titulo = Submenu::find($menu);
        $tipos = TiposDocumento::get();
        $contador = 0;



            $subdosub = SubdoSub::find($menu);

                if($subdosub == null){
                    return back();
                }

            $subguia = $subdosub->id_sub_menu;
            $submenu = Submenu::find($subguia);
            $valor = $submenu->id_menu;
            $menuguia = Menu::find($valor);
            $valorguia = $menuguia->id_menu;


            $menus = Menu::get();

            $pivo = $menuguia->id_diretoria;
            $diretoria = Diretoria::find($pivo);
            $diretorias = Diretoria::get();

            $_SESSION['submenu'] = $submenu->id_sub_menu;
            $_SESSION['valor'] = $valorguia;
            $_SESSION['subdosub'] = $menu;

            $titulo = SubdoSub::find($menu);
            $docs = Documentos::where('id_sub_s_menu', $menu)->orderBy('data', 'DESC')->get();
            $funcionarios = Funcionario::latest()->get();
            $diretorias = Diretoria::find($menu);
            $submenus = Submenu::get();
            $tipos = TiposDocumento::get();

            return view('subview', compact('menu'), compact( 'documentos', 'titulo' ,'funcionarios', 'diretorias','docs', 'submenus', 'tipos', 'contador','menu','valorguia','diretoria'));

    }

    public function dashboard()
    {

        $noticias = DB::connection('noticias')->select(DB::raw("SELECT DISTINCT ID
                                ,post_title as Titulo
                                ,post_date  as Data
                                ,guid
                                FROM an_posts, an_postmeta
                                WHERE an_posts.post_type = 'post'
                                AND an_posts.post_status = 'publish'
                                AND an_postmeta.post_id = an_posts.id
                                AND an_postmeta.meta_key = '_thumbnail_id'
                                ORDER BY id desc limit 5"));

        if (Auth::guard('admin')->check()) {

            $departamentos = Departamentos::where('id_setor',  Auth::guard('admin')->user()->departamento->id_setor)->with('diretorias')->first();
            return view('dashboard', compact('departamentos', 'noticias'));
        }

        $assembleias = Assembleia::where([['status','=',1],['inicio','<=',now()],['fim','>=',now()]])->latest()->limit(5)->get();
        $assembleiasValidas = new Collection();

        foreach($assembleias as $key => $assembleia){

            if ($assembleia->permissao->contains('id_usuarios_habilitados', Auth::guard("web")->user()->siape[0]->Status_Siape)) {
                $assembleiasValidas[$key] = $assembleia;

                $participante = Participantes::where([['cpf','=',Auth::guard("web")->user()->CPF_Cadastro],['id_assembleia','=',$assembleiasValidas[$key]->id_assembleia]])->first();

                if($participante && Apuracao::where([['id_participante','=',$participante->id_participante]])->count() !== 0){
                    $assembleiasValidas[$key]->votou = true;
                }

            }

        }

        $id = Auth::guard('web')->user()->Id_Cadastro;
        $ContagemJuridico = Documentos::where('id_diretoria', 14)->get()->count();
        $ContagemFinaceiro = Documentos::where('id_diretoria', 15)->get()->count();
        $ContagemAssistenciais = Documentos::where('id_diretoria', 8)->get()->count();
        $ContagemOcarmentario = Documentos::where('id_diretoria', 22)->get()->count();
        $ultimosDocumentos = Documentos::latest()->limit(15)->with('tipo')->get();
        $ultimosPrecatorios = Precatorios::where('id_Cadastro', $id)->latest()->limit(2)->get();

        $aniversariantes = DB::connection('sqlsrv')->select(DB::raw("select DISTINCT
        Cad.Nome_Cadastro as Nome,
        coalesce(e.UF_Endereco, '-') as UF,
        convert(varchar(5),DataNascimento_Cadastro,103) as Data,
        year(getdate())*10000+month(DataNascimento_Cadastro)*100+day(DataNascimento_Cadastro) as dt_ref
        from Cadastro           Cad
        join siape              S    on S.id_Cadastro       = Cad.Id_Cadastro
        left join Endereco      e    on e.Id_Cadastro  = Cad.Id_Cadastro
        where 1=1
        and s.Filiado_Siape = 1
        and s.Cobrar_Siape = 1
        and s.Status_Siape in (1,2,3)
        and coalesce(S.suspensa, '') <> 'S'
        and coalesce(S.excluido, '') <> '*'
        and coalesce(Cad.excluido, '') <> '*'
        and MONTH(Cad.DataNascimento_Cadastro) = MONTH(GETDATE())
        GROUP BY Cad.DataNascimento_Cadastro, Cad.Nome_Cadastro,e.UF_Endereco
        ORDER BY year(getdate())*10000+month(DataNascimento_Cadastro)*100+day(DataNascimento_Cadastro) asc"));

        $cadastroAtualizado = DB::connection('sqlsrv')->select(DB::raw("select top 1
        IdLogAlteracao,
        Ultima_Atualizacao  = convert(char(10),getdate,103) + ' ' + convert(char(10),getdate,108)
        from LogAlteracoes l
        left join sysobjects   o on o.id              = l.idobj
        left join syscolumns c on c.id              = o.id and c.colid = l.idcol
        left join CaUsuarios u on u.IdUsuario = l.suser_id
        where 1=1
        and l.idObj in (select id from sysObjects where type='u' and name in ('endereco','telefone','email'))
        and DATEDIFF(MONTH, Getdate, GETDATE()) < 6
        and (
              (o.name = 'endereco'   and idkey in (select id_endereco  from Endereco  where Id_Cadastro = $id ))
          or (o.name = 'telefone'     and idkey in (select id_telefone    from Telefone    where Id_Cadastro = $id ))
          or (o.name = 'email'         and idkey in (select id_email        from email         where Id_Cadastro = $id ))
        )
        order by IdLogAlteracao desc"));

        if (empty($cadastroAtualizado)) {
            Alert::warning('Atualização Cadastral','<div style="text-align: center; font-size: 20px;">Agora é possível atualizar seus dados pela Área Restrita da ANFIP.<br>Clique em "Atualizar Informações" e preencha com seus dados atuais.</div>')->persistent(false, false)
            ->showConfirmButton('<a href="/profile" style="min-width: 95px; margin-right:  5px; color: #fff;"
            type="button" class="btn" title="Atualizar Informações">
            Atualizar Informações</a>', '#3085d6')
            ->showCancelButton(' <a href="#" style="min-width: 95px; margin-right:  5px; color: #fff;"
            type="button" class="btn" title="Adiar">
            Adiar</a>', '#aaa')->toHtml()->reverseButtons()->width('900px');

        }

         return view('dashboard', compact('assembleiasValidas', 'ContagemJuridico', 'ContagemFinaceiro', 'ContagemAssistenciais', 'ContagemOcarmentario', 'ultimosDocumentos', 'aniversariantes', 'noticias','ultimosPrecatorios'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function list($menu)
    {

            $submenu = Submenu::find($menu);

            if($submenu == null){
                return back();
            }

            $valor = $submenu->id_menu;
            $menuguia = Menu::find($valor);
            $valorguia = $menuguia->id_diretoria;
            $direcguia = Diretoria::find($valorguia);
            $menus = Menu::get();
            $contador = 0;

            $checagem = DB::connection('mysql')->select(DB::raw("SELECT * FROM grupos_acessos WHERE id_diretoria = $direcguia->id_diretoria "));

            foreach($checagem as $check)
            {
            if($check->id_setor == Auth::guard('admin')->user()->departamento->id_setor)
                {
                    $contador+=1;
                }
            }

            if($contador == 0){
                return back();
            }
            else{
                $contador = 0;
            }

            $_SESSION['submenu'] = $submenu->id_sub_menu;
            $_SESSION['valor'] = $valor;
            $submenus = Submenu::get();
            $diretorias = Diretoria::find($menu);
            $titulo = Submenu::find($menu);

            if($submenu->nome_sub_menu == 'Precatórios'){

                $precatorios = Precatorios::orderBy('created_at', 'DESC')->with('tipo')->get();

                return view('precatory', compact('menu'), compact( 'precatorios', 'titulo' , 'diretorias', 'submenus', 'contador'));
            }

            $funcionarios = Funcionario::latest()->get();
            $tipos = TiposDocumento::get();
            $documentos = Documentos::where('id_sub_menu', $menu)->orderBy('data', 'DESC')->get();


            return view('list', compact('menu'), compact( 'documentos', 'titulo' ,'funcionarios', 'diretorias', 'submenus', 'tipos', 'contador'));
    }



    public function create($menu)

    {
        if (Auth::guard('admin')->check()) {

            $submenu = Submenu::find($menu);
            $abstracao = Menu::get();
            $contador = 0;

            if($submenu->nome_sub_menu == 'Precatórios'){
                $tipospre = DB::connection('mysql')->select(DB::raw(" SELECT * FROM tipos_precatorio ORDER BY descricao "));
                $assoc = DB::connection('sqlsrv')->select(DB::raw(" SELECT * FROM dbo.Cadastro ORDER BY Nome_Cadastro "));
                return view('precatoryCreate', compact('menu'), compact('assoc', 'tipospre', 'contador'));

            }

                foreach($abstracao as $abs){
                    if($submenu->id_menu == $abs->id_menu){
                        $diretoria = $abs->id_diretoria;
                    }
                }

            $menus = Menu::orderBy('nome_menu', 'ASC')->get();
            /*$submenus = Submenu::orderBy('nome_sub_menu', 'ASC')->get();*/

            $setores = Diretoria::get();
            $tiposdoc = DB::connection('mysql')->select(DB::raw(" SELECT * FROM tipos_documento ORDER BY nome "));

            return view('create', compact('menu'), compact('diretoria', 'submenu', 'setores', 'menus', 'tiposdoc', 'contador'));
        }

        return back();
    }

    public function saving($menu)

    {
        if (Auth::guard('admin')->check()) {

            $subdosub = SubdoSub::find($menu);
                $abstracao = Submenu::get();

                foreach($abstracao as $abs){
                    if($subdosub->id_sub_menu == $abs->id_sub_menu){
                        $idmenu = $abs->id_menu;
                    }
                }

            $submenu = Submenu::find($idmenu);
            $abstracao = Menu::get();

                foreach($abstracao as $abs){
                    if($submenu->id_menu == $abs->id_menu){
                        $diretoria = $abs->id_diretoria;
                    }
                }

            $menus = Menu::orderBy('nome_menu', 'ASC')->get();
            /*$submenus = Submenu::orderBy('nome_sub_menu', 'ASC')->get();*/
            $setores = Diretoria::get();
            $tiposdoc = DB::connection('mysql')->select(DB::raw(" SELECT * FROM tipos_documento ORDER BY nome "));
            $contador = 0;
            return view('save', compact('menu'), compact('diretoria', 'submenu', 'setores', 'menus', 'tiposdoc', 'contador'));
        }

        return back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function print()
    {
        return back();
    }

    public function menus($menu)
    {

        return view('menus', compact('menu'));
    }

    public function store(StoreUpdateDoc $request, $menu)
    {

        $pivo = Submenu::find($menu);
        $abstracao = Menu::get();

        foreach($abstracao as $abs){
            if($pivo->id_menu == $abs->id_menu){
                $diretoria = $abs->id_diretoria;
                $idmenu = $abs->id_menu;
            }
        }

        if ($request->file('link_doc')->isValid()) {

            $nameFile = $request->file('link_doc')->getClientOriginalName();

            $split = explode("-", $request->data);

            $link_doc = $request->file('link_doc')->storeAs('arquivos/' . $split[0] . '/' . $split[1], $nameFile);

            Documentos::create([
                'titulo' => $request->titulo,
                'id_menu' => $idmenu,
                'id_sub_menu' => $menu,
                'data' => $request->data,
                'id_funcionario' => Auth::guard('admin')->user()->id_funcionario,
                'link_doc' => $link_doc,
                'id_diretoria' => $diretoria,
                'id_tipo' => $request->tipodocumento

            ]);

            return redirect()->route('list', [$menu])->with('success', 'Documento Cadastrado Com Sucesso!');
        } else {
            return back()->withErrors([
                abort(400, 'Nenhum arquivo foi enviado.')
            ]);
        }
    }


    public function save(StoreUpdateDoc $request, $menu)
    {

        $pivo = SubdoSub::find($menu);
        $abstracao = Submenu::get();

        foreach($abstracao as $abs){
            if($pivo->id_sub_menu == $abs->id_sub_menu){
                $idmenu = $abs->id_menu;
                $idsubmenu = $abs->id_sub_menu;
            }
        }

        $pivo2 = Menu::find($idmenu);
        $menuabs = Menu::get();

        foreach($menuabs as $abs){
            if($pivo2->id_menu == $abs->id_menu){
                $diretoria = $abs->id_diretoria;
            }
        }

        if ($request->file('link_doc')->isValid()) {

            $nameFile = $request->file('link_doc')->getClientOriginalName();

            $split = explode("-", $request->data);

            $link_doc = $request->file('link_doc')->storeAs('arquivos/' . $split[0] . '/' . $split[1], $nameFile);

            Documentos::create([
                'titulo' => $request->titulo,
                'id_menu' => $idmenu,
                'id_sub_s_menu' => $menu,
                'id_sub_menu' => $idsubmenu,
                'data' => $request->data,
                'id_funcionario' => Auth::guard('admin')->user()->id_funcionario,
                'link_doc' => $link_doc,
                'id_diretoria' => $diretoria,
                'id_tipo' => $request->tipodocumento

            ]);

            return redirect()->route('show', [$menu])->with('success', 'Documento cadastrado com sucesso!');
        } else {
            return back()->withErrors([
                abort(400, 'Nenhum arquivo foi enviado.')
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if (Auth::guard('admin')->check()) {

            $setores = Diretoria::get();
            $menus = Menu::get();
            $submenus = Submenu::get();
            $tiposdoc = TiposDocumento::get();
            $contador = 0;
            $documento = Documentos::find($id);
            $menu = $documento->id_sub_menu;

            $submenu = Submenu::find($menu);
            $abstracao = Menu::get();

                foreach($abstracao as $abs){
                    if($submenu->id_menu == $abs->id_menu){
                        $diretoria = $abs->id_diretoria;
                    }
                }

            $documento['link_doc'] = substr(strrchr($documento->link_doc, "/"), 1);
            return view('edit', compact('menu', 'documento', 'setores', 'diretoria', 'id', 'submenus', 'menus', 'tiposdoc', 'contador'));
        }

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }
        if (!$documento = Documentos::find($id)) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }

        $dados = $request->except('_token', '_method');

        $dados['id_funcionario'] =  Auth::guard('admin')->user()->id_funcionario;


        if ($request->link_doc && $request->file('link_doc')->isValid())
        {

            if (Storage::exists($documento->link_doc)) {
                Storage::delete($documento->link_doc);


        }
            $nameFile = $request->file('link_doc')->getClientOriginalName();
            $split = explode("-", $request->data);
            $dados['link_doc'] = $request->file('link_doc')->storeAs('arquivos/' . $split[0] . '/' . $split[1], $nameFile);

        }

        $documento->update($dados);
        return redirect()->route('list', ['menu' => $dados['menu']])->with('success', 'Documento Editado com sucesso!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        if ($documento = Documentos::find($request->id)) {

            if (Storage::exists($documento->link_doc)) {
                Storage::delete($documento->link_doc);
            }

            $documento->delete();

            return back()->with('success', 'O arquivo foi apagado!');
        }

        return back()->withErrors([
            abort(400, 'Nenhum arquivo foi enviado.')
        ]);
    }
}
