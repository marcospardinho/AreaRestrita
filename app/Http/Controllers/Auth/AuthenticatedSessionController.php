<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cadastro;
use App\Models\CaUsuarios;
use App\Models\ControleAcessos;
use App\Models\Endereco;
use App\Providers\RouteServiceProviderAdmin;
use Illuminate\Database\Eloquent\Builder;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */


    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function ajax($cpf)
    {
    
    $cpf = str_replace(".", "", $cpf);
    $cpfsemnada = str_replace("-", "", $cpf);

    $cpf = Cadastro::where('CPF_Cadastro',$cpfsemnada)->first();

    if($cpf == null){
        $cpfFunc = CaUsuarios::where('cpf',$cpfsemnada)->first();
        if($cpfFunc == null){
            echo "<p><font style='text-align: center; color: #3498db;'><i class='fas fa-exclamation-circle'></i></font> CPF não encontrado !</p>";
        }
    }
  
    }

    public function store(LoginRequest $request)
    {

        $request->validate([
            'cpf' => 'required',
            'password' => 'required',
        ]);
        $cpff = $request->cpf;
        $cpfsempontos = str_replace(".", "", $cpff);
        $cpfsemnada = str_replace("-", "", $cpfsempontos);



        $cpf = Cadastro::where('CPF_Cadastro',$cpfsemnada)->first();
        if($cpf == null){
            $cpfFunc = CaUsuarios::where('cpf',$cpfsemnada)->where('senha', hash("sha256",$request->password))->first();


            if($cpfFunc){
                Auth::guard('admin')->loginUsingId($cpfFunc->id_funcionario);
                $request->session()->regenerate();
                return redirect()->intended(RouteServiceProvider::HOME);

            }

                return back()->withErrors([
                'cpf' => 'As credenciais estão incorretas.',
                ])->withInput();
        }


        if ($cpf->siape[0]->password !== sha1($request->password)) {
            return back()->withErrors([
                'cpf' => 'As credenciais estão incorretas.',
                ])->withInput();
            }

        if($cpf->siape[0]->Filiado_Siape == 0){
            return back()->withErrors([
                'cpf' => 'As credenciais estão incorretas.',
                ])->withInput();
        }


        if(Auth::guard('web')->loginUsingId($cpf->Id_Cadastro)){


            $request->session()->regenerate();

            ControleAcessos::create([
                'Id_Cadastro' => Auth::guard('web')->user()->Id_Cadastro
            ]);
            return redirect()->intended(RouteServiceProvider::HOME);

        }

        return back()->withErrors([
        'cpf' => 'As credenciais estão incorretas.',
        ]);
}

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        if($request == null){
            return back();
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

