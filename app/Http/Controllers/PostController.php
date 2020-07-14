<?php

namespace App\Http\Controllers;

use App\Category;
use App\Helpers\JwtAuth;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show','getImage','getPostsByCategory', 'getPostsByUser']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all()->load('category')->load('user');


        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //recoger datos por Post

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $params = json_decode($json);

        if (!empty($params) && !empty($params_array)) {


            //conseguir usuario identificado

            $user = $this->getidentity($request);
            //validar datos

            $validate = Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'

            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el post, faltan datos'
                ];
            } else {
                //guardar article

                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->save();
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia datos del post correctamente'
            ];
        }
        //devolver una response

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id) ->load('category')
        ->load('user'); 
        if (is_object($post)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' =>  $post
            ];
        
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'no existe la entrada'
            ];
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        //recoger datos que llegan por Post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);


        if (!empty($params_array)) {

            //validar datos

            $validate = Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',

            ]);

            if ($validate->fails()) {

                $data['errors'] = $validate->errors();
            } else {
                //eliminar lo que no actualizaremos

                unset($params_array['created_at']);
                unset($params_array['user_id']);
                unset($params_array['id']);
                unset($params_array['user']);
                unset($params_array['category']);


                //conseguir usuario identificado

                $user = $this->getidentity($request);


                //buscar el registro
                $post = Post::where('id', $id)->where('user_id', $user->sub)->first();
                $post->update($params_array);
                if (!empty($post) && is_object($post)) {
          
                 

                //actualizar el registro

                
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'changes' => $params_array,
                        'post' => $post

                    ];
                    return response()->json($data, $data['code']);
                }
                
            }
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'actualiza el registro con datos correctos'
            ];
            return response()->json($data, $data['code']);
        }
        //devolver una response
    /*     return response()->json($data, $data['code']); */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //conseguir usuario identificado

        $user = $this->getidentity($request);

        //conseguir el registro

        $post = Post::where('id', $id)->where('user_id', $user->sub)->first();

        if (!empty($post)) {
            //borrar el registro
            $post->delete();

            //devolver algo

            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No existe el post a eliminar'
            ];
        }
        return response()->json($data, $data['code']);
    }

    private function getidentity(request $request)
    {


        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);
        return $user;
    }

public function upload(request $request){
//recoger la imagen de la peticion

$image= $request->file('file0');

//validar la imagen
$validate=  Validator::make($request->all(), [
'file0'=>'required|image'
]);



if($validate->fails()|| !$image){
$data =[
    'code'=>400,
    'status'=>'error',
    'message'=>'error al subir imagen'
];

}else{
//guardar la imagen en el disco
    $image_name=time().$image->getClientOriginalName();


    Storage::disk('images')->put($image_name, File::get($image));

$data=[
'code'=>200,
'status'=>'success',
'image'=>$image_name
];
}



//devolver datos
return response()->json($data, $data['code']);

}

public function getImage($filename){

//comprobar si existe el dichero
$isset= Storage::disk('images')->exists($filename);

if($isset){
//conseguir la imagen
$file=Storage::disk('images')->get($filename);

//devolver imagen

return new Response($file, 200);

}else{

//mostrar posible error

$data=[

    'code'=>404,
    'status'=>'error',
'message'=>'La imagen no existe'
];
}

return response()->json($data, $data['code']);


}

public function getPostsByCategory($id){

    $posts=Post::where('category_id', $id)->get();


return response()->json([
'status'=>'success',
'posts'=>$posts


],200);
}

public function getPostsByUser($id){
  
    $posts=Post::where('user_id', $id)->get();

    return response()->json([
'status'=>'success',
'posts'=>$posts
    ],200);

}

}
