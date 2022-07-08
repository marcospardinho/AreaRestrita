<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cadastro;
use App\Models\CaUsuarios;
use App\Models\Funcionario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class NewPasswordController extends Controller
{


    /**
     * Display the password reset view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)->first();
        if (!$tokenData) {
            Alert::error('Link inválido, Faça outra solicitação', 'Para mais informações entre em contato com o suporte')->autoClose(5000);
            return redirect()->route('password.request');
        }
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'cpf' => 'required|min:11',
            'token' => 'required',
            'password' => 'required|string|confirmed|min:5',
        ]);

        $cpf = $request->cpf;
        $cpf = str_replace(".", "", $cpf);
        $cpf = str_replace("-", "", $cpf);

        if ($request->type != 1 && $request->type != 2) {
            Alert::error('Tipo inválido')->persistent(false, false);
            return back();
        }

        if ($request->type == 1) {

            $updatePassword = DB::table('password_resets')
                ->where(['email' => $request->email, 'token' => $request->token])
                ->first();

            if (!$updatePassword) {
                Alert::error('Token inválido, Faça outra solicitação', 'Para mais informações entre em contato com o suporte')->persistent(false, false);
                return back();
            }

            $user = Funcionario::where('cpf', $cpf)->where('email', $request->email)->first();

            if (empty($user)) {
                Alert::error('O CPF não corresponde ao utilizado para solicitar a recuperação!', 'Digite seu CPF corretamente.')->persistent(false, false);
                return back()->withInput();
            }


            $updated = $user->update(['senha' => hash("sha256", $request->password)]);


            if ($updated) {
                Mail::send('mail.emailConfirmation', ['user' => $user], function ($message) use ($user) {
                    $message->to($user->email);
                    $message->subject('Senha Alterada com Sucesso!');
                    $message->bcc('informatica@anfip.org.br');
                });
                DB::table('password_resets')->where(['email' => $request->email])->delete();
                Alert::success('Sua senha foi alterada com sucesso!')->autoClose(5000);
                return redirect()->route('login');
            }


            return back()->withErrors([
                'cpf' => 'As credenciais estão incorretas.',
            ])->withInput();
        }


        $updatePassword = DB::table('password_resets')
            ->where(['email' => $request->email, 'token' => $request->token])
            ->first();

        if (!$updatePassword) {
            Alert::error('Token inválido, Faça outra solicitação', 'Para mais informações entre em contato com o suporte')->persistent(false, false);
            return back();
        }

        $novasenha =  sha1($request->password);
        $email = $request->email;
        $user = Cadastro::where('CPF_Cadastro', $cpf)->whereHas('email', function ($q) use ($email) {
            $q->where('Email_Email', '=', $email);
        })->first();

        if (empty($user)) {
            Alert::error('O CPF não corresponde ao utilizado para solicitar a recuperação!', 'Digite seu CPF corretamente.')->persistent(false, false);
            return back()->withInput();
        }

        $updated = DB::connection('sqlsrv')->update(DB::raw("
            sp_login 'AreaRestrita_Anfip'
            update siape set password = '$novasenha'
            from siape    s
            join Cadastro c ON c.Id_Cadastro = s.Id_Cadastro
            where c.CPF_Cadastro = '$cpf'"));

        if ($updated) {
            Mail::send('mail.emailConfirmation', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email[0]->Email_Email);
                $message->subject('Senha Alterada com Sucesso!');
                $message->bcc('informatica@anfip.org.br');
            });
            DB::table('password_resets')->where(['email' => $request->email])->delete();
            Alert::success('Sua senha foi alterada com sucesso!')->autoClose(5000);
            return redirect()->route('login');
        }

        return back()->withErrors([
            'cpf' => 'As credenciais estão incorretas.',
        ])->withInput();
    }
}
