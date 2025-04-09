<?php

namespace App\Http\Controllers;

use App\Models\Campain;
use App\Models\Company;
use App\Models\State;
use App\Models\TabState;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\StateState;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class StateController extends Controller
{

    public function index(Request $request)
    {
        $campain_id = 0;
        $campains = Campain::get();

        if ($request->query('id')) {
            $campain_id = $request->query('id');
        };

        return view('state', [
                                'campain_id'    => $campain_id,
                                'campains'      => $campains,
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

        $elements = State::leftjoin('tab_states', 'tab_states.id', '=', 'states.tab_state_id')
                        ->leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
                        ->select(
                            'states.id as id',
                            'campains.name as campain_name',
                            'tab_states.name as tab_state_name',
                            'states.name as name',
                            'states.color as color',
                            'states.order as order',
                            'states.state as state',
                            'states.not as not',
                            'states.age as age',
                            'states.com as com',
                        )
                        ->where('tab_states.campain_id', $campain_id)
                        ->orderBy('states.order', 'asc')
                        ->get();

        return $elements;
    }

    public function validateModalForm()
    {

        $messages = [
            'name.required'         => 'Debe completar el nombre.',
            'tab_state_id.required'         => 'Debe seleccionar una Pestaña de Estado.',
            'color.required'         => 'Debe seleccionar un Color.',
            'order.required'         => 'Debe completar el Orden.',
            'state_user.required'         => 'Debe seleccionar el Estado.',
        ];

        $rules = [
            'name'                  => 'required',
            'tab_state_id'                  => 'required',
            'color'                  => 'required',
            'order'                  => 'required',
            'state_user'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateModalForm();

        $id = request('id');
        $name = request('name');
        $tab_state_id = request('tab_state_id');
        $color = request('color');
        $order = request('order');
        $state = 1; //activo
        $state_user = request('state_user');
        $not = request('not') ? request('not') : 'off';
        $age = request('age') ? request('age') : 'off';
        $com = request('com') ? request('com') : 'off';
        $state_ids = request('state_ids');

        $state_state = json_decode($state_ids);

        if (isset($id)) {
            $element =  State::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $element->updated_at_user = Auth::user()->name;
        } else {
            $element = new State();
            $element->tab_state_id = $tab_state_id;
            $element->state = $state;
            $element->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $element->name = $name;
        $element->tab_state_id = $tab_state_id;
        $element->color = $color;
        $element->order = $order;
        $element->state_user = $state_user;
        $element->not = $not;
        $element->age = $age;
        $element->com = $com;

        $element->save();

        if (isset($id)) {
            StateState::where('to_state_id', $id)->delete();
        }

        $stateStateData = [];
        foreach ($state_state as $stateId) {
            $stateStateData[] = [
                'from_state_id' => $element->id,
                'to_state_id' => $stateId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        StateState::insert($stateStateData);

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
        $campain_id = request('campain_id');
        $elements = TabState::where('campain_id', $campain_id)->get();
        return $elements;
    }

    public function getState()
    {
        $id = request('id');
        $element = State::findOrFail($id);
        $states_states = StateState::where('from_state_id', $id)
                                            ->leftjoin('states', 'states.id', '=', 'states_states.to_state_id')
                                            ->select(
                                                'states.id as id',
                                                'states.name as name',
                                            )
                                            ->get();
        return response()->json([
                                'state' => $element,
                                'states' => $states_states
                            ]);
    }

    public function delete()
    {
        $element = State::findOrFail(request('id'));
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
        $element = State::findOrFail(request('id'));
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
        $element = State::findOrFail(request('id'));
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
