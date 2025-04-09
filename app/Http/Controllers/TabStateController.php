<?php

namespace App\Http\Controllers;

use App\Models\Campain;
use App\Models\Company;
use App\Models\TabState;
use App\Models\User;
use App\Models\UserGroup;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TabStateController extends Controller
{

    public function index(Request $request)
    {
        $campain_id = 0;
        $campains = Campain::get();

        if ($request->query('id')) {
            $campain_id = $request->query('id');
        };

        return view('tab_state', [
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

        $tab_states = TabState::leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->select(
                'tab_states.id as id',
                'campains.name as campain_name',
                'tab_states.name as name',
                'tab_states.order as order',
                'tab_states.state as state',
            )
            ->where('campain_id', $campain_id)
            ->orderBy('tab_states.order','asc')
            ->get();

        return $tab_states;
    }

    public function validateModalForm()
    {

        $messages = [
            'name.required'         => 'Debe completar el nombre.',
            'order.required'         => 'Debe completar el Orden.',
        ];

        $rules = [
            'name'                  => 'required',
            'order'                  => 'required',
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
            $tab_state =  TabState::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $tab_state->updated_at_user = Auth::user()->name;
        } else {
            $tab_state = new TabState();
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

    public function getTabState()
    {
        $id = request('id');
        $elements = TabState::findOrFail($id);
        return $elements;
    }

    public function delete()
    {
        $element = TabState::findOrFail(request('id'));
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
        $element = TabState::findOrFail(request('id'));
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
        $element = TabState::findOrFail(request('id'));
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
