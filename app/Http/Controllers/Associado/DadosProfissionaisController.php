<?php

namespace Apemesp\Http\Controllers\Associado;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

use Apemesp\Http\Controllers\Controller;

use Apemesp\Http\Requests;

use Apemesp\Classes\Associado;

use Apemesp\Apemesp\Repositories\Apemesp\UserRepository;

use Apemesp\Apemesp\Repositories\Associado\DadosProfissionaisRepository;

use Apemesp\Apemesp\Repositories\Associado\DadosAcademicosRepository;

use Auth;

use Session;

use View;

use DB;

use Input;

class DadosProfissionaisController extends Controller{


    public function __construct()
    {
        $this->middleware('auth', ['except' => 'logout']);

        View::composers([
            'Apemesp\Composers\MenuComposer'  => ['partials.admin._nav'],
            'Apemesp\Composers\MensagensComposer'  => ['partials.admin._mensagens']
        ]);

    }

    public function getUserCadastro()
    {
      return Auth::user()->id_cadastro;
    }

    public function getUserId()
    {
      return Auth::user()->id;
    }

    public function getDadosProfissionais()
    {

      $id_cadastro = $this->getUserCadastro();
      $dados = array();
      $dadosProfissionais = new DadosProfissionaisRepository;
      $dadosAcademicos = new DadosAcademicosRepository;
      $formacoes = $dadosAcademicos->getFormacoes($this->getUserId());

       if ($id_cadastro < 3 && count($formacoes) == 0){
            return view('admin.associado.restricao');
       }
       if ($id_cadastro < 3 && count($formacoes) >= 1) {
         $dadosAcademicos->changeCadastro($this->getUserId(), $id_cadastro);
         return $this->index();
       }
      if ($id_cadastro >= 3){
        if (Auth::user()->opcao_dados_profissionais == 1) {
          return view('admin.associado.dadosprofissionaisinativos');
        }
          return $this->index();
      }
    }

    public function index()
    {
      $dadosProfissionais = new DadosProfissionaisRepository;
      return view('admin.associado.dadosprofissionais')
      ->with('dados', $dadosProfissionais->getDadosProfissionais($this->getUserId()))
      ->with('especialidades', $dadosProfissionais->getEspecialidades())
      ->with('proximidades', $dadosProfissionais->getProximidades())
      ->with('escalas', $dadosProfissionais->getEscalas());
    }

    public function storeDadosProfissionais(Request $request)
    {

      $dadosProfissionais = new DadosProfissionaisRepository;
      $id_cadastro = $this->getUserCadastro();
      $id_user = $this->getUserId();
      
      $teste = $this->validate($request, array(
              'cep' => 'required|max:8',
              'endereco' => 'required',
              'bairro' => 'required',
              'proximidade' => 'required',
              'especialidade' => 'required',
              'telefone' => 'required',
              'dias_atendimento' => 'required',
          ));
      
      $resultado = $dadosProfissionais->storeDadosProfissionais($request);
      $dadosProfissionais->changeCadastro($id_user, $id_cadastro);
      if ($resultado == 0) {
        return view('admin.associado.dadosprofissionais')
        ->with('dados', $dadosProfissionais->getDadosProfissionais($this->getUserId()))
        ->with('especialidades', $dadosProfissionais->getEspecialidades())
        ->with('proximidades', $dadosProfissionais->getProximidades())
        ->with('escalas', $dadosProfissionais->getEscalas());
        $this->sendEmailAdministradores(Auth::user()->id);
        Session::flash('sucesso', 'Seus dados profissionais foram inseridos com sucesso');
      } else {
        Session::flash('cuidado', 'Algum dos dados estava incorreto ou você ultrapassou 3 locais de atendimento');
      }

    }

    public function storeOpcaoDeAtendimento(){

        $dadosProfissionais = new DadosProfissionaisRepository;
        $dadosAcademicos = new DadosAcademicosRepository;
        $dadosProfissionais->storeOpcaoDeAtendimento();
        $dadosProfissionais->changeCadastro($this->getUserId(), $this->getUserCadastro());
        return view('admin.associado.documentacao')->with('cpf', $dadosAcademicos->getCpf($this->getUserId()));
        Session::flash('sucesso', 'Seus dados profissionais foram atualizados com sucesso');
    }

    public function showDadosProfissionais($id)
    {
      $dadosProfissionais = new DadosProfissionaisRepository;
      $dados = $dadosProfissionais->getDadoProfissional($id, $this->getUserId());
      if(count($dados)) {
        $dados[0]->cidade = $dadosProfissionais->getCidadeEspecifica($dados[0]->id_cidade);
        $dados[0]->estado = $dadosProfissionais->getEstadoEspecifico($dados[0]->id_estado);
        return view('admin.associado.showdadosprofissionais')
          ->with('dados', $dados)
          ->with('especialidades', $dadosProfissionais->getEspecialidades())
          ->with('proximidades', $dadosProfissionais->getProximidades())
          ->with('escalas', $dadosProfissionais->getEscalas());

      }
      return view('errors.404');
    }


    public function editDadosProfissionais($id)
    {
      $dadosProfissionais = new DadosProfissionaisRepository;
      $dados = $dadosProfissionais->getDadoProfissional($id, $this->getUserId());
      if(count($dados)) {
        $dados[0]->cidade = $dadosProfissionais->getCidadeEspecifica($dados[0]->id_cidade);
        $dados[0]->estado = $dadosProfissionais->getEstadoEspecifico($dados[0]->id_estado);
        return view('admin.associado.editdadosprofissionais')
          ->with('dados', $dados)
          ->with('especialidades', $dadosProfissionais->getEspecialidades())
          ->with('proximidades', $dadosProfissionais->getProximidades())
          ->with('escalas', $dadosProfissionais->getEscalas());

      }
      return view('errors.404');
    }


    public function updateDadosProfissionais(Request $request, $id)
    {

      $dadosProfissionais = new DadosProfissionaisRepository;


      $teste = $this->validate($request, array(
              'cep' => 'required|max:8',
              'endereco' => 'required',
              'bairro' => 'required',
              'proximidade' => 'required',
              'especialidade' => 'required',
              'telefone' => 'required',
              'dias_atendimento' => 'required',
          ));

      $resultado = $dadosProfissionais->updateDadosProfissionais($request, $id);

      if ($resultado == 0) {
        return view('admin.associado.dadosprofissionais')
        ->with('dados', $dadosProfissionais->getDadosProfissionais($this->getUserId()))
        ->with('especialidades', $dadosProfissionais->getEspecialidades())
        ->with('proximidades', $dadosProfissionais->getProximidades())
        ->with('escalas', $dadosProfissionais->getEscalas());
        $this->sendEmailAdministradores(Auth::user()->id);
        Session::flash('sucesso', 'Seus dados profissionais foram atualizados com sucesso');
      }

      Session::flash('cuidado', 'Seus dados acadêmicos não foram atualizados');

    }


    public function destroyDadosProfissionais($id)
    {
      $repository = new DadosProfissionaisRepository;
      $repository->destroy($id);
      return $this->index();
    }

    public function sendEmailAdministradores($id)
    {
        $user = User::findOrFail($id);
        $userRepo = new UserRepository;

        $administradores = $userRepo->findAllAdmins();
        foreach($administradores as $administrador) {
            Mail::send('emails.administradores_perfil', ['id' => $user->id, 'nome' => $user->name, 'email' => $user->email], function ($m) use ($user, $administrador) {
                $m->from('site.apemesp@gmail.com', 'APEMESP');

                $m->to($administrador->email, $administrador->name)->subject('Novos dados profissionais!');
            });
        }
    }


}
