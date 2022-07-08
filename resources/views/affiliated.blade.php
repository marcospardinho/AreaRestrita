<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cadastro de filiado</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
    <script src="../../plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="../../plugins/jquery-validation/jquery.mask.min.js"></script>

    <style>
        .logo-register {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

    </style>

</head>

<body>
    <div class="col-md-6 container-fluid" style="margin-top: 7px;">
        <div class="card card-primary">
            <div class="card-header" style='background-color: #075d99;'>
                <h3 class="card-title">Formulário de Cadastro de Associado</h3>
            </div>
            <div class="card-body">
                <div class="card-header">
                    <img width="400" src="https://www.anfip.org.br/wp-content/uploads/2021/05/71-anos_ANFIP_pagina.png"
                        class="logo-register" alt="logo">

                </div>
                <!-- Date -->
                <br>
                <form class="needs-validation" novalidate id="formAffilied" name="formAffilied"
                    action="{{ route('affilied.store') }}" enctype="multipart/form-data" method="post">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <!-- Conteudo da Abas -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <div class="row">
                        <div class="col-sm-6">
                            <!-- select -->
                            <div class="form-group">
                                <label>Status</label> <font color="red"><b>*</b></font>
                                <select class="custom-select" name="siape_status" id="siape_status">
                                    <option value='1'>Ativo</option>
                                    <option value='2'>Aposentado</option>
                                    <option value='3'>Pensionista</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="form-group">
                                    <label>Siape</label> <font color="red"><b>*</b></font>
                                    <input type="text" name="siape_matricula"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        id="siape_matricula"   class="form-control"
                                        placeholder="Matrícula siape">
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- /.inicio da Divisão de Documentos do Ativo -->

                        <div id="divAtivo" style="">
                            <h5 class="mt-4 mb-2">Dados Adicionais</h5>
                            <div class="row">
                            <div class="col-sm-6">
                            <div class="form-group">
                                <label>Data de Admissão:</label>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="date" id="dtadmissao" name="dtadmissao" class="form-control"
                                    data-target="#reservationdate" onchange="" />
                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <!-- /.Fim da Divisão de Documentos do Ativo -->

                        <!-- /.Inicio da Divisão de Documentos do Pensionista -->

                        <div id="divPensionista" style="display : none;">
                        <h5 class="mt-4 mb-2">Dados Adicionais</h5>
                        <div class="row">
                        <div class="col-sm-4">
                        <div class="form-group">
                            <label>Siape do Instituidor:</label>
                            <div class="input-group date" data-target-input="nearest">
                                <input type="text" id="sinstituidor" name="sinstituidor" class="form-control"
                                data-target="#reservationdate" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" pattern="[0-9]+$" />
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            </div>
                        </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                        <div class="form-group">
                            <label>Nome do Instituidor:</label>
                            <div class="input-group date" data-target-input="nearest">
                                <input type="text" id="ninstituidor" name="ninstituidor" class="form-control"
                                data-target="#reservationdate" onchange="verificar2()" />
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fas fa-user"></i></div>
                            </div>
                        </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                        <div class="form-group">
                            <label>Data de Óbito do Instituidor:</label>
                            <div class="input-group date" data-target-input="nearest">
                                <input type="date" id="dtpensao" name="dtpensao" class="form-control"
                                data-target="#reservationdate" onchange="" />
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- /.Fim da Divisão de Documentos do Pensionista -->

                    <!-- /.inicio da Divisão de Documentos do Aposentado -->

                    <div id="divAposentado" style="display : none;">
                        <h5 class="mt-4 mb-2">Dados Adicionais</h5>
                        <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label>Data Aposentadoria:</label>
                            <div class="input-group date" data-target-input="nearest">
                                <input type="date" id="dtaposentadoria" name="dtaposentadoria" class="form-control"
                                data-target="#reservationdate" onchange="" />
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- /.Fim da Divisão de Documentos do Aposentado -->

                        <h5 class="mt-4 mb-2">Dados Pessoais</h5>
                        <div class="form-group">
                            <label>Nome completo:</label> <font color="red"><b>*</b></font>
                            <div class="input-group date" data-target-input="nearest">
                                <input type="text" id="ncompleto" name="ncompleto" class="form-control"
                                data-target="#reservationdate" onchange="verificar()" />
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fas fa-user"></i></div>
                            </div>
                        </div>
                        <!-- /.input group -->
                    </div>
                    <!-- /.form group -->

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>CPF:</label> <font color="red"><b>*</b></font>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="text" id="cpf" name="cpf" class="form-control"
                                        maxlength="13" data-target="#reservationdate" onblur="validarCPF(this)" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-sm-4">
                            <div class="form-group">
                                <label>Sexo:</label>
                                <div class="input-group date" data-target-input="nearest">
                                <select class="form-control" id="sexo" name="sexo">
                                <option value="" >Masculino</option>
                                <option value="" >Feminino</option>
                                <option value="" >Não Binario</option>
                                </select>
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-restroom"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                        <div class="col-sm-6">
                            <!-- Date and time -->
                            <div class="form-group">
                                <label>Data de Nascimento</label> <font color="red"><b>*</b></font>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="date" id="dtnasc" name="dtnasc"
                                        class="form-control datetimepicker-input" data-target="#reservationdatetime" />
                                    <div class="input-group-append" data-target="#reservationdatetime"
                                        data-toggle="datetimepicker">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <h5 class="mt-4 mb-2">Contato</h5>
                    <!-- /.form group -->
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Telefone:</label> <font color="red"><b>*</b></font>

                                <div class="input-group">

                                    <input type="text" class="form-control" name="telefone" id="telefone">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                </div>
                                <!-- /.input group -->
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label>Email:</label> <font color="red"><b>*</b></font>

                                <div class="input-group">

                                    <input type="text" class="form-control float-right" name="email" id="email">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                </div>

                                <!-- /.input group -->
                            </div>
                            <!-- /.form group -->
                        </div>

                    </div>

                    <h5 class="mt-4 mb-2">Endereço</h5>
                    <!-- Date and time range -->

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                <label for="cep">CEP:</label> <font color="red"><b>*</b></font> <span id='mensagem' style='color: red;'></span></label>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="text" id="cep" name="cep" class="form-control"
                                        maxlength='9' data-target="#reservationdate" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-home"></i></div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="col-sm-6">

                            <div class="form-group">
                                <label>Endereço: </label> <font color="red"><b>*</b></font>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="text" id="endereco" name="endereco"
                                        class="form-control" data-target="#reservationdatetime" />
                                    <div class="input-group-append" data-target="#reservationdatetime"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-map-marked-alt"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="col-sm-2">

                                <div class="form-group">
                                    <label>Nº: </label> <font color="red"><b>*</b></font>
                                    <div class="input-group date" data-target-input="nearest">
                                        <input type="text" id="numero" name="numero"
                                            class="form-control datetimepicker-input"
                                            data-target="#reservationdatetime" />
                                        <div class="input-group-append" data-target="#reservationdatetime"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                    <!-- /.form group -->
                    <div class="row">
                        <div class="col-sm-3">
                                <div class="form-group">
                                <label>Bairro:</label> <font color="red"><b>*</b></font>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="text" id="bairro" name="bairro"
                                        class="form-control datetimepicker-input" data-target="#reservationdate" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-map-marker-alt"></i></div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        <div class="col-sm-3">
                            <!-- Date and time -->
                            <div class="form-group">
                                <label>Cidade: </label> <font color="red"><b>*</b></font>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="text" id="cidade" name="cidade"
                                        class="form-control" data-target="#reservationdatetime" />
                                    <div class="input-group-append" data-target="#reservationdatetime"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-city"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                                <div class="form-group">
                                <label>UF:</label> <font color="red"><b>*</b></font>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="text" id="uf" name="uf" class="form-control"
                                        data-target="#reservationdate" maxlength="2" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-building"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Complemento:</label>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="text" id="complemento" name="complemento"
                                        class="form-control datetimepicker-input" data-target="#reservationdate" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        

            </div>
            <div class="col-sm-12">

                <div class="form-check">
                    <input type="checkbox" name="terms" class="form-check-input" id="terms">
                    <label class="form-check-label" for="terms"><b><font color="red">Li e aceito os termos e condições abaixo</font></b></label>
                    <br>
                    <label class="form-check-label" for="terms"><b>Autorizo</b> a consignação em folha de pagamento do
                        valor da mensalidade social em favor da ANFIP <b>na forma do art. 12, itens I e II do seu
                            Estatuto</b>.<br><br>
                        Declaro que <b>é de minha inteira responsabilidade</b> manter meus dados pessoais/funcionais
                        atualizados perante essa Associação.<br><br>
                        Declaro também conhecer e aceitar as normas estatutárias da <b>ANFIP</b> - <b>Associação
                            Nacional dos Auditores Fiscais da Receita Federal do Brasil</b> - como associação de âmbito
                        nacional de representação da classe, em juízo ou fora dele. <b>Confira</b> <a
                            href="https://www.anfip.org.br/wp-content/uploads/2019/08/Estatuto-2019_agosto.pdf"
                            target="_blank">
                            <font color="red"><b>AQUI</b></font>
                        </a> o Estatuto da <b>ANFIP</b>.
                        <br><br><b>Outras informações pelo e-mail: cadastro@anfip.org.br</b><br><br>
                        Valor da mensalidade: Servidor: R$ 130,00 / Pensionista: R$ 130,00<br><br></label>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <a href="/"><button type="button"
                        class="btn btn-lg btn-secondary text-white">Cancelar</button></a>&nbsp;
                <button type="submit" id="submitButton"
                    class="btn btn-primary btn-lg justify-content-md-end" disabled="true">Enviar</button>
            </div>
            </form>

            <br>
            <div class="card-footer">
                <strong>Copyright <sup>&copy;</sup> {{ now()->year }} <a target="_blank"
                        href="https://www.anfip.org.br/">Anfip</a>.</strong> Todos os Direitos Reservados.
                <div>Em caso de dúvidas, entre em contato com o cadastro pelo telefone (61)3251-8114 | WhatsApp (61)98366-6111.</div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>

    <!-- /.col (right) -->
    @include('sweetalert::alert')

    <script>
        jQuery("#siape_status").change(function(){

            document.getElementById("divAposentado").style.display = "none";
            document.getElementById("divPensionista").style.display = "none";
            document.getElementById("divAtivo").style.display = "none";

            var id = jQuery(this).val();

            if(id == 3) {
            document.getElementById("divPensionista").style.display = "block";
            }

            if(id == 2) {
            document.getElementById("divAposentado").style.display = "block";
            }

            if(id == 1) {
            document.getElementById("divAtivo").style.display = "block";
            }
        });
    </script>

    <script src="{{ url('dist/js/affiliatedValidation.js') }}"></script>
    <script src="{{ url('dist/js/formAffiliated.js') }}"></script>

</body>

</html>
