<?php

namespace Apemesp\Http\Controllers\Associado;

use Illuminate\Http\Request;

use Apemesp\Http\Requests;

use Apemesp\Http\Controllers\Controller;

use Apemesp\Apemesp\Classes\Associado;

use Apemesp\Apemesp\Repositories\Associado\DadosAcademicosRepository;

use Apemesp\Apemesp\Repositories\Associado\CarteirinhaRepository;

use Apemesp\Apemesp\Repositories\Admin\FinanceiroRepository;

use Apemesp\Apemesp\Repositories\Admin\AssociadoRepository;

use Apemesp\Apemesp\Repositories\Admin\RepresentanteLegalRepository;

use Apemesp\Apemesp\Models\Assunto;

use Mail;

use Auth;

use Session;

use View;

use DB;

class CarteirinhaController extends Controller
{


  public function __construct()
  {
      $this->middleware('auth', ['except' => 'logout']);
      View::composers([
          'Apemesp\Composers\MenuComposer'  => ['partials.admin._nav'],
          'Apemesp\Composers\MensagensComposer'  => ['partials.admin._mensagens']
      ]);

  }

  public function getIndex()
  {
    $carteirinhaRepository = new CarteirinhaRepository;
    $financeiroRepository = new FinanceiroRepository;

    $carteirinha = $carteirinhaRepository->getStatus($this->getUserId());
    $anuidades = $financeiroRepository->getAssociado($this->getUserId());
      if($carteirinha){
        foreach($anuidades as $anuidade) {
          if($anuidade->ano == date("Y") && $anuidade->status != 2 && $anuidade->status != 3) {
            return view('admin.associado.restricao');
          } else {
            return view('admin.associado.carteirinha.index')->with('carteirinha', $carteirinha);
          }
        }
        if(empty($anuidade) || !isset($anuidade) || $anuidade == null) {
          return view('admin.associado.restricao');
        }
      } else {
        return view('admin.associado.restricao');
      }
  }

  public function getIdCadastro()
  {
    return Auth::user()->id_cadastro;
  }

  public function getUserId()
  {
    return Auth::user()->id;
  }

  public function storeOld(Request $request)
  {
    if($request->numero != "999999" && $request->numero != "000000") {
      $carteirinhaRepository = new CarteirinhaRepository; 
      $carteirinha = $carteirinhaRepository->storeOld($request);

    } 
      return $this->getIndex();
  }

  public function getCertificado()
  {
    $associadoRepository = new AssociadoRepository;
    $repRepository = new RepresentanteLegalRepository;
    $carteirinhaRepository = new CarteirinhaRepository; 
    $admfim = new FinanceiroRepository;
    $dadosAcademicos = new DadosAcademicosRepository;

    $carteirinha = $carteirinhaRepository->getStatus($this->getUserId());
    $numeroCarteirinha = $carteirinhaRepository->getNumero($this->getUserId());
    $associado = $associadoRepository->getAssociado($this->getUserId()); 
    $representante = $repRepository->getRepresentante();

    $anuidades = $admfim->getAssociado(Auth::user()->id);
    foreach($anuidades as $anuidade) {
      if($anuidade->ano == date("Y") && $anuidade->status != 2 && $anuidade->status != 3) {
      $status6 = false;
      } else {
      $status6 = true;
      }
  }
    if($status6) {
      return view("admin.associado.certificado.index")
      ->with('associado', $associado)
      ->with('numeroCarteirinha', $numeroCarteirinha)
      ->with('representante', $representante)
      ->with('carteirinha', $carteirinha)
      ->with('cidade', $dadosAcademicos->getCidadeEspecifica($associado->id_cidade))
      ->with('estados', $dadosAcademicos->getEstados());
    } else {
      Session::get("cuidado", "Sua situação financeira atual não permite a emissão do comprovante");
      return view('admin.associado.carteirinha.index')->with('carteirinha', $carteirinha);
    }
  }

  public function segundaVia(Request $request)
  {
    $carteirinhaRepository = new CarteirinhaRepository; 

    $mensagem = "Solicito a segunda via da minha carteirinha" ."<br>" . "Observações: ". $request->observacao . "<br>" . "Estas informações estão disponiveis dentro do painel da Carteirinha na área do administrador";
    $mensagemAssociado = "Sua solicitação foi enviada com sucesso, aguarde o nosso retorno";

    $dados = array(
			'titulo' => "Segunda Via da Carteirinha",
			'mensagem' => $mensagem, 
			'nome' => $this->getName(),
			'email' => $this->getEmail(),
			'data' => date('Y-m-d'),
      );

    $dados2 = array(
      'titulo' => "Segunda Via da Carteirinha",
      'mensagem' => $mensagemAssociado, 
      'nome' => $this->getName(),
      'email' => $this->getEmail(),
      'data' => date('Y-m-d'),
      );
      
      $oEmail = Assunto::Where('assunto', 'like', '%anuidade%')->select('*')->get()->first();
    	Mail::send('emails.send', $dados, function ($message) use ($oEmail)
    	{
    		$message->from($this->getEmail(), $this->getName());
    		$message->to("secretaria@apemesp.com")->subject("Segunda Via da Carteirinha");
      });
      
      Mail::send('emails.send', $dados2, function ($message) use ($oEmail)
    	{
    		$message->from($this->getEmail(), $this->getName());
    		$message->to($this->getEmail())->subject("Segunda Via da Carteirinha");
    	});
      
      
      $carteirinhaRepository->segundaVia($request);
      Session::flash('sucesso', 'A mensagem foi enviada com sucesso');
      
      return redirect()->back();
  }

  public function getEmail()
  {
    return Auth::user()->email;
  }

  public function getName()
  {
    return Auth::user()->name;
  }
}
