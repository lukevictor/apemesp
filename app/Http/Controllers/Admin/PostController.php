<?php

namespace Apemesp\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Apemesp\Http\Controllers\Controller;

use Apemesp\Http\Requests;

use Apemesp\Apemesp\Repositories\Admin\PostRepository;

use Apemesp\Apemesp\Repositories\Admin\ChartRepository;

use Auth;

use Session;

use AuthenticatesAndREgisterUsers, ThorttlesLogins;

use View;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'logout']);

        View::composers([
            'Apemesp\Composers\MenuComposer'  => ['partials.admin._nav'],
            'Apemesp\Composers\MensagensComposer'  => ['partials.admin._mensagens']
        ]);

    }

    public function index()
    {
        $chart = new ChartRepository;

        $views = $chart->getVisualizacoes();
        $maisVistos = $chart->getPostsMaisVistos();
        $id_perfil = Auth::user()->id_perfil;
        
        if ($id_perfil == 1 || $id_perfil == 2) {

            return view('posts.index')
                   ->with('views',$views)
                   ->with('maisVistos',$maisVistos);

        } else {

            return view('errors.400');
        }

    }


    public function indexApemesp()
    {
      $postRepository = new PostRepository;
      $posts = $postRepository->getPosts(1);

      return view('posts.listPosts')->with('posts', $posts)->with('pagina', 'APEMESP');
      unset($postRepository);
    }

    public function indexJomesp()
    {
      $postRepository = new PostRepository;
      $postsjomesp = $postRepository->getPosts(2);

      return view('posts.listPosts')->with('posts', $postsjomesp)->with('pagina', 'JOMESP');
      unset($postRepository);

    }


    public function create()
    {
        $postRepository = new PostRepository;
        $id_perfil = Auth::user()->id_perfil;
        $tags = $postRepository->getTags();

        if ($id_perfil == 1 || $id_perfil == 2) {
            return view('posts.create')->with('tags', $tags);
        } else {
            return view('errors.400');
        }

    }



    public function store(Request $request)
    {
        //Validar os dados
            $this->validate($request, array(
                    'titulo' => 'required|max:255',
                    'previa' => 'required',
                    'conteudo' => 'required'
                ));

         //Salvar no BD
         $postRepository = new PostRepository;
         $id_post = $postRepository->storePost($request);

        //Salvar imagem
        $imagemAtual = $postRepository->getImage($id_post);
        $imagem = $this->storeImage($request, $id_post, $imagemAtual);
        $postRepository->storeImage($id_post, $imagem);


        if ($request->destino == 1) {
            Session::flash('sucesso', 'O post da APEMESP foi salvo com sucesso');
        }
        if ($request->destino == 2) {
            Session::flash('sucesso', 'O post da JOMESP foi salvo com sucesso');
        }
        unset($postRepository);
            //flash para esta request e put para salvar na sessao

            //redirecionar a pagina
        return redirect()->route('posts.show', $id_post);
    }

    public function storeImage($request, $id, $imagemAtual)
    {
        $arquivo = $request->file('imagem');

        $pastaDestino = base_path() . DIRECTORY_SEPARATOR . 'public/images/posts/imagens/previas';
        if ($arquivo == null) {
            $nomeArquivo = $imagemAtual;
        } else {

            $nomeArquivo ='previa'. $id . '.' . $request->file('imagem')->getClientOriginalExtension();
            $request->file('imagem')->move($pastaDestino, $nomeArquivo);
        }

        return $nomeArquivo;
    }

    public function show($id)
    {

        $id_perfil = Auth::user()->id_perfil;
        $postRepository = new PostRepository;
        $post = $postRepository->getPost($id);
        unset($postRepository);
        if ($id_perfil == 1 || $id_perfil == 2) {
            return view('posts.show')->with('post', $post);
        } else {
            return view('errors.400');
        }


    }


    public function edit($id)
    {
        $id_perfil = Auth::user()->id_perfil;
        $postRepository = new PostRepository;
        $post = $postRepository->getPost($id);
        $tags = $postRepository->getTags();
        unset($postRepository);
        if ($id_perfil == 1 || $id_perfil == 2) {
            return view('posts.edit')->with('post', $post)->with('tags', $tags);
        } else {
            return view('errors.400');
        }
    }


    public function update(Request $request, $id)
    {
        //Validar os dados
            $this->validate($request, array(
                    'titulo' => 'required|max:255',
                    'previa' => 'required',
                    'conteudo' => 'required'
                ));

            $postRepository = new PostRepository;
            $postRepository->updatePost($request, $id);
            $imagemAtual = $postRepository->getImage($id);
            $imagem = $this->storeImage($request, $id, $imagemAtual);
            $postRepository->storeImage($id, $imagem);

            Session::flash('sucesso', 'O post foi atualizado com sucesso');
            //flash para esta request e put para salvar na sessao
            return redirect()->route('posts.show', $id);
    }

    public function destroy($id)
    {
        $postRepository = new PostRepository;
        $postRepository->destroy($id);
        Session::flash('sucesso', 'O post foi deletado com sucesso');
        
        $postRepository = new PostRepository;

        $posts = $postRepository->getPosts(1);
        $postsjomesp = $postRepository->getPosts(2);
        $pagina = '';
        unset($postRepository);
        return view('posts.listPosts')->with('posts', $posts)->with('postsjomesp', $postsjomesp)->with('pagina', $pagina);
    }

     public function search(Request $request)
    {
        $postRepository = new PostRepository;

        $posts = $postRepository->search($request, 1);
        $postsjomesp = $postRepository->search($request, 2);

        unset($postRepository);
        return view('posts.listPosts')->with('posts', $posts)->with('postsjomesp', $postsjomesp)->with('pagina', $request->pagina);
    }

}
