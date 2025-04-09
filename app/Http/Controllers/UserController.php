<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $groups_general = Group::get();
        $company = Company::findOrFail(1);;
        $users = User::get();

        return view('user')->with(compact('users', 'company', 'groups_general'));
    }

    public function profile()
    {
        $userId = Auth::user()->id;
        $user = User::where('id', $userId)->first();

        //return view('profile', compact('user'));
        return "xd";
    }

    public function validateForm()
    {
        $messages = [
            'name.required'         => 'Debe ingresar un nombre.',
            'user.required'         => 'Debe ingresar un Usuario.',
            'password.required'     => 'Debe ingresar una contraseña.',
            'telefono.required'     => 'Debe ingresar un teléfono.',
            'genero.required'       => 'Debe seleccionar el género.',
            'fecha_naci.required'   => 'Debe ingresar la fecha de nacimiento.',
            'obs.required'          => 'Debe ingresar una observación.'
        ];

        $rules = [
            'name'                  => 'required',
            'user'                  => 'required',
            'password'              => 'required',
            'telefono'              => 'required',
            'genero'                => 'required',
            'fecha_naci'            => 'required',
            'obs'                   => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function validateModalGroup()
    {
        $messages = [
            'group_id.required'         => 'Debe seleccionar un Grupo.',
        ];

        $rules = [
            'group_id'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function addGroup()
    {
        $this->validateModalGroup();

        $user_id = request('user_id');
        $group_id = request('group_id');

        $info = [
            'type'  => 1,
            'title' => 'Bien',
            'msg'   => 'Grupo agregado con éxito.',
        ];

        $element = new UserGroup();
        $element->user_id = $user_id;
        $element->group_id = $group_id;
        $element->save();

        $list = $this->listGroup();

        return response()->json([
            'info'  => $info,
            'list'  => $list
        ]);
    }

    public function store()
    {
        $this->validateForm();

        $company = Company::findOrFail(1);;

        $id = request('id');
        $name = request('name');
        $user = request('user');
        $password = bcrypt(request('password'));
        $telefono = request('telefono');
        $genero = request('genero');
        $fecha_naci = request('fecha_naci');
        $obs = request('obs');

        if (isset($id)) {
            $element = User::findOrFail($id);
            $msg = 'Usuario actualizado exitosamente.';
        } else {
            $element = new User();
            $msg = 'Usuario creado exitosamente.';
        }

        $element->name = $name;
        $element->user = $user . $company->sufijo;
        $element->password = $password;
        $element->remember_token = $password;
        $element->telefono = $telefono;
        $element->genero = $genero;
        $element->fecha_naci = $fecha_naci;
        $element->obs = $obs;
        $element->save();

        $type = 3;
        $title = 'Bien';
        $url = route('dashboard.user.index');

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }

    public function delete()
    {

        $id = request('id');
        $element = User::findOrFail($id);
        $element->delete();

        $type = 3;
        $title = 'Bien';
        $msg = 'Usuario eliminado exitosamente.';
        $url = route('dashboard.user.index');


        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }

    public function listGroup()
    {

        $user_id = request('user_id');

        $groups = UserGroup::select(
            'user_groups.id  as id',
            'groups.id as group_id',
            'groups.name as group_name'
        )
            ->leftjoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->where('user_groups.user_id', $user_id)
            ->get();

        return $groups;
    }

    public function deleteGroup()
    {
        $id = request('id');
        $group = UserGroup::findOrFail($id);
        $group->delete();

        $list = $this->listGroup();

        $info = [
            'type'  => 1,
            'title' => 'Bien',
            'msg'   => 'Se quitó el grupo con éxito.',
        ];

        return response()->json([
            'info'  => $info,
            'list'  => $list
        ]);
    }
}
