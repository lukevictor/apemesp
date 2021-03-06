<?php

namespace Apemesp\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Apemesp\Http\Controllers\Controller;

use Apemesp\Http\Requests;

use Apemesp\Apemesp\Models\Menu;

use Apemesp\Apemesp\Repositories\Admin\FormacaoRepository;

use Apemesp\Apemesp\Repositories\Admin\ConfigsRepository;

use Auth;

use Session;

use View;

class FormacaoController extends Controller
{


	 public function __construct()
    {
        $this->middleware('auth', ['except' => 'logout']);

        View::composers([
			'Apemesp\Composers\MenuComposer'  => ['partials.admin._nav'],
			'Apemesp\Composers\MensagensComposer'  => ['partials.admin._mensagens']
        ]);

    }


    public function addFormacao()
    {
    	return view('admin.admin.paginas.add.formacao');
    }

    public function listFormacao()
    {
			$repository = new FormacaoRepository;
			$formacoes = $repository->listAll();
			return view('admin.admin.paginas.list.formacao')->with('formacoes', $formacoes);
    }

    public function storeFormacao(Request $request)
    {
      $repository = new FormacaoRepository;
      $id = $repository->store($request);
	  if($request->file('imagem') != null) {
		$nomeArquivo = $this->storeImage($id, $request);
		$repository->updateImagem($nomeArquivo, $id);
	  }
      return redirect()->route('list.formacao');
    }

		public function storeImage($id, $request)
		{
			//Armazenamento da imagem
			
			$extensao = $request->file('imagem')->getClientOriginalExtension();
			$pastaDestino = base_path() . DIRECTORY_SEPARATOR . 'public/images/musicoterapia/formacao/';
			$nomeArquivo ='formacao'. $id . '.' . $extensao;
			if (file_exists($pastaDestino . $nomeArquivo)) {
				unlink($pastaDestino . $nomeArquivo);
			}
			$request->file('imagem')->move($pastaDestino, $nomeArquivo);
			return $nomeArquivo;
		}


    public function destroyFormacao($id)
    {
			$repository = new FormacaoRepository;
			$repository->destroy($id);
			return redirect()->back();
    }

		public function editFormacao($id)
		{
			$repository = new FormacaoRepository;
			$formacao = $repository->getFormacao($id);
			return view('admin.admin.paginas.edit.formacao')->with('formacao', $formacao);
		}

		public function updateFormacao(Request $request, $id)
		{
			$repository = new FormacaoRepository;
			$repository->update($request, $id);
			if($request->file('imagem') != null) {
				$nomeArquivo = $this->storeImage($id, $request);
				$repository->updateImagem($nomeArquivo, $id);
			}
			return redirect()->route('list.formacao');
		}

}
