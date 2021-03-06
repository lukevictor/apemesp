@extends('main')

@section('titulo', '| Home')

@section('extrastyle')
<style type="text/css">
.tagline-divider {
    margin: 15px auto 3px;
    max-width: 250px;
    border-color: #999999;
}

.box {
    margin-bottom: 20px;
    padding: 30px 15px;
    background: #fff;
    background: rgba(255,255,255,0.9);
}

.intro-text {
    text-transform: uppercase;
    font-size: 1.25em;
    font-weight: 400;
    letter-spacing: 1px;
}

.img-border {
    float: none;
    margin: 0 auto 0;
    border: #999999 solid 1px;
}

.img-left {
    float: none;
    margin: 0 auto 0;
}

@media screen and (min-width:1000px) {
    .brand {
        display: inherit;
        margin: 0;
        padding: 30px 0 10px;
        text-align: center;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        font-family: "Josefin Slab","Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 5em;
        font-weight: 700;
        line-height: normal;
        color: #fff;
    }

    .top-divider {
        margin-top: 0;
    }

    .img-left {
        float: left;
        margin-right: 25px;
    }
}
</style>
@endsection

@section('categorias')

    <a href="{{ url('')}}" class="btn btn-primary">
        <i class="chatter-bubble"></i>
        Todas as categorias
      </a>
      <ul class="nav nav-pills nav-stacked">

    @foreach($tags as $tag)

        <li>
          <a href="{{ url('')}}/tags/{{ $tag->id }}">
            <div class="chatter-box"></div>
            <i class="fa fa-music" aria-hidden="true"></i>
            - {{ $tag->tag }}
          </a>
        </li>
    @endforeach
      </ul>

@endsection

@section('conteudo')


        @foreach($posts as $post)


           <div class="jumbotron" style="background-color: #fffefd; padding-bottom: 10%;">

                    <hr>
                    <a href="/pages/post/{{ $post->id }}" class="" style="color: black;">
                    <h2 class="intro-text text-center"><strong>{{ $post->titulo }}</strong>
                        <br> {{ $post->subtitulo }}
                    </h2>
                    </a>
                    <hr>
                    <img class="img-responsive img-border img-left bordas" src="{{ url('images/') }}/posts/imagens/previas/{{ $post->imagem_previa }}" style="height: 120px; width: 300px;" alt="">
                    <hr class="visible-xs">
                    <p>{{ $post->previa }}</p>
                    <a href="/pages/post/{{ $post->id }}" class=""> Continuar lendo <i class="fa fa-long-arrow-right"> </i></a>
                    <p><div style="float: left;"> Postado em:
                        <?php

                        $data = new DateTime($post->created_at);
                        echo $data->format('d-m-Y H:i:s');

                        ?>
                    </div></p>


        </div>

        <hr>


        @endforeach


         <p> {{ $posts->render() }} </p>



    </div>
    @endsection
