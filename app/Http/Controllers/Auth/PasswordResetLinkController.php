<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cadastro;
use App\Models\CaUsuarios;
use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
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
        ]);

        $cpf = $request->cpf;
        $cpf = str_replace(".", "", $cpf);
        $cpf = str_replace("-", "", $cpf);

        $user = Cadastro::where('CPF_Cadastro', $cpf)->first();

        if ($user == null) {

            $user = CaUsuarios::where('cpf', $cpf)->first();
            if ($user == null) {
                Alert::error('CPF não encontrado!', 'Para mais informações entre em contato com o suporte')->persistent(false, false);
                return back()->withInput();
            }


            if ($user->email == null) {
                Alert::error('E-mail não encontrado!', 'Entre em contato com o cadastro pelo telefone (61)3251-8114 | WhatsApp (61)98366-6111 ou E-mail cadastro@anfip.org.br')->persistent(false, false);
                return back();
            }


            if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                Alert::error('Seu Email cadastrado não é válido!', 'Para mais informações entre em contato com o suporte')->persistent(false, false);
                return back();
            }

            $checagem = DB::connection('mysql')->select(DB::raw(" SELECT * FROM password_resets WHERE email = '$user->email' "));

            if (!empty($checagem)) {
                DB::table('password_resets')->where(['email' => $user->email])->delete();
            }

            $token = Str::random(64);

            DB::table('password_resets')->insert(
                ['email' => $user->email, 'token' => $token, 'created_at' => Carbon::now()]
            );

            $emailname = strstr($user->email, '@', true);
            $gmailname = strstr($user->email, '@', false);
            $emailview = substr($emailname, 0, 4);

            Mail::send('mail.mailview', ['token' => $token, 'user' => $user, 'type' => 1], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Redefinição de senha');
                $message->bcc('informatica@anfip.org.br');
            });

            Alert::success('Enviamos um link para redefinição de senha no E-mail<br>' . $emailview . '*******' . $gmailname . '!')->persistent(false, false);
            return back();
        }

        if ($user->siape[0]->Filiado_Siape == 0) {
            Alert::error('CPF não encontrado!', 'Para mais informações entre em contato com o suporte')->persistent(false, false);
            return back()->withInput();
        }

        if ($user->email->count() == 0) {
            Alert::error('E-mail não encontrado!', 'Entre em contato com o cadastro pelo telefone (61)3251-8114 | WhatsApp (61)98366-6111 ou E-mail cadastro@anfip.org.br')->persistent(false, false);
            return back();
        }

        foreach ($user->email as $mail) {
            if ($mail->status_Email == "Ativo" && $mail->tipo_Email == "Pessoal") {
                $email = $mail->Email_Email;
                break;
            }
        }
        
            if (empty($email)) {
                foreach ($user->email as $mail) {
                    if ($mail->tipo_Email == "Pessoal") {
                        $email = $mail->Email_Email;
                        break;
                    }
                }
            }

            if (empty($email)) {
                foreach ($user->email as $mail) {
                    if ($mail->status_Email == "Ativo") {
                        $email = $mail->Email_Email;
                        break;
                    }
                }
            }
        
        if (empty($email)) {
            $email = $user->email[0]->Email_Email;
        }
        

        $emailname = strstr($email, '@', true);
        $gmailname = strstr($email, '@', false);
        $emailview = substr($emailname, 0, 4);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Alert::error('E-mail não encontrado!', 'Entre em contato com o cadastro pelo telefone (61)3251-8114 | WhatsApp (61)98366-6111 ou E-mail cadastro@anfip.org.br')->persistent(false, false);
            return back();
        }

        $checagem = DB::connection('mysql')->select(DB::raw(" SELECT * FROM password_resets WHERE email = '$email' "));

        if (!empty($checagem)) {
            DB::table('password_resets')->where(['email' => $email])->delete();
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert(
            ['email' => $email, 'token' => $token, 'created_at' => Carbon::now()]
        );
        Mail::send('mail.mailview', ['token' => $token, 'email' => $email, 'user' => $user, 'type' => 2], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Redefinição de senha');
            $message->bcc('informatica@anfip.org.br');
        });

        Alert::success('Enviamos um link para redefinição de senha no E-mail<br>' . $emailview . '*******' . $gmailname . '!')->persistent(false, false);
        return back();
    }
}