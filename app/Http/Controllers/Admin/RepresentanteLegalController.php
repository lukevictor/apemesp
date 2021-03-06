<?php

namespace Apemesp\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Apemesp\Http\Controllers\Controller;

use Apemesp\Http\Requests;

use Apemesp\Apemesp\Repositories\Admin\RepresentanteLegalRepository;

use Auth;

use Session;

use View;

class RepresentanteLegalController extends Controller
{


	 public function __construct()
    {
        $this->middleware('auth', ['except' => 'logout']);

        View::composers([
            'Apemesp\Composers\MenuComposer'  => ['partials.admin._nav'],
            'Apemesp\Composers\MensagensComposer'  => ['partials.admin._mensagens']
        ]);

    }


   public function index() {
   		$representanteRepository = New RepresentanteLegalRepository;
        $representante = $representanteRepository->getRepresentante();
        unset($representanteLegalRepository);
        return view('admin.admin.representantelegal.index')->with('representante',$representante);
    }

    public function addRepresentante() {
        return view('admin.admin.configs.comissoes.addrepresentanteLegal');
    }

    public function editRepresentante($id) {

        $representanteLegalRepository = New RepresentanteLegalRepository;
        $representanteLegal = $representanteLegalRepository->getRepresentante();
        return view('admin.admin.representantelegal.edit')->with('representanteLegal', $representanteLegal);
    }


     public function updateRepresentante(Request $request, $id=1) {
        $representanteLegalRepository = New RepresentanteLegalRepository;
        if($request->file('assinatura') != null) {
            $nomeArquivo = $this->storeImage($id, $request);
            $representanteLegalRepository->updateImagem($id, $nomeArquivo);
        }
        $representanteLegalRepository->update($request);
        Session::flash('sucesso', 'A representante legal foi atualizado');
        return redirect()->route('representantelegal.index');
    }

    public function storeImage($id, $request)
	{
		//Armazenamento da imagem
		$extensao = $request->file('assinatura')->getClientOriginalExtension();
		$pastaDestino = base_path() . DIRECTORY_SEPARATOR . 'public/images/Assinatura/';
		$nomeArquivo ='assinatura'. $id . '.' . $extensao;
		$arquivo = $pastaDestino . $nomeArquivo;
		if (file_exists($arquivo)) {
			unlink($arquivo);
		}
		$request->file('assinatura')->move($pastaDestino, $nomeArquivo);
		
		return $nomeArquivo;
	}

  

}
