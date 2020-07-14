<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "test user controller ";
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function register(Request $request)
    {

        //1) recoger datos del usuario que llegan por POST y decodificarlos

        $json = $request->input('json', null);

        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true); //array


        //2)) Limpiar datos

        $params_array = array_map('trim', $params_array);

        // 3) validar los datos

        if (!empty($params_array) && !empty($params)) {

            //validacion
            $validate = Validator::make($params_array, [

                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users', // comprobar si el usuario already exists
                'password' => 'required '

            ]);

            if ($validate->fails()) {
                //validacion fallida
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'el usuario no se ha creado correctamente',
                    'errors' => $validate->errors()

                );
                return response()->json($data, 400);
            } else {

                //validacion pasada correctamente

                //4) cifrar la password
                $pwd =  hash('sha256', $params->password);



                //5) crear el usuario

                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'user';


                //guardar usuario
                $user->save();



                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'el usuario se ha creado correctamente',
                    'user'=>$user,

                );
            }
        } else {


            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos',

            );
        }







        return response()->json($data, $data['code']);
    }



    public function login(Request $request)
    {

$jwtAuth= new JwtAuth();


//Recibir datos por POST
$json= $request->input('json', null);

$params= json_decode($json);
$params_array= json_decode($json, true);
//validar datos

$validate = Validator::make($params_array, [

  
    'email' => 'required|email', 
    'password' => 'required '

]);
if ($validate->fails()) {
    //validacion fallida
    $signup = array(
        'status' => 'error',
        'code' => 404,
        'message' => 'No se ha podido iniciar sesion',
        'errors' => $validate->errors()

    );
    
}else{
//cifrar la contraseña
$pwd= hash('sha256', $params->password);


//devolver token o datos
$signup=$jwtAuth->signup($params->email, $pwd);
if(!empty($params->gettoken)){

    $signup=$jwtAuth->signup($params->email, $pwd, true);
}

}




return response()->json($signup, 200);


    }
/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {

        //comprobar si el usuario esta autenticado

       $token= $request->header('Authorization');

       $jwtAuth= new JwtAuth();

       $checkToken= $jwtAuth->checkToken($token);
//actualizar el Usuario
    /* //recoger datos por post */
    $json= $request->input('json', null);
    $params_array=json_decode($json, true);
      
    
    if($checkToken && !empty($params_array)){

    

//sacar usuario identificado
$user= $jwtAuth->checkToken($token, true);

//validar los datos

$validate= Validator::make($params_array, [
    'name' => 'required|alpha',
    'surname' => 'required|alpha',
    'email' => 'required|email|unique:users,'.$user->sub// 
   

]);


//quitar campos a no actualizar
unset($params_array['id']);
unset($params_array['role']);
unset($params_array['password']);
unset($params_array['created_at']);
unset($params_array['remember_token']);



//actualizar usuario en Base de datos

$user_update= User::where('id', $user->sub)->update($params_array); 

//devolver array con resultado

$data= array(
    'code'=>200,
    'status'=>'success',
    'message'=>'el usuario se actualizo correctamente',
    'user'=> $user,
    'changes'=>$params_array

);



       }else{
        $data= array(

            'code'=>400,
            'status'=>'error',
            'message'=>'el usuario no esta identificado'
            );
            
       }

       return response()->json($data, $data['code']);
    }


    public function upload(Request $request){
        
//recoger los datos de la peticion

$image= $request->file('file0');


//validacion de la imagen

$validate= Validator::make($request->all(),[
    'file0'=>'required|image'
]);


//Guardar imagen

if(!$image || $validate->fails()){
    $data= array(

        'code'=>400,
        'status'=>'error',
        'message'=>'error al subir imagen'
        );
  
}else{

    $image_name=time().$image->getClientOriginalName();
    Storage::disk('users')->put($image_name, File::get($image));


    $data=array(
        'code'=>200,
        'status'=>'success',
        
'image'=>$image_name,


    );


    
}


//devolver el resultado


        return response()->json($data, $data['code']);
    }



public function getImage($filename){

    //comprobar si existe el filename

    $isset= Storage::disk('users')->exists(($filename));
    if ($isset) {
        
    $file= Storage::disk('users')->get($filename);
    return new Response($file, 200);
    }else{
        $data= array(

            'code'=>404,
            'status'=>'error',
            'message'=>'la imagen no existe'
            );

            return response()->json($data, $data['code']);  
    }



}

public function detail($id){

    $user= User::find($id);
    if (is_object($user)){
        $data= array(
'code'=>200,
'status'=> 'success',
'user'=>$user


        );
    }else{
        $data= array(
            'code'=>404,
            'status'=> 'error',
        'message'=>'el usuario no existe'
            
            
                    );
    }

    return response()->json($data,$data['code']);
}


}
