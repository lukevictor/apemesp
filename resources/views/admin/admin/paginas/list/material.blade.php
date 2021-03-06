@extends('admin.dashboard')

@section('titulo', 'Lista de Materiais')

@section('extrastyle')

  <script src="http://apemesp.com.br/public/ckeditor/ckeditor.js "></script>

@endsection

@section('conteudo')

<div class="container">
  <div class="row">
  <div class="col-md-10">

			<fieldset>
				<legend>Materiais</legend>




	<table class="table table-hover table-striped">
			<thead>
					<tr>
							<th> ID </th>
							<th>Imagem</th>
							<th>Titulo</th>
							<th colspan="2"></th>
					</tr>
			</thead>
			<tbody>
@foreach($materiais as $material)
				<tr>
								<td><b>{{ $material->id }}</b></td>

								<td>
                  <img src="{{ url('/images/musicoterapia/material')}}//{{ $material->imagem }}" style="width: 30px; heigth: 25px;">
								</td>
								<td><b>{{ $material->titulo }}</b></td>
								<td><a class="btn btn-default" href="{{ url('/admin/paginas/material') }}/ {{ $material->id }}">Editar</a></td>
<td>

<button class="btn btn-danger" data-toggle="modal" data-target="#deletar{{ $material->id }}">Deletar</button>
<div class="modal fade" id="deletar{{ $material->id }}" role="dialog">

      <div class="modal-dialog">

        <!-- Modal content-->
            <div class="modal-content">
              <form class="form-horizontal" method="GET" action="{{ url('/admin/paginas/material/destroy')}}/{{ $material->id}}">
               {{ csrf_field() }}
                  <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Excluir Literatura!</h4>
                          </div>
                              <div class="modal-body">
                                <p>Deseja realmente excluir esta material?</p>
                              </div>
                              <div class="modal-footer">
                                <a href="#" class="btn btn-info btn-block" data-dismiss="modal">
                                  Não
                                </a>
                              <button class="btn btn-danger btn-block">Sim</button>
                            </div>
                </form>
            </div>

      </div>

</div>
</td>
</tr>
@endforeach
<a class="btn btn-success" href="/admin/paginas/adicionar/material">Adicionar Material</a>

			</tbody>
	</table>


</div>
{{ $materiais->setPath('')->render() }}
</div>

</div>




@endsection


@section('extrascript')

    {!! Html::script('public/js/parsley.min.js') !!}

    <script type="text/javascript">
     CKEDITOR.replace( 'conteudo', {
                filebrowserBrowseUrl: '{!! url('filemanager/index.html') !!}'
            });
     </script>


@endsection
