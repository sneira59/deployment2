<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inventario;
use App\Models\UsuarioInventario;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\InventarioRequest;
use App\Http\Requests\InventarioEditRequest;
use Alert;






class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('example');

    }

    public function index()
    {
        $iv = DB::table('usuario_inventario')
        ->select(
            'usuario_inventario.nomb_usua',
            'usuario_inventario.idUsuario',
            'usuario_inventario.cedula',
            'cargo.nomb_cargo'
        )
        ->join('cargo','usuario_inventario.FK_CARGO', '=', 'cargo.idCargo')
        ->get();

        $al = DB::table('usuario_inventario')
        ->select(DB::raw("COUNT(*) as count_row"))
        ->get();

        return view('inventario.inventario')->with('iv', $iv )
        ->with('al', $al );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $c = DB::table('cargo')
        ->select('idCargo','nomb_cargo')
        ->get();

        $e = DB::table('equipo')
        ->select('idEquipo','nomb_equipo')
        ->get();

        $u = DB::table('usuario_inventario')
        ->select('idUsuario')
        ->get();


        return view('inventario.create')->with('c', $c)->with('e',$e)->with('u',$u);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InventarioRequest $request)
    {

      

        $i = new UsuarioInventario();
        $i->nomb_usua = $request->input('name');
        $i->cedula = $request->input('cedula');
        $i->FK_CARGO = $request->input('cargo');
        $i->save();

        $e = new Inventario();
        $e->FK_EQUI = $request->input('equipo');
        $e->serial = $request->input('serial');
        $e->placa = $request->input('placa');
        $e->observacion = $request->input('observer');
        $e->FK_U = $i->idUsuario;
        $e->save();
        Alert::success('Felicidades!', 'Usuario registrado Correctamente.');


        return redirect('inventarios');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $in = UsuarioInventario::find($id);
        
        $iv = DB::table('Inventario')->where('idUsuario','=',$id)
        ->select(
            'usuario_inventario.nomb_usua',
            'usuario_inventario.cedula',
            'usuario_inventario.idUsuario',
            'cargo.nomb_cargo',
            'Inventario.idUsuaActivo',
            'Inventario.serial',
            'Inventario.placa',
            'equipo.nomb_equipo',
            'Inventario.FK_U',
            'Inventario.acta'

        )
        ->join('usuario_inventario','Inventario.FK_U', '=', 'usuario_inventario.idUsuario')
        ->join('cargo','usuario_inventario.FK_CARGO', '=', 'cargo.idCargo')
        ->join('equipo','Inventario.FK_EQUI', '=', 'equipo.idEquipo')
        ->get();

        return view('inventario.show')->with('in',$iv);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $iv = UsuarioInventario::find($id);
        $c = DB::table('cargo')
        ->select('cargo.nomb_cargo','cargo.idCargo')
        ->get();
        return view('inventario.edit')->with('c', $c)->with('iv', $iv);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InventarioEditRequest $request, $id)
    {
        $inventarios = UsuarioInventario::find($id);
        $inventarios->nomb_usua = $request->input('name');
        $inventarios->cedula = $request->input('cedula');
        $inventarios->FK_CARGO = $request->input('cargo');
        $inventarios->save();
        Alert::success('Felicidades!', 'Usuario Editado Correctamente.');

        return redirect('inventarios');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inventarios = UsuarioInventario::find($id);
        $inventarios->delete();
        Alert::success('Felicidades!', 'Usuario Eliminado Correctamente.');
        return redirect('inventarios');
    }

    public function crear($id){

        $c = DB::table('equipo')
        ->select('idEquipo','nomb_equipo')
        ->get();

        $u = DB::table('usuario_inventario')->where('idUsuario',"=",$id)
        ->select('idUsuario','nomb_usua')
        ->get();
        

        return view('equipo.create')->with('c',$c)->with('u',$u);
    }

    public function almacenar(Request $request) {
        $id = session()->get('id');
        $e = new Inventario();
        $e->FK_EQUI = $request->input('equipo');
        $e->serial = $request->input('serial');
        $e->placa = $request->input('placa');
        $e->observacion = $request->input('observer');
        $e->FK_U = $request->input('idfk');
        $e->save();
        Alert::success('Felicidades!', 'Equipo Agregado Correctamente.');

        return redirect('inventarios');


    }
    public function mostrarobs($id){
        $ob = DB::table('Inventario')->where('idUsuaActivo','=',$id)
        ->select(
            'Inventario.observacion',

        )
        ->join('usuario_inventario','Inventario.FK_U', '=', 'usuario_inventario.idUsuario')
        ->join('equipo','Inventario.FK_EQUI', '=', 'equipo.idEquipo')
        ->get();
  
        return view('observe.show')->with('ob',$ob);

    }
    public function editareq($id){

        $equipos = Inventario::find($id);

        $c = DB::table('equipo')
        ->select('idEquipo','nomb_equipo')
        ->get();

        $u = DB::table('usuario_inventario')
        ->select('idUsuario','nomb_usua')
        ->get();

        $a = DB::table('Inventario')
        ->select('serial','placa','observacion')
        ->get();

        return view('equipo.edit')->with('c', $c)->with('u', $u)->with('a', $a)->with('equipos',$equipos);

    }
    public function actualizareq(Request $request, $id){
        $equipos = Inventario::find($id);
        $equipos->FK_EQUI = $request->input('equipo');
        $equipos->serial = $request->input('serial');
        $equipos->placa = $request->input('placa');
        $equipos->observacion = $request->input('observer');
        $equipos->save();
        Alert::success('Felicidades!', 'Equipo Actualizado Correctamente.');
        return redirect('inventarios');


    }

    public function eliminar($id){
        $equipos = Inventario::find($id);
        $equipos->delete();
        Alert::success('Felicidades!', 'Equipo Eliminado Correctamente.');
        return redirect('inventarios');

    }

    public function enable($id){
        $in = Inventario::find($id);

        switch($in->acta){
            case null: $in->acta = 1;
                        $in->save();
                        Alert::success('Felicidades!', 'Acta igual a = ?.');

            break;

            case 1: $in->acta = 2;
                    $in->save();
                        Alert::success('Felicidades!', 'Entregado con acta');
            break;
            
            case 2: $in->acta = 1;
                    $in->save();
                    Alert::success('Felicidades!', 'Acta Enviada');

            break;
        }
        return redirect('inventarios');
    }



}