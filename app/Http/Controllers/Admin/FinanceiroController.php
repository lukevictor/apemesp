<?php
namespace Apemesp\Http\Controllers\Admin;

use Apemesp\Http\Requests;

use Apemesp\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Apemesp\Apemesp\Repositories\Admin\FinanceiroRepository;

use Apemesp\Apemesp\Repositories\Admin\AssociadoRepository;

use Apemesp\Apemesp\Repositories\Associado\CarteirinhaRepository;

use View;

use Session;

class FinanceiroController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->middleware('auth', ['except' => 'logout']);
    	
        View::composers([
            'Apemesp\Composers\MenuComposer'  => ['partials.admin._nav'] ,
            'Apemesp\Composers\MensagensComposer'  => ['partials.admin._mensagens']
        ]);
  
    }

    public function index()
    {
        $financeiroRespository = new FinanceiroRepository;
        $associados = $financeiroRespository->getAssociados();
        
        unset($financeiroRespository);
     
    	return view('admin.admin.financeiro.financeiro')->with('associados', $associados);
    }

    public function indexDadosBancarios()
    {
        $financeiroRespository = new FinanceiroRepository;
        $dadosBancarios = $financeiroRespository->getDadosBancarios();
        unset($financeiroRespository);
     
    	return view('admin.admin.financeiro.dadosbancarios.index')->with('dadosbancarios', $dadosBancarios);
    }

    public function search(Request $request)
    {

        $associadoRepository = new AssociadoRepository;
        $associados = $associadoRepository->search($request);
        unset($associadoRepository);
        return view('admin.admin.financeiro.financeiro')->with('associados', $associados);
    }

    public function getAssociado($id)
    {
        $financeiroRespository = new FinanceiroRepository;
        $associado = $financeiroRespository->getAssociado($id);
        $status = $financeiroRespository->getStatusAnuidade();
        return view('admin.admin.financeiro.associado')
        ->with('associado',$associado)
        ->with('id_user', $id)
        ->with('status', $status);
    }

    public function avaliarLancamento($id, $ano)
	{
        $financeiroRespository = new FinanceiroRepository;
        $associado = $financeiroRespository->getLancamento($id, $ano);
        $status = $financeiroRespository->getStatusAnuidade();
        return view('admin.admin.financeiro.avaliacao')
        ->with('associado',$associado)
        ->with('status',$status);
    }
    
    public function storeAnuidade(Request $request)
    {
      
      $financeiroRespository = new FinanceiroRepository;
      $carteirinhaRespository = new CarteirinhaRepository;
      $anuidade = $financeiroRespository->storeAnuidade($request->id, $request);
      
      if ($anuidade) {
        $arquivo = $request->file('comprovante');
        $pastaDestino = base_path() . DIRECTORY_SEPARATOR . 'public/files/' . $financeiroRespository->getCpf($request->id);
        $nomeArquivo = 'comprovante_'. $request->ano . '.' . $request->file('comprovante')->getClientOriginalExtension();
        if (file_exists($pastaDestino . $nomeArquivo)) {
			unlink($pastaDestino . $nomeArquivo);
		}
        $request->file('comprovante')->move($pastaDestino, $nomeArquivo);
        $financeiroRespository->gravaArquivo($nomeArquivo, $request->ano, $request->id);
        Session::flash('sucesso', 'Sua anuidade foi salva com sucesso');
      } else {
        Session::flash('cuidado', 'Verifique o arquivo ou o ano deste comprovante, sua anuidade não foi salva.');
      }
      if($request->ano == date("Y") && $request->status == 2 || $request->ano == date("Y") && $request->status == 3)
      {
        $carteirinhaRespository->gerarNumeroAssociado($request);
      }
      return redirect()->back(); 
    }

    public function salvarAvaliacao(Request $request)
    {
      $financeiroRespository = new FinanceiroRepository;
      $carteirinhaRespository = new CarteirinhaRepository;

      $financeiroRespository->salvarAvaliacao($request);

      $arquivo = $request->file('comprovante');
      if($arquivo != null) {
        $pastaDestino = base_path() . DIRECTORY_SEPARATOR . 'public/files/' . $financeiroRespository->getCpf($request->id);
        $nomeArquivo = 'comprovante_'. $request->ano . '.' . $request->file('comprovante')->getClientOriginalExtension();
        
        if (file_exists($pastaDestino . $nomeArquivo)) {
            unlink($pastaDestino . $nomeArquivo);
        }
      
         $request->file('comprovante')->move($pastaDestino, $nomeArquivo);
         $financeiroRespository->gravaArquivo($nomeArquivo, $request->ano, $request->id);
       }
      Session::flash('sucesso', 'Sua anuidade foi salva com sucesso');

      if($request->ano == date("Y") && $request->status == 2 || $request->ano == date("Y") && $request->status == 3)
      {
        $carteirinhaRespository->gerarNumeroAssociado($request);
      }
      return $this->getAssociado($request->id);

    }

}

