<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\Horario;
use App\Models\Day;

use Illuminate\Support\Facades\Auth;

class GroupUserController extends Controller
{
    public function index()
    {

        $companies = Company::get();
        $groups = Group::leftjoin('horarios', 'horarios.id', '=', 'groups.horario_id')
                        ->select(
                            'groups.id as id',
                            'groups.company_id as company_id',
                            'groups.campana_id as campana_id',
                            'groups.name as name',
                            'groups.ip as ip',
                            'groups.horario_id as horario_id',
                            'groups.state as state',
                            'groups.perfil_id as perfil_id',
                            'groups.created_at as created_at',
                            'groups.updated_at as updated_at',
                            'groups.created_at_user as created_at_user',
                            'groups.updated_at_user as updated_at_user',
                            'groups.deleted_at as deleted_at',
                            'groups.permissions as permissions',
                            'horarios.name as horario_name',
                        )
                        ->get();
        $hours = Horario::get();
        return view('group_user')->with(compact('groups', 'companies', 'hours'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'      => 'Debe seleccionar una Compañía.',
            'name.required'            => 'Debe ingresar un Nombre.',
            'ip.required'              => 'Debe ingresar una IP.'
        ];

        $rules = [
            'company_id'            => 'required',
            'name'                  => 'required',
            'ip'                    => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $id = request('id');
        $company_id = request('company_id');
        $name = request('name');
        $ip = request('ip');
        $permissions = request('permissions');
        $horario_id = request('horario_id');

        if (isset($id)) {
            $element = Group::findOrFail($id);
            $element->updated_at_user = Auth::user()->name;
            $msg = 'Grupo actualizado exitosamente.';
        } else {
            $element = new Group();
            $element->created_at_user = Auth::user()->name;
            $msg = 'Grupo creado exitosamente.';
        }

        $element->company_id = $company_id;
        $element->name = $name;
        $element->ip = $ip;
        $element->permissions = $permissions;
        $element->horario_id = $horario_id;
        $element->save();

        $type = 3;
        $title = 'Bien';
        $url = route('dashboard.group.user.index');

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }

    public function getGroup()
    {
        $userID = Auth::user()->id;
        $user_group = UserGroup::where('user_id', $userID)->first();
        $group = NULL;
        $horario = NULL;

        // date_default_timezone_set('America/Lima');
        // setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain', 'es_ES', 'es');

        // $dayOfWeek = strftime("%A");
        // $dayStr = ucfirst($dayOfWeek);
        $days = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sabado',
            'Sunday' => 'Domingo',
        ];
        $dayStr = date('l');

        if ($user_group) {
            $group = Group::where('id', $user_group->group_id)->first();
            $horario = Day::where('horario_id', $group->horario_id)
                            ->where('day', $days[$dayStr])
                            ->first();
        };

        return response()->json([
                                'group'     => $group,
                                'horario'   => $horario
                            ]);
    }

    public function delete()
    {

        $id = request('id');
        $element = Group::findOrFail($id);
        $element->delete();

        $type = 3;
        $title = 'Bien';
        $msg = 'Grupo eliminado exitosamente.';
        $url = route('dashboard.group.user.index');


        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }
}
