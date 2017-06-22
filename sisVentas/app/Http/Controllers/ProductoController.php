<?php

namespace sisVentas\Http\Controllers;

use Illuminate\Http\Request;

use sisVentas\ProductoModel;
use Illuminate\Support\Facades\Redirect;
use sisVentas\Http\Requests\ProductoRequest;
use DB;

class ProductoController extends Controller
{
    public function __construct(){

    }

    public function Index(Request $request){
    	if($request){
    		$query = trim($request->get('searchText'));
    		$productos = DB::table('Producto')
    				->where('Nombre','LIKE','%'.$query.'%')
    				->orderBy('idProducto','desc')
    				->paginate(7);
			return view('Almacen.Productos.Index',["productos"=>$productos,"searchText"=>$query]);

    	}
    }

    public function Create(){
    	return view("Almacen.Productos.Create");

    }

    public function Store(ProductoRequest $request){
    	$productoM = new ProductoModel;
        $productoM->fk_idVendedor = $request->get('fk_vendedor');
        $productoM->Nombre = $request->get('nombre');
        $productoM->Descripcion = $request->get('descripcion');
        $peso = $request->get('peso');
        $productoM->Peso = $peso;
        $costo = $request->get('precioCosto');
        $productoM->PrecioCosto = $costo;

        //CALCULANDO PRECIO DE VENTA
        $seguro = 0.07;
        $iva = 0.12;
        $precioEnvio = 3.55;
        $final = $peso * $precioEnvio;
        $final = $final + $costo;  
        $final = $final + ($costo * $seguro);
        $final = $final + ($iva * $final);

        $productoM->PrecioVenta = round($final,2);
        $productoM->save();
    	return Redirect::to('Almacen/Productos');
    }

    public function Show($id){
    	return view("Almacen.Productos.Show",["Producto"=>ProductoModel::findOrFail($id)]);
    }

    public function Edit(){
    	return view("Almacen.Productos.Edit",["Producto"=>ProductoModel::findOrFail($id)]);
    }

    public function Update(ProductoRequest $request, $id){
    	$productoM = ProductoModel::findOrFail($id);
    	$productoM->fk_idVendedor = $request->get('fk_vendedor');
    	$productoM->Nombre = $request->get('nombre');
		$productoM->Peso = $request->get('peso');
    	$productoM->Descripcion = $request->get('descripcion');
    	$productoM->PrecioCosto = $request->get('precioCosto');
    	$productoM->PrecioVenta = $request->get('precioVenta');
    	$productoM->update();
    	return Redirect::to('Almacen/Productos');
    }

    // public function destroy(){

    // }
}
