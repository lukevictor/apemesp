@extends('admin.dashboard')

@section('titulo', 'Editar Página')

@section('extrastyle')

  <script src="http://apemesp.com.br/public/ckeditor/ckeditor.js "></script>

@endsection

@section('conteudo')

            {!! Form::open(array('route' => ['pagina.update', $pagina->id], 'data-parsley-validate' => '')) !!}

    			{{ Form::label('titulo', 'Titulo:') }}
                {{ Form::text('titulo', $pagina->titulo ,array('class' => 'form-control')) }}

                {{ Form::label('subtitulo', 'Subtitulo:') }}
                {{ Form::text('subtitulo', $pagina->subtitulo ,array('class' => 'form-control')) }}


    			{{ Form::label('conteudo', 'Conteudo:') }}
    			{{ Form::textarea('conteudo', $pagina->body, array('class' => 'form-control')) }}

    			{{ Form::submit('Atualizar', array('class' => 'btn btn-success btn-lg btn-block')) }}

			{!! Form::close() !!}

@endsection


@section('extrascript')

    {!! Html::script('public/js/parsley.min.js') !!}

    <script type="text/javascript">
     CKEDITOR.replace( 'conteudo', {
                filebrowserBrowseUrl: '{!! url('filemanager/index.html') !!}'
            });
     </script>


@endsection


