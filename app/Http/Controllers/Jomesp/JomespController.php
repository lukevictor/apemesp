<?php

namespace App\Http\Controllers\Jomesp;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;

use Session;

use View;

use DB;

use App\Http\Controllers\Controller;

use App\Apemesp\Repositories\Apemesp\PagesRepository;

use App\Apemesp\Models\Tag;

use Cache;

class JomespController extends Controller{


public function getIndex()
{
	$posts = DB::table('posts')
                     ->select('*')
                     ->where('id_destino', 2)
                     ->orderBy('posts.id', 'desc')
                     ->paginate(6);

		$posts->setPath('/');
		$tags = Tag::all();
		
        return view('jomesp.index')
        		->with('posts', $posts)
        		->with('tags', $tags);
}

public function getContato()
{
	return view('jomesp.contato');
}

public function getEdicoes()
{
    return view('jomesp.edicoes');
}

public function getPost($id)
{

        $page = new PagesRepository;
        $post = $page->getPost($id);
        $view = $page->getPostView($id);
    
        if ($post == null) {
            return view('errors.post');
        } else {
            if (Cache::has($id) == false) {
                Cache::add($id, 'contador', 0.30);
                $view->total_visitas++;
                $view->save();
            }
            unset($page);

            return view('jomesp.posts.show')->with('post', $post);
        }

}




}

