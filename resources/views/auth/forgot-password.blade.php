<link rel="shortcut icon" href="https://www.anfip.org.br/wp-content/uploads/2020/12/76x76.png">
<div class="hold-transition login-page">
    <div class="login-box">
        <x-guest-layout>
            <x-auth-card>

                <!-- Google Font: Source Sans Pro -->
                <link rel="stylesheet"
                    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
                <!-- Font Awesome -->
                <link rel="stylesheet" href="{{ url('plugins/fontawesome-free/css/all.min.css') }}">
                <!-- icheck bootstrap -->
                <link rel="stylesheet" href="{{ url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
                <!-- Theme style -->
                <link rel="stylesheet" href="{{ url('dist/css/adminlte.min.css') }}">

                <link rel="stylesheet" href="{{ url('dist/css/alt/adminlte.sobre.css') }}">
                    
                <!-- jQuery -->
                <script src="{{ url('plugins/jquery/jquery.min.js') }}"></script>
                <!-- Bootstrap 4 -->
                <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

                <!-- jquery-validation -->
                <script src="../../plugins/jquery-validation/jquery.validate.min.js"></script>
                <script src="../../plugins/jquery-validation/additional-methods.min.js"></script>
                <script src="../../plugins/jquery-validation/jquery.mask.min.js"></script>
                <!-- Bootstrap 4 -->
                <script src="{{ url('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
                <!-- AdminLTE App -->
                <script src="{{ url('dist/js/adminlte.min.js') }}"></script>

                <script>
                    $(function() {
                        $.validator.setDefaults({

                        });
                        $('#quickForm').validate({
                            rules: {
                                cpf: {
                                    required: true,
                                    minlength: 11
                                },
                            },
                            errorElement: 'x-input',
                            errorPlacement: function(error, element) {
                                error.addClass('invalid-feedback');
                                element.closest('.form-control').append(error);
                            },
                            highlight: function(element, errorClass, validClass) {
                                $(element).addClass('is-invalid');
                            },
                            unhighlight: function(element, errorClass, validClass) {
                                $(element).removeClass('is-invalid');
                            }
                        });
                    });
                </script>
                <script type="text/javascript">
                    jQuery(function($) {
                        $("#cpf").mask("000.000.000-00");
                    });
                </script>


                <div class="card card-outline card-primary">
                    <div class="card-header text-center">
                        <img src="../../dist/img/AdminLTELogoH.png" alt="User Avatar" class="img-size-500 logo-anfip">
                    </div>




                    <div class="card">
                        <div class="card-body login-card-body">
                            <div class="mb-4 text-sm text-gray-600">
                                {{ __('Esqueceu sua senha? Digite seu CPF que enviaremos um email com um link para redefinição de sua senha.') }}
                            </div>



                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4" :status="session('status')" />

                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />

                            <form method="POST" action="{{ route('password.email') }}">
                               <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <!-- Email Address -->
                                <div class="input-group mb-3">


                                    <x-input id="cpf" class="form-group form-control" type="text" name="cpf"
                                        placeholder="CPF" maxlength="11" :value="old('cpf')" required autofocus />
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-id-card"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="icheck-primary">

                                    <a class="underline text-sm text-gray-600 hover:text-gray-900"
                                        href="{{ route('login') }}">
                                        {{ __('Voltar para login') }}
                                    </a>

                                </div>


                                <div class="flex items-center justify-end mt-4">
                                    <button type="submit"
                                    class="btn btn-primary btn-block inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                    onclick="event.preventDefault();
                                     this.disabled=true;
                                     this.value='Enviando';
                                     this.closest('form').submit(); ">
                                    Enviar redefinição de senha
                                </button>
                                </div>
                            </form>
                        </div>
                    </div>
            </x-auth-card>


        </x-guest-layout>
        @include('sweetalert::alert')
    </div>
</div>
