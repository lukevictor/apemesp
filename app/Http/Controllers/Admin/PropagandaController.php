<?php

namespace Apemesp\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Apemesp\Http\Controllers\Controller;

use Apemesp\Http\Requests;

use Apemesp\Apemesp\Models\Menu;

use Apemesp\Apemesp\Repositories\Admin\PropagandaRepository;

use Auth;

use Session;

use View;

class PropagandaController extends Controller
{


	 public function __construct()
    {
        $this->middleware('auth', ['except' => 'logout']);

        View::composers([
			'Apemesp\Composers\MenuComposer'  => ['partials.admin._nav'],
			'Apemesp\Composers\MensagensComposer'  => ['partials.admin._mensagens']
        ]);

    }


    public function addPropaganda()
    {
    	return view('admin.admin.paginas.add.propaganda');
    }

    public function listPropaganda()
    {
			$repository = new PropagandaRepository;
			$formacoes = $repository->listAll();
			return view('admin.admin.paginas.list.propaganda')->with('formacoes', $formacoes);
    }

    public function storePropaganda(Request $request)
    {


      $repository = new PropagandaRepository;
      $id = $repository->store($request);
	  if($request->file('imagem') != null) {
			$nomeArquivo = $this->storeImage($id, $request);
			$repository->updateImagem($nomeArquivo, $id);
	  }
      return redirect()->route('list.propaganda');
    }

	public function storeImage($id, $request)
	{
		//Armazenamento da imagem
		$extensao = $request->file('imagem')->getClientOriginalExtension();
		$pastaDestino = base_path() . DIRECTORY_SEPARATOR . 'public/images/propagandas/';
		$nomeArquivo ='propaganda'. $id . '.' . $extensao;
		$request->file('imagem')->move($pastaDestino, $nomeArquivo);

		return $nomeArquivo;
	}


    public function destroyPropaganda($id)
    {
			$repository = new PropagandaRepository;
			$repository->destroy($id);
			return redirect()->back();
    }

		public function editPropaganda($id)
		{
			$repository = new PropagandaRepository;
			$propaganda = $repository->getPropaganda($id);
			return view('admin.admin.paginas.edit.propaganda')->with('propaganda', $propaganda);
		}

		public function updatePropaganda(Request $request, $id)
		{
			$repository = new PropagandaRepository;
			$nomeArquivo = $this->storeImage($id, $request);
			$repository->update($request, $id, $nomeArquivo);	
			Session::flash('sucesso', 'A propaganda foi atualizada');
			return redirect()->route('list.propaganda');
		}

		public function updatePropagandaAtiva(Request $request)
		{
			$repository = new PropagandaRepository;
			$repository->updatePropagandaAtiva($request->quantidade);
			Session::flash('sucesso', 'A quantidade de propagandas ativas foi atualizada');
			return redirect()->back();
		}
}
