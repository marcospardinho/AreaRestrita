<x-app-layout>
    <x-slot name="header">

    </x-slot>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                </div>
            </div><!-- /.container-fluid -->
        </section>
        <style>
            .hide_row {
                padding: 0 !important;
            }

        </style>
        <!-- Main content -->
        <section class="content-header sessTit">
            <div class="container-fluid">
                <div class="row mb-2">
                    <!-- Título -->
                    <div class="col-sm-6">
                        <h1 style="font-size: 25px;">Painel Gerenciador de Assembleias </h1>
                    </div>
                    <!-- Lado Direito -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <a href="" style="min-width: 95px; margin-right:  5px" type="button"
                                class="btn btn-default btn-sm" title="Voltar"><i class="fas fa-times"></i>
                                Voltar</a>
                            <a href="{{ route('assembly.create') }}" style="min-width: 95px; margin-right: -7px"
                                form="formUnidade" class='btn bg-primary btn-sm btn-tam'><i class="fas fa-plus"></i>
                                Nova Assembleia</a>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Conteudo da Pagina -->
        <section class="content sessMargin">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assembleias</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">

                <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Enquetes</th>
                                <th style="width: 40px"></th>
                            </tr>
                        </thead>
                        <tbody>


                        @forelse($assembleias as $assembleia)
                                <tr data-toggle="collapse" class="accordion-button collapsed"
                                    data-target="#{{ Str::slug($assembleia->titulo, '-') }}" class="accordion-toggle">

                                <td>{{$assembleia->titulo}}</td>
                                <td>{{$assembleia->descricao}}</td>
                                <td>
                                    @if($assembleia->status == 0)
                                    <span class="badge badge-danger">Desativada</span>
                                    @else
                                    <span class="badge badge-success">Ativa</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-primary">{{$assembleia->enquetes->count()}}</span></td>


                                </tr>
                                    <tr>
                                        <td class="hide_row" colspan="6">
                                                <div id="{{ Str::slug($assembleia->titulo, '-') }}"
                                                    class="accordian-body collapse">
                                                    <table class="table table-sm">

                                                        <tbody>
                                                                <tr>
                                                                    <td>
                                                                    <a href="{{ route('assembly.edit',$assembleia->id_assembleia) }}" style=" margin-top: 0.4cm; margin-right: 0.1cm; " class="btn btn-sm btn-info float-left" ><i class="fas fa-pen"></i> Editar</a>
                                                                    <a href="#" class="btn btn-sm btn-danger float-left deleteAssembly" data-id="{{$assembleia->id_assembleia}}"  data-toggle="modal" data-target="#mdlExcluirAssembly" style="  margin-top: 0.4cm; margin-right: 0.1cm; " ><i class="fas fa-trash-alt"></i> Remover</a>
                                                                    @if($assembleia->status == 0)
                                                                    <a href="{{ route('active', array('assembleia' => $assembleia->id_assembleia, 'type' => 1)) }}" style="  margin-top: 0.4cm; margin-right: 0.1cm; " class="btn btn-sm btn-success float-left" ><i class="fas fa-check"></i> Ativar Assembleia</a>
                                                                    @else
                                                                    <a href="{{ route('active', array('assembleia' => $assembleia->id_assembleia, 'type' => 0)) }}" style="  margin-top: 0.4cm; margin-right: 0.1cm; " class="btn btn-sm btn-default float-left" ><i class="fas fa-ban"></i> Encerrar Assembleia</a>
                                                                    @endif
                                                                    <a href="{{ route('question.index',$assembleia->id_assembleia) }}" style="  margin-top: 0.4cm; margin-right: 0.1cm; " class="btn btn-sm btn-warning float-left" ><i class="fas fa-list-ul"></i> Ver Enquetes</a>
                                                                    <a href="{{ route('question.create',$assembleia->id_assembleia) }}" style="  margin-top: 0.4cm; margin-right: 0.1cm; " class="btn bg-primary btn-sm float-left" ><i class="far fa-plus-square"></i> Adicionar Enquete</a>
                                                                    <a href="{{ route('report.participants',$assembleia->id_assembleia) }}" style=" margin-top: 0.4cm; margin-right: 0.1cm; " class="btn btn-sm btn-primary float-left"><i class="fas fa-users"></i> Relatório de participantes</a>
                                                                    <a href="{{ route('report.question', $assembleia->id_assembleia) }}" style=" margin-top: 0.4cm; margin-right: 0.1cm; " class="btn btn-sm btn-primary float-left"><i class="far fa-clipboard"></i> Relatório de questões</a>
                                                                    <a href="{{ route('report.votesuf', $assembleia->id_assembleia) }}" style=" margin-top: 0.4cm; margin-right: 0.1cm; " class="btn btn-sm btn-primary float-left"><i class="fas fa-globe"></i> Relatório de votos por UF</a>
                                                                    </td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                    </tr>

                            @empty
                                <br />
                                <div style='text-align:center'>
                                    <h2>Lista vazia!<h2>
                                </div>

                            @endforelse



                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                {{-- <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                      <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                      <li class="page-item"><a class="page-link" href="#">1</a></li>
                      <li class="page-item"><a class="page-link" href="#">2</a></li>
                      <li class="page-item"><a class="page-link" href="#">3</a></li>
                      <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                    </ul>
                  </div> --}}
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->

    <!-- Modal Exclusao de Registro-->
    <div class="modal" id="mdlExcluirAssembly" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                <form class="needs-validation" id="formID" action="{{ route('assembly.delete') }}" method="POST">
                                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    @method('DELETE')
                                    <input type="hidden" name="id" id="app_id" class="recipient-name">
                                    <img class="text-center" src=""  width="85px">
                                    <br/>
                                    <span style="font-size: 30px; color: #595959"><b>Exclusão de Assembléia</b></span><br/>
                                    <b style="font-size: 20px" class="text-danger">Ao excluir essa Assembléia, todos os dados relacionados a ela serão deletados do banco. Você tem certeza que deseja executar essa ação ?</b><br><br>
                                    <a style="min-width: 100px; margin-right: 20px" type="button" class="btn btn-default btn-tam" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</a>
                                    <button name="send" id="send" style="min-width: 100px" type='submit'  class='btn btn-danger btn-tam' ><i class="fas fa-trash-alt"></i> Excluir</button>
                                            </form>
                                </div>
                            </div>
                        </div>
                    </div><!-- /Modal Exclusao de Registro-->

<script>
    $(document).on('click','.deleteAssembly',function(){
    var userID=$(this).attr('data-id');
    $('#app_id').val(userID);
    $('#mdlExcluirAssemblyl').modal('show');
});
</script>
    @include('sweetalert::alert')

</x-app-layout>
