<?php

namespace Apemesp\Http\Controllers\Associado;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

use Apemesp\Http\Controllers\Controller;

use Apemesp\Http\Requests;

use Apemesp\Apemesp\Repositories\Associado\AssociadoRepository;

use Apemesp\Apemesp\Repositories\Associado\DadosAcademicosRepository;

use Apemesp\Apemesp\Classes\Associado;

use Auth;

use Session;

use View;

class AssociadoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'logout']);

         View::composers([
            'Apemesp\Composers\MenuComposer'  => ['partials.admin._nav'],
            'Apemesp\Composers\MensagensComposer'  => ['partials.admin._mensagens']
        ]);  
         
        
    }

    public function getCidades($idEstado)
    {
       $associado = new AssociadoRepository; 
       $cidades = $associado->getCidades($idEstado);
       unset($associado);
       return response()->json(['cidades' => $cidades]);
    }

    public function getDownload($arquivo)
    {
        $dadosAcademicos = new DadosAcademicosRepository;
        $cpf = $dadosAcademicos->getCpf(Auth::user()->id);
        $file= public_path(). "/files/" . $cpf . "/" . $arquivo;
        $headers = array('Content-Type: application/pdf',);
        if (file_exists($file)) {
          return Response::download($file, 'arquivo.pdf', $headers);
        } else {
          Session::flash('cuidado','Arquivo não encontrado para download');
          return redirect()->back();
        }
    }

    public function verificarCpf(Request $request)
    {
        $cpf  = $request->cpf;
        dd($cpf);
    }

    public function jaRegistrado()
    {
        return view('auth.verifyRegister');
    }




}