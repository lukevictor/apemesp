@extends('admin.dashboard')

@section('titulo', 'Configurações')

@section('conteudo')

<form class="form-horizontal" enctype="multipart/form-data"  action="{{ url('admin/configs/forumassuntos')}}" method="POST">
        <fieldset>


                <legend>Adicionar Assunto para a página do fórum</legend>

           {{ csrf_field() }}
                <!-- Campo Assunto -->
                    <div class="form-group">
                      <label class="col-md-4 control-label" for="name">Assunto:</label>
                      <div class="col-md-4">
                      <input id="name" name="name" type="text" placeholder="Assunto" class="form-control input-md" required="">
                      </div>
                    </div>

                <!-- Botão -->
                    <div class="form-group">

                      <div class="col-md-4">
                        <button id="singlebutton" name="singlebutton" class="btn btn-primary">Salvar</button>
                      </div>
                    </div>


        </fieldset>
</form>


@endsection
