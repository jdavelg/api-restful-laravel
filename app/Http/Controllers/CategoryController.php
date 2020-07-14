<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

public function __construct()
{
   $this->middleware('api.auth', ['except'=> ['index','show']]);
}


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $categories = Category::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories,

        ],200);
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

        //recoger los datos
        $json= $request->input('json',null);
$params_array= json_decode($json, true);


if (!empty($params_array)) {
    # code...


//validar los datos


$validate= Validator::make($params_array, [
'name'=>'required'

]);
//guardar categoria

if ($validate->fails()) {
    $data=[

        'code'=>400,
        'status'=>'error',
        'message'=>'no se ha guardado la categoria'


    ];

}else{

    $category= new Category();
    $category->name= $params_array['name'];
    $category->save();

    $data=[

        'code'=>200,
        'status'=>'success',
        'category'=>$category


    ];
}
}else{
    $data=[

        'code'=>400,
        'status'=>'error',
        'message'=>'no has enviado ninguna categoria'


    ]; 
}
//devolver results
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
        $category = Category::find($id);

        if (is_object($category)) {

            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        } else {

            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La categoria no existe'
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
        //recoger los datos que llegan por POST

        $json= $request->input('json',null);
        $params_array= json_decode($json, true);

        if(!empty($params_array)){

//validar los datos

$validate= Validator::make($params_array, [
    'name'=>'required'

]);


//quitar lo que no se actualiza

unset($params_array['id']);
unset($params_array['created_at']);

//actualizar registro de categoria

$category= Category::where('id', $id)->update($params_array);
$data = [
    'code' => 200,
    'status' => 'success',
    'category' => $params_array
];
}else{
    $data = [
        'code' => 404,
        'status' => 'error',
        'message' => 'No has enviado ninguna categoria'
    ];

}



//devolver los datos

return response()->json($data, $data['code']);
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
}
