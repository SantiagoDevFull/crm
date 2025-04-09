<?php

namespace App\Http\Controllers;

use App\Models\Campain;
use App\Models\Company;
use App\Models\Block;
use App\Models\User;
use App\Models\UserGroup;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BlockController extends Controller
{

    public function index(Request $request)
    {
        $campain_id = 0;
        $campains = Campain::get();

        if ($request->query('id')) {
            $campain_id = $request->query('id');
        };

        return view('block', [
                                'campain_id'      => $campain_id,
                                'campains'      => $campains
                            ]);
    }

    public function validateForm()
    {
        $messages = [
            'campain_id.required'         => 'Debe seleccionar una campaña.',
        ];

        $rules = [
            'campain_id'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $this->validateForm();

        $campain_id = request('campain_id');

        $blocks = Block::leftjoin('campains', 'campains.id', '=', 'blocks.campain_id')
            ->select(
                'blocks.id as id',
                'campains.name as campain_name',
                'blocks.name as name',
                'blocks.order as order',
                'blocks.state as state',
            )
            ->where('campain_id', $campain_id)
            ->orderBy('blocks.order','asc')
            ->get();

        return $blocks;
    }

    public function validateModalForm()
    {

        $messages = [
            'name.required'         => 'Debe completar el nombre.',
            'order.required'         => 'Debe completar el Orden.',
            'campain_id.required'         => 'Debe elegir una Campaña.',
        ];

        $rules = [
            'name'                  => 'required',
            'order'                  => 'required',
            'campain_id'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {

        $this->validateModalForm();

        $id = request('id');
        $campain_id = request('campain_id');
        $name = request('name');
        $order = request('order');
        $state = 1; //activo

        if (isset($id)) {
            $tab_state =  Block::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $tab_state->updated_at_user = Auth::user()->name;
        } else {
            $tab_state = new Block();
            $tab_state->campain_id = $campain_id;
            $tab_state->state = $state;
            $tab_state->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $tab_state->name = $name;
        $tab_state->order = $order;

        $tab_state->save();

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function getBlock()
    {
        $id = request('id');
        $elements = Block::findOrFail($id);
        return $elements;
    }

    public function delete()
    {
        $element = Block::findOrFail(request('id'));
        $element->delete();

        $msg = 'Registro eliminado exitosamente';
        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function deshabilitar()
    {
        $element = Block::findOrFail(request('id'));
        $element->state = 0;
        $element->save();

        $msg = 'Registro deshabilitado exitosamente';
        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function habilitar()
    {
        $element = Block::findOrFail(request('id'));
        $element->state = 1;
        $element->save();

        $msg = 'Registro habilitado exitosamente';
        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }
}