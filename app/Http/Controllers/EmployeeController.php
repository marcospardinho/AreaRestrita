<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAffiliated;
use App\Http\Requests\StoreFunc;
use App\Models\Cadastro;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Departamentos;
use App\Models\DocsComplementares;
use App\Models\Email;
use App\Models\Endereco;
use App\Models\Funcionario;
use App\Models\NovoFiliado;
use App\Models\Telefone;
use App\Models\Termo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!(Auth::guard('admin')->user()->departamento->id_setor == 7 || Auth::guard('admin')->user()->departamento->id_setor == 2)) {

            return back();
        }

        $_SESSION['valor'] = 0;
        $departamentos = Departamentos::get();
        $funcionarios = Funcionario::latest()->get();
        $contador = 0;
        return view('employee', compact('funcionarios', 'departamentos', 'contador'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $_SESSION['valor'] = 0;
        $setores = Departamentos::get();
        return view('auth.register', compact('setores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFunc $request)
    {
        $cpff = $request->cpf;
        $cpfsempontos = str_replace(".", "", $cpff);
        $cpfsemnada = str_replace("-", "", $cpfsempontos);

        $dados = [
            'nome_funcionario' => $request->nome,
            'cpf' => $cpfsemnada,
            'usuario' => $request->usuario,
            'id_setor' => $request->setor,
            'senha' => hash("sha256", $request->senha),
            'foto' => $request->foto,
            'email' => $request->email

        ];

        if ($request->foto && $request->file('foto')->isValid()) {

            $nameFile = $request->file('foto')->getClientOriginalName();

            $dados['foto'] = $request->file('foto')->storeAs('images', $nameFile);
        }
        $success = Funcionario::create($dados);
        if ($success) {
            return redirect()->route('employee')->with('message', 'Funcionario cadastrado com sucesso!');
        }
        return back()->withErrors([
            abort(400, 'Nenhum funcionario cadastrado.')
        ]);
    }


    public function orderAffiliated(StoreAffiliated $request)
    {

        $cpf = preg_replace("/[^0-9]/", "", $request->cpf);
        $cep = preg_replace("/[^0-9]/", "", $request->cep);
        $telefone = preg_replace("/[^0-9]/", "", $request->telefone);

        if ($user = Cadastro::where('CPF_Cadastro', $cpf)->first()) {
            foreach ($user->siape as $filiacao) {
                if ($filiacao->Filiado_Siape == 1) {
                    Alert::warning('Encontramos seu CPF em nossa base de dados, você será redirecionado para atualização de cadastro.', 'Em caso de dúvidas, entre em contato com o cadastro pelo telefone (61)3251-8114 | WhatsApp (61)98366-6111 ou E-mail cadastro@anfip.org.br')->autoClose(12000);
                    return redirect()->route('login');
                }
                return redirect()->route('profile');
            }
        }

        $codigo = $request->uf . $request->siape_matricula . $cpf;


        NovoFiliado::create([
            'siape' => $request->siape_matricula,
            'status' => $request->siape_status,
            'codigo' => $codigo,
            'nome' => $request->ncompleto,
            'cpf' => $cpf,
            'data_nascimento' => $request->dtnasc,
            'telefone' => $telefone,
            'email' => $request->email,
            'cep' => $cep,
            'endereco' => $request->endereco,
            'complemento' => $request->complemento,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'created_at' => now(),
            'updated_at' => now()

        ]);

        $novofiliado = NovoFiliado::where('cpf', $cpf)->first();

        switch($request->siape_status){
            case(1):
                if($request->dtadmissao !== null){
                    DocsComplementares::create([
                        'id_novo_filiado' => $novofiliado->id,
                        'data_adimissao' => $request->dtadmissao,
                        'created_at' => now(),
                        'updated_at' => now()
            
                    ]);

                    $ano = substr($request->dtadmissao, 0, 4);
                    $mes = substr($request->dtadmissao, 5, 2);
                    $dia = substr($request->dtadmissao, 8, 2);

                    $request["dt"] = "$dia/$mes/$ano";
                    break;
                }
                $request["dt"] = "";
                break;

            case(2):
                if($request->dtaposentadoria !== null){
                    DocsComplementares::create([
                        'id_novo_filiado' => $novofiliado->id,
                        'data_aposentadoria' => $request->dtaposentadoria,
                        'created_at' => now(),
                        'updated_at' => now()
            
                    ]);

                    $ano = substr($request->dtadmissao, 0, 4);
                    $mes = substr($request->dtadmissao, 5, 2);
                    $dia = substr($request->dtadmissao, 8, 2);

                    $request["dt"]= "$dia/$mes/$ano";
                    break;
                }

                $request["dt"] = "";
                break;

            case(3):
                if($request->ninstituidor == null && ($request->dtpensao == null && $request->sinstituidor == null)){
                    $request["dt"] = "";
                    $request["ninstituidor"] = "";
                    $request["dtpensao"] = "";
                    $request["sinstituidor"] = "";
                    break;
                }


                $complementares = new DocsComplementares;

                $complementares->id_novo_filiado = $novofiliado->id;
                $complementares->created_at = now();
                $complementares->updated_at = now();

                $ano = substr($request->dtpensao, 0, 4);
                $mes = substr($request->dtpensao, 5, 2);
                $dia = substr($request->dtpensao, 8, 2);

                    $request["dt"] = "$dia/$mes/$ano";
                
                    if($request->ninstituidor !== null){
                        $complementares->nome_instituidor = $request->ninstituidor;
                    }
                    else{
                        $request["nome_instituidor"] = "";
                    }

                    if($request->dtpensao !== null){
                        $complementares->data_obito_instituidor = $request->dtpensao;
                    }
                    else{
                        $request["dt"] = "";
                    }
                    
                    if($request->sinstituidor !== null){
                        $complementares->siape_instituidor = $request->sinstituidor;
                    }
                    else{
                        $request["siape_instituidor"] = "";
                    }

                    $complementares->save();

                break;
        }

        $request["ano"] = substr($request->dtnasc, 0, 4);
        $request["mes"] = substr($request->dtnasc, 5, 2);
        $request["dia"] = substr($request->dtnasc, 8, 2);

        Mail::send('mail.emailCadastroFiliado', ['token' => $request->siape_status, 'codigo' => $codigo, 'email' => $request->email, 'req' => $request], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Ficha de cadastro online - '. $request->ncompleto);
            $message->bcc('informatica@anfip.org.br');
            $message->bcc('cadastro@anfip.org.br');
        });

        alert()->html('<h5><b>Desejamos boas-vindas ao quadro associativo da ANFIP .</b> Clique no botão <b>"Acesse o SIGEPE"</b> Para concluir o processo de filiação, autorizando o desconto em folha através do <b>Sistema de Gestão de Acesso (SIGAC).</b> Caso não queira realizar o procedimento agora, clique em <b>"Adiar"</b>. </h5><br><h6>Caso tenha alguma dúvida em como fazer a autorização, <b>clique</b> <a href="../../dist/docs/passoapasso.pdf" target="_blank"><font color="red"><b>AQUI</b></font></a> e siga o passo a passo.</h6>')->persistent(false, false)
            ->showConfirmButton('<a target="__blank" href="https://sso.gestaodeacesso.planejamento.gov.br/cassso/login?service=https%3A%2F%2Fadmsistema.sigepe.planejamento.gov.br%2Fsigepe-as-web%2Fprivate%2FareaTrabalho%2Findex.jsf" style="min-width: 95px; margin-right:  5px; color: #fff;"
        type="button" class="btn" title="Acesse o SIGEPE">
        Acesse o SIGEPE</a>', '#3085d6')
            ->showCancelButton(' <a href="https://www.anfip.org.br/" style="min-width: 95px; margin-right:  5px; color: #fff;"
        type="button" class="btn" title="Adiar">
        Adiar</a>', '#aaa')->toHtml()->reverseButtons()->width('900px');

        return back();

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

    public function editProfile()
    {
        // if (!$termo = Termo::find(Auth::guard('web')->user()->Id_Cadastro)) {
        //     $alert = Alert::question('Termo de Imagem', '
        //     <div style="text-align: left;">Pelo presente TERMO DE AUTORIZAÇÃO PARA USO DE IMAGEM E VOZ, Eu, com dados descritos a seguir, aqui denominado(a) como TITULAR, autorizo que a ANFIP - Associação Nacional dos Auditores Fiscais da Receita Federal do Brasil, tido como CONTROLADOR, inscrito no CNPJ sob n°  03.636.693/0001-00, em razão da prestação de serviços de representante ou substituta processual, disponha dos meus dados pessoais, de acordo com os artigos 7° e 11 da Lei n° 13.709/2018, e também autorizo a utilização de minha imagem e/ou voz, consoante disposto neste instrumento:<br><br>
        //     CLÁUSULA PRIMEIRA<br><br>
        //     O TITULAR autoriza o CONTROLADOR a realizar o tratamento, ou seja, a utilizar os dados pessoais relacionados à divulgação de sua imagem e/ou voz, em áudio e vídeo, para finalidade de atualização cadastral, promoção da campanha publicitária de interesse do CONTROLADOR, ocorrendo a divulgação no seu site e demais mídias, online e offline, já existentes ou que venham a existir.<br>
        //     Parágrafo Primeiro: A autorização ora pactuada é feita de forma inteiramente gratuita, nada havendo a ser pleiteado ou recebido do CONTROLADOR seja a que título for, ficando desde já ajustando que o TITULAR concorda que nada tem a reclamar com relação à autorização ora concedida, em Juízo ou fora dele.<br>
        //     Parágrafo Segundo: Nenhuma das utilizações previstas no caput desta Cláusula, ou ainda qualquer outra que pretenda o CONTROLADOR dar às imagens e/ou vozes cuja utilização foi autorizada através deste Termo, têm limitação de tempo ou de número de vezes, podendo ocorrer no Brasil e/ou no exterior, sem que seja devida ao TITULAR qualquer remuneração.<br><br>
        //     CLÁUSULA SEGUNDA – Finalidade do Tratamento dos Dados<br><br>
        //     O Titular autoriza que o CONTROLADOR utilize sua imagem com a finalidade de divulgação de campanha publicitária de seu interesse, adotando todas as medidas de proteção de dados, visando a preservação de seu direito à intimidade, coibindo o uso com finalidade distinta prevista neste termo.<br>
        //     Parágrafo Primeiro: Caso seja necessário o compartilhamento de dados com terceiros que não tenham sido relacionados nesse termo ou qualquer  alteração contratual posterior, será ajustado novo termo de consentimento para este fim (§ 6° do artigo 8° e § 2° do artigo 9° da Lei n° 13.709/2018).<br>
        //     Parágrafo Segundo: Em caso de alteração na finalidade, que esteja em desacordo com o consentimento original, o CONTROLADOR deverá comunicar o TITULAR, que poderá revogar o consentimento, conforme previsto na cláusula sexta.<br>
        //     Parágrafo Terceiro: O TITULAR se compromete a não inspecionar ou aprovar a arte final ou qualquer material relacionado ao uso de sua imagem e/ou voz ora concedido, ficando acordado que o CONTROLADOR se obriga a não utilizar os direitos de sua personalidade de forma pejorativa ou distorcida.<br><br>
        //     CLÁUSULA TERCEIRA – Compartilhamento de Dados<br><br>
        //     A CONTROLADOR fica autorizada a compartilhar os dados pessoais do Titular com outros agentes de tratamento de dados, caso seja necessário para as finalidades previstas neste instrumento, desde que sejam respeitados os princípios da boa-fé, finalidade, adequação, necessidade, livre acesso, qualidade dos dados, transparência, segurança, prevenção, não discriminação e responsabilização e prestação de contas.<br><br>
        //     CLÁUSULA QUARTA – Responsabilidade pela Segurança dos Dados<br><br>
        //     Fica o CONTROLADOR responsabilizado por manter medidas de segurança, técnicas e administrativas suficientes a proteger os dados pessoais do Titular e à Autoridade Nacional de Proteção de Dados (ANPD), comunicando ao TITULAR, caso ocorra algum incidente de segurança que possa acarretar risco ou dano relevante, conforme artigo 48 da Lei n° 13.709/2020.<br><br>
        //     CLÁUSULA QUINTA – Término do Tratamento dos Dados<br><br>
        //     Ao CONTROLADOR, é permitido manter e utilizar os dados pessoais do Titular durante todo o período contratualmente firmado para as finalidades relacionadas nesse termo e ainda após o término da contratação para cumprimento de obrigação legal ou impostas por órgãos de fiscalização, nos termos do artigo 16 da Lei n° 13.709/2018.<br><br>
        //     CLÁUSULA SEXTA – Direito de Revogação do Consentimento<br><br>
        //     O TITULAR poderá revogar seu consentimento, a qualquer tempo, por e-mail ou por carta escrita, conforme o artigo 8°, § 5°, da Lei n° 13.709/2020.<br><br>
        //     CLÁUSULA SÉTIMA – Tempo de Permanência dos Dados Recolhidos<br><br>
        //     O TITULAR fica ciente de que O CONTROLADOR deverá permanecer com os seus dados pelo período mínimo necessário à finalidade publicitária ora estabelecida.</div>')->persistent(false, false)
        //         ->showConfirmButton('<a href="/termAccepted" style="min-width: 95px; margin-right:  5px; color: #fff;"
        //     type="button" class="btn" title="Concordo">
        //     Concordo</a>', '#3085d6')
        //         ->showCancelButton(' <a href="/dashboard" style="min-width: 95px; margin-right:  5px; color: #fff;"
        //     type="button" class="btn" title="Não concordo">
        //     Voltar</a>', '#aaa')->toHtml()->reverseButtons()->width('900px');
        // }

        return view('profile');
    }

    public function storeProfile(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }
        if (!$associado = Cadastro::find(Auth::guard('web')->user()->Id_Cadastro)) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }

        $dadosNovos = $request->except('_token');
        $countTel = Telefone::where('Id_Cadastro', Auth::guard('web')->user()->Id_Cadastro)->count();
        $countEmail = Email::where('Id_Cadastro', Auth::guard('web')->user()->Id_Cadastro)->count();

        $dadosNovos["cep"] = preg_replace("/[^0-9]/", "", $request->cep);
        
        if(Auth::guard('web')->user()->email->count() !== 0){
            $dadosAntigos["email1"] = Auth::guard('web')->user()->email[0]->Email_Email;
            $dadosAntigos["email1_tipo"] = Auth::guard('web')->user()->email[0]->tipo_Email;
            $email = Email::find(Auth::guard('web')->user()->email[0]->Id_Email);

        }
        else{
            $dadosAntigos["email1"] = '';
            $dadosAntigos["email1_tipo"] = "Vazio";

        }
        
        if($countTel !== 0){
            $telefones = Telefone::find(Auth::guard('web')->user()->telefone[0]->Id_Telefone);
            $dadosAntigos["DDD_telefone1"] = Auth::guard('web')->user()->telefone[0]->DDD_Telefone;
            $dadosAntigos["telefone1"] = Auth::guard('web')->user()->telefone[0]->Numero_Telefone;
            $dadosAntigos["telefone1_tipo"] = Auth::guard('web')->user()->telefone[0]->Tipo_Telefone;
        }
        else{
            $dadosAntigos["DDD_telefone1"] = '';
            $dadosAntigos["telefone1"] = '';
            $dadosAntigos["telefone1_tipo"] = "Vazio";

        }

        if(Auth::guard('web')->user()->endereco == null){

            $dadosAntigos["cep"] = "Vazio";
            $dadosAntigos["uf"] = "Vazio";
            $dadosAntigos["numero"] = "Vazio";
            $dadosAntigos["bairro"] = "Vazio";
            $dadosAntigos["complemento"] = "Vazio";
            $dadosAntigos["cidade"] = "Vazio";
            $dadosAntigos["endereco"] = "Vazio";
        }
        else{

            $endereco = Endereco::find(Auth::guard('web')->user()->endereco->Id_Endereco);

            $dadosAntigos["cep"] = Auth::guard('web')->user()->endereco->CEP_Endereco;
            $dadosAntigos["uf"] = Auth::guard('web')->user()->endereco->UF_Endereco;
            $dadosAntigos["numero"] = Auth::guard('web')->user()->endereco->Numero_Endereco;
            $dadosAntigos["bairro"] = Auth::guard('web')->user()->endereco->Bairro_Endereco;
            $dadosAntigos["complemento"] = Auth::guard('web')->user()->endereco->Complemento_Endereco;
            $dadosAntigos["cidade"] = Auth::guard('web')->user()->endereco->Cidade_Endereco;
            $dadosAntigos["endereco"] = Auth::guard('web')->user()->endereco->Endereco_Endereco;
        }

            if($countTel >= 2){
                $dadosAntigos["telefone2"] = Auth::guard('web')->user()->telefone[1]->Numero_Telefone;
                $dadosAntigos["telefone2_tipo"] = Auth::guard('web')->user()->telefone[1]->Tipo_Telefone;
                $dadosAntigos["DDD_telefone2"] = Auth::guard('web')->user()->telefone[1]->DDD_Telefone;

            }
            else{
                $dadosAntigos["telefone2"] = '';
                $dadosAntigos["telefone2_tipo"] = "Vazio";
                $dadosAntigos["DDD_telefone2"] = '';
            }

            if($countEmail >= 2){
                $dadosAntigos["email2"] = Auth::guard('web')->user()->email[1]->Email_Email;
                $dadosAntigos["email2_tipo"] = Auth::guard('web')->user()->email[1]->tipo_Email;

            }
            else{
                $dadosAntigos["email2"] = '';
                $dadosAntigos["email2_tipo"] = 'Vazio';


            }

        $telefone = preg_replace("/[^0-9]/", "", $request->telefone1);
        $ddd = substr($telefone, 0, 2);
        $dadosNovos["DDD_telefone1"] = $ddd;
        $contagem = strlen($telefone);
        $telefone = substr($telefone, 2, $contagem);
        $dadosNovos["telefone1"] = $telefone;

        if($countTel == 0){
            $idcadastro = Auth::guard('web')->user()->Id_Cadastro;
            
            DB::connection('sqlsrv')->insert(DB::raw("
            sp_login 'AreaRestrita_Anfip';
            INSERT INTO Telefone (Id_Cadastro, DDD_Telefone, Numero_Telefone, Tipo_Telefone, Contato_Telefone)
            VALUES ('$idcadastro', '$ddd', '$telefone', '$request->telefone1_tipo', '0');"));
        }
        else{
            DB::connection('sqlsrv')->update(DB::raw("
            sp_login 'AreaRestrita_Anfip';
            UPDATE Telefone SET DDD_Telefone = '$ddd', Numero_Telefone = '$telefone', Tipo_Telefone = '$request->telefone1_tipo', Contato_Telefone = '1' WHERE Id_Telefone = '$telefones->Id_Telefone' ;"));
        }

        if ($request->telefone2 !== null) {
            if ($countTel <= 1) {
                $telefone = preg_replace("/[^0-9]/", "", $request->telefone2);
                $ddd = substr($telefone, 0, 2);
                $dadosNovos["DDD_telefone2"] = $ddd;
                $contagem = strlen($telefone);
                $telefone = substr($telefone, 2, $contagem);
                $dadosNovos["telefone2"] = $telefone;
                $idcadastro = Auth::guard('web')->user()->Id_Cadastro;

                DB::connection('sqlsrv')->insert(DB::raw("
                    sp_login 'AreaRestrita_Anfip';
                    INSERT INTO Telefone (Id_Cadastro, DDD_Telefone, Numero_Telefone, Tipo_Telefone, Contato_Telefone)
                    VALUES ('$idcadastro', '$ddd', '$telefone', '$request->telefone2_tipo', '0');"));
            }
            else {
                $telefones = Telefone::find(Auth::guard('web')->user()->telefone[1]->Id_Telefone);
                $telefone = preg_replace("/[^0-9]/", "", $request->telefone2);
                $ddd = substr($telefone, 0, 2);
                $dadosNovos["DDD_telefone2"] = $ddd;
                $contagem = strlen($telefone);
                $telefone = substr($telefone, 2, $contagem);
                $dadosNovos["telefone2"] = $telefone;
                DB::connection('sqlsrv')->update(DB::raw("
                    sp_login 'AreaRestrita_Anfip';
                    UPDATE Telefone SET DDD_Telefone = '$ddd', Numero_Telefone = '$telefone', Tipo_Telefone = '$request->telefone2_tipo', Contato_Telefone = '0' WHERE Id_Telefone = '$telefones->Id_Telefone' ;"));
            }
        }
        else {
            $dadosNovos["telefone2"] = '';
            $dadosNovos["telefone2_tipo"] = 'Vazio';
            $dadosNovos["DDD_telefone2"] = '';
            if ($countTel >= 2) {
                $idTelefone = Auth::guard('web')->user()->telefone[1]->Id_Telefone;
                DB::connection('sqlsrv')->delete(DB::raw("
                    sp_login 'AreaRestrita_Anfip';
                    DELETE FROM Telefone WHERE Id_Telefone = '$idTelefone' ;"));

            }
        }

        $bairro = preg_replace('/\W+/u', " " , $request->bairro);

        $cep = preg_replace("/[^0-9]/", "", $request->cep);
        $dt = now();

            if(Auth::guard('web')->user()->endereco == null){
                $idcadastro = Auth::guard('web')->user()->Id_Cadastro;
                
                DB::connection('sqlsrv')->insert(DB::raw("
                sp_login 'AreaRestrita_Anfip';
                INSERT INTO Endereco (Id_Cadastro, Endereco_Endereco, Bairro_Endereco, Cidade_Endereco, UF_Endereco, CEP_Endereco, Numero_Endereco, Complemento_Endereco, dtatualizacao_Endereco, Correspondencia_Endereco, Tipo_Endereco)
                VALUES ('$idcadastro', '$request->endereco', '$bairro', '$request->cidade', '$request->uf', '$cep', '$request->numero', '$request->complemento', '$dt', '1', 'Residencial');"));
            }
            else{
                DB::connection('sqlsrv')->update(DB::raw("
                sp_login 'AreaRestrita_Anfip';
                UPDATE Endereco SET Endereco_Endereco = '$request->endereco', Bairro_Endereco = '$bairro', Cidade_Endereco = '$request->cidade', UF_Endereco = '$request->uf',
                CEP_Endereco = '$cep', Numero_Endereco = '$request->numero', Complemento_Endereco = '$request->complemento', dtatualizacao_Endereco = '$dt', Correspondencia_Endereco = '1', Tipo_Endereco = 'Residencial' WHERE Id_Endereco = '$endereco->Id_Endereco' ;"));
            }

        if(Auth::guard('web')->user()->email->count() !== 0){
            DB::connection('sqlsrv')->update(DB::raw("
            sp_login 'AreaRestrita_Anfip';
            UPDATE Email SET Email_Email = '$request->email1', tipo_Email = '$request->email1_tipo', status_Email = 'Ativo' WHERE Id_Email = '$email->Id_Email' ;"));
        }
        else{
            $idcadastro = Auth::guard('web')->user()->Id_Cadastro;
            DB::connection('sqlsrv')->insert(DB::raw("
                sp_login 'AreaRestrita_Anfip';
                INSERT INTO Email (Id_Cadastro, Email_Email, tipo_Email, status_Email)
                VALUES ('$idcadastro', '$request->email1', '$request->email1_tipo', 'Ativo');"));
        }

        if ($request->email2 !== null) {
            if ($countEmail <= 1) {
                $idcadastro = Auth::guard('web')->user()->Id_Cadastro;
                DB::connection('sqlsrv')->insert(DB::raw("
                    sp_login 'AreaRestrita_Anfip';
                    INSERT INTO Email (Id_Cadastro, Email_Email, tipo_Email, status_Email)
                    VALUES ('$idcadastro', '$request->email2', '$request->email2_tipo', 'Inativo');"));
            }
            else {
                $email = Email::find(Auth::guard('web')->user()->email[1]->Id_Email);
                DB::connection('sqlsrv')->update(DB::raw("
                    sp_login 'AreaRestrita_Anfip';
                    UPDATE Email SET Email_Email = '$request->email2', tipo_Email = '$request->email2_tipo', status_Email = 'Inativo' WHERE Id_Email = '$email->Id_Email' ;"));
            }
        }
        else {
            $dadosNovos["email2"] = '';
            $dadosNovos["email2_tipo"] = 'Vazio';
            if ($countEmail >= 2) {
                $idEmail = Auth::guard('web')->user()->email[1]->Id_Email;
                DB::connection('sqlsrv')->delete(DB::raw("
                        sp_login 'AreaRestrita_Anfip';
                        DELETE FROM Email WHERE Id_Email = '$idEmail' ;"));

            }
        }

        if ($request->foto && $request->file('foto')->isValid()) {

            $name = $associado->Nome_Cadastro;
            if (Storage::exists($associado->foto)) {
                Storage::delete($associado->foto);
            }

            $nameFile = $request->file('foto')->getClientOriginalName();
            $foto = $request->file('foto')->storeAs('images/associados/' . $name, $nameFile);
            $id = $associado->Id_Cadastro;

            DB::connection('sqlsrv')->update(DB::raw("
                sp_login 'AreaRestrita_Anfip';
                UPDATE Cadastro SET foto = '$foto' WHERE Id_Cadastro = '$id' ;"));
        }

        $NomeAssociado = Auth::guard('web')->user()->Nome_Cadastro;
        $siape = Auth::guard('web')->user()->siape[0]->Matricula_Siape;
        $emailUser = $dadosNovos["email1"];

        Mail::send('mail.emailAtualizacao', ['nome' => $NomeAssociado, 'siape' => $siape, 'dadosAntigos' => $dadosAntigos, 'dadosNovos' => $dadosNovos], function ($message) use ($emailUser, $NomeAssociado){
            $message->to($emailUser);
            $message->subject('Comprovante de Atualização Cadastral - ' .  $NomeAssociado);
            $message->bcc('informatica@anfip.org.br');
            $message->bcc('cadastro@anfip.org.br');

        });

        return redirect()->route('dashboard')->with('success', 'Atualização Concluída');
    }

    public function removeProfile()
    {

        if (!Auth::guard('web')->check()) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }

        if (!$associado = Cadastro::find(Auth::guard('web')->user()->Id_Cadastro)) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }

        if (Storage::exists($associado->foto)) {
            Storage::delete($associado->foto);
        }

        $id = $associado->Id_Cadastro;

        $result = DB::connection('sqlsrv')->update(DB::raw("
           sp_login 'AreaRestrita_Anfip';
           UPDATE Cadastro SET foto = null WHERE Id_Cadastro = '$id' ;"));

        if ($result) {
            return redirect()->route('dashboard')->with('success', 'A foto foi removida');
        }

        return back()->with('error', 'Erro ao cadastrar foto. Tente Novamente');
    }

    public function createAcess()
    {
        try {
            Termo::create([
                'Id_Cadastro' => Auth::guard('web')->user()->Id_Cadastro
            ]);

            return redirect()->route('profile');
        } catch (\Throwable $th) {
            return redirect()->route('dashboard')->with('warning', 'Você não pode acessar essa página');
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
            $_SESSION['valor'] = 0;
            $setores = Departamentos::get();
            $funcionario = Funcionario::find($id);
            return view('employeedit', compact('funcionario', 'setores'));
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
        if (!$funcionario = Funcionario::find($id)) {
            return back()->withErrors([
                abort(500, 'Não autorizado.')
            ]);
        }

        $dados = $request->except('_token', '_method');

        if ($dados['cpf'] != null) {
            $cpf = str_replace(".", "", $dados['cpf']);
            $dados['cpf'] = str_replace("-", "", $cpf);
        };

        if ($dados['senha'] !=  $funcionario->senha) {
            $dados['senha'] =  hash("sha256", $dados['senha']);
        };

        if ($request->foto && $request->file('foto')->isValid()) {

            if (Storage::exists($funcionario->foto)) {
                Storage::delete($funcionario->foto);
            }

            $nameFile = $request->file('foto')->getClientOriginalName();
            $dados['foto'] = $request->file('foto')->storeAs('images', $nameFile);
        }
        $funcionario->update($dados);
        return redirect()->route('employee')->with('message', 'Documento Editado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($funcionario = Funcionario::find($request->id)) {

            if (Storage::exists($funcionario->foto)) {
                Storage::delete($funcionario->foto);
            }

            $funcionario->delete();

            return back()->with('message', 'O arquivo foi apagado!');
        }

        return back()->withErrors([
            abort(400, 'Nenhum arquivo foi enviado.')
        ]);
    }
}
