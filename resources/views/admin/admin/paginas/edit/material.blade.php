@extends('admin.dashboard')

@section('titulo', 'Editar Material')

@section('extrastyle')

  <script src="http://apemesp.com.br/public/ckeditor/ckeditor.js "></script>

@endsection

@section('conteudo')


<form method="POST"  enctype="multipart/form-data" action="{{ url('/admin/paginas/material/') }}/{{ $material[0]->id }}">
 {{ csrf_field() }}
  <fieldset>
  <legend>Edição do material</legend>
  <!-- Botão de Arquivo para Imagem -->

<br />

    <div class="form-group">
      <label class="col-md-4 control-label" for="img">Imagem Atual: </label>
      <img src="{{ url('/images/musicoterapia/material')}}/{{ $material[0]->imagem }}" style="width: 20%; heigth: 25%;">
      <hr>
    </div>
    <div class="form-group" id="mensagem">
      <label class="col-md-4 control-label" for="filebutton">Alterar Imagem: </label>
      <div class="col-md-4">
        <input id="imagem" name="imagem" class="input-file" type="file">
      </div>
      <hr>
    </div>
    <br />
  <!-- Campo Titulo -->
    <div class="form-group">
      <label class="col-md-4 control-label" for="titulo">Titulo:</label>
      <div class="col-md-4">
      <input id="titulo" name="titulo" type="text" placeholder="Titulo da Obra ou do Site" class="form-control input-md" required="" value="{{ $material[0]->titulo }}">
      </div>
    </div>
    <br />
    <br />
  <!-- Campo Descrição -->
    <div class="form-group">
      <label class="col-md-4 control-label" for="descricao">Descrição:</label>
      <div class="col-md-6">
        {{ Form::label('conteudo', 'Conteudo:') }}
        {{ Form::textarea('conteudo', $material[0]->conteudo, array('class' => 'form-control input-md',
        'id' => 'conteudo',
        'name' => 'conteudo',
        'placeholder' => 'Breve descrição sobre a indicação. Você pode incluir aqui links(para leitura ou compra e de páginas de redes sociais)',
        'cols' => '30',
        'required' => '',
        )) }}
      </div>
    </div>
    <br />

    <hr>
    <label class="col-md-4 control-label" for="salvar"></label>
    <center class="col-md-1"><button type="submit" id="salvar" class="btn btn-success"> Salvar Material</button></center>
  </fieldset>
</form>


@endsection


@section('extrascript')

    {!! Html::script('public/js/parsley.min.js') !!}

    <script type="text/javascript">
     CKEDITOR.replace( 'conteudo', {
                filebrowserBrowseUrl: '{!! url('filemanager/index.html') !!}'
            });
     </script>


@endsection
