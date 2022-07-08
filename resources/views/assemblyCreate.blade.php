<x-app-layout>
    <x-slot name="header">

    </x-slot>

    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="../../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">



    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <!--<div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                  <li class="breadcrumb-item"><a href="#">Layout</a></li>
                  <li class="breadcrumb-item active">Fixed Layout</li>
                </ol>
              </div> -->
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content-header sessTit">
            <div class="container-fluid">
                <div class="row mb-2">
                    <!-- Título -->
                    <div class="col-sm-6">
                        <h1 style="font-size: 25px;">Adicionar Assembleia </h1>
                    </div>
                    <!-- Lado Direito -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <a href="{{ route('assembly.index') }}" style="min-width: 95px; margin-right:  5px"
                                type="button" class="btn btn-default btn-sm" title="Voltar"><i
                                    class="fas fa-times"></i>
                                Cancelar</a>
                            <button style="min-width: 95px; margin-right: -7px" type='submit' form="formAssembleia"
                                class='btn bg-primary btn-sm btn-tam'><i class="fas fa-forward"></i> Salvar</button>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Conteudo da Pagina -->
        <section class="content sessMargin">
            <div class="card">
                <div class="card-body">

                    <form class="needs-validation" novalidate id="formAssembleia" name="formAssembleia"
                        action="{{ route('assembly.add') }}" enctype="multipart/form-data" method="post">
                        <!-- Abas -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <!-- Conteudo da Abas -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="tab-content boxligth" id="nav-tabContent">

                            <!-- Linha 01 -->
                            <div class="sub-doc">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="ndoc">Assembleia</label>
                                        <input type="text" class="form-control" id="nomeassembleia"
                                            name="nomeassembleia" value="{{ old('nomeassembleia') }}" maxlength="100"
                                            placeholder="Título da assembleia" />
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Status</label>
                                        <select name="status" class="form-control custom-select" required="true">
                                            <option value="0" selected>Desativada</option>
                                            <option value="1">Ativada</option>

                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="area">Usuários Habilitados</label>
                                        <div>
                                            <input type="checkbox" id="ativos" name="usuarios[]" value="1">
                                            <label for="ativos"> &nbspAtivos&nbsp </label>
                                            <input type="checkbox" id="aposentados" name="usuarios[]" value="2">
                                            <label for="aposentados"> &nbspAposentados&nbsp </label>
                                            <input type="checkbox" id="pensionistas" name="usuarios[]" value="3">
                                            <label for="pensionistas"> &nbspPensionistas&nbsp </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label for="ndoc">Texto da Assembleia</label>
                                    <textarea id="textoassembleia" name="textoassembleia"
                                        placeholder="Digite o texto da assembleia aqui" value="{{ old('textoassembleia') }}"  class="form-control"
                                        rows="4"></textarea>

                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Data de início</label>
                                    <div class="input-group date" id="reservationdatetime" data-target-input="nearest">
                                        <input type="text" id="dtinicio" name="dtinicio" value="{{ old('dtinicio') }}"
                                            class="form-control datetimepicker-input"
                                            data-target="#reservationdatetime" />
                                        <div class="input-group-append" data-target="#reservationdatetime"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Data do término</label>
                                    <div class="input-group date" id="reservationdatetime2" data-target-input="nearest">
                                        <input type="text" id="dttermino" name="dttermino" value="{{ old('dttermino') }}"
                                            class="form-control datetimepicker-input"
                                            data-target="#reservationdatetime2" />
                                        <div class="input-group-append" data-target="#reservationdatetime2"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                </form>

            </div>
    </div>
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- InputMask -->
    <script src="../../plugins/moment/moment.min.js"></script>
    <!-- date-range-picker -->
    <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="../../plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>



    <script type="text/javascript">
        $(function() {

            //Date and time picker
            $('#reservationdatetime').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',
                icons: {
                    time: 'far fa-clock'
                },


            });

            $('#reservationdatetime2').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',
                icons: {
                    time: 'far fa-clock'
                },

            });

        });
    </script>



</x-app-layout>
