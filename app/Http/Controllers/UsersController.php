<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\GroupAdvertisement;
use App\Models\Advertisement;
use App\Models\Logins;
use App\Models\Horario;
use App\Models\Day;
use App\Models\Campain;
use App\Models\ModuleInGroup;
use App\Models\Module;
use App\Models\SectionInGroup;
use App\Models\Section;
use App\Models\SubSectionInGroup;
use App\Models\SubSection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class UsersController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $modules = $this->modules();
        $campaigns = Campain::get();

        $user = Auth::user();

        $query_group = Group::leftjoin('horarios', 'horarios.id', '=', 'groups.horario_id')
            ->leftjoin('companies', 'companies.id', '=', 'groups.company_id')
            ->leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
            ->select(
                'groups.id as id',
                'groups.name as name'
            )
            ->where('groups.created_at_user', $user->name);
        //->where('groups.id', 14);


        $groups_general = $query_group->get();


        $query = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.user_verified_at',
            'users.created_at',
            'users.updated_at',
            'users.telefono',
            'users.genero',
            'users.fecha_naci',
            'users.group_perfil_id',
            'users.obs',
            'users.pass_change',
            'users.document_type_id',
            'users.document_number',
            'users.bank_id',
            'users.bank_account',
            'users.bank_cci',
            'users.fecha_inicio',
            'users.fecha_cese',
            'users.fecha_inicap',
            'users.fecha_fincap',
            'users.foto_perfil',
            'users.foto_doc',
            'users.curriculum',
            'users.contrato'
        )
            ->where('users.user_id', $user->id);

        if ($user->id != 44) {
            $query->where('users.user_id', $user->id);
        }

        $users = $query->get();

        $groups = UserGroup::select(
            'user_groups.id  as id',
            'user_groups.user_id  as user_id',
            'groups.id as group_id',
            'groups.name as group_name'
        )
            ->leftjoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->get();

        $user = User::findOrFail($user->id);

        if ($user->id === 44) {
            $company = Company::findOrFail(1);
        } else {

            $company = Company::leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
                ->select(
                    'companies.id as id',
                    'companies.name as name',
                    'companies.short_name as short_name',
                    'companies.document as document',
                    'companies.pais as pais',
                    'companies.contact as contact',
                    'companies.asist_type as asist_type',
                    'companies.sufijo as sufijo',
                    'companies.menu_color as menu_color',
                    'companies.text_color as text_color',
                    'companies.logo as logo',
                    'user_groups.user_id as dif',
                )
                ->where('user_groups.user_id', $user->id)
                ->first();

            if (!$company) {
                $company = Company::leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
                    ->select(
                        'companies.id as id',
                        'companies.name as name',
                        'companies.short_name as short_name',
                        'companies.document as document',
                        'companies.pais as pais',
                        'companies.contact as contact',
                        'companies.asist_type as asist_type',
                        'companies.sufijo as sufijo',
                        'companies.menu_color as menu_color',
                        'companies.text_color as text_color',
                        'companies.logo as logo',
                        'user_groups.user_id as dif',
                    )
                    ->where('user_groups.user_id', $user->user_id)
                    ->first();
            }
        }

        

        return view('users', compact('users', 'company', 'groups_general', 'groups', 'campaigns', 'modules', 'user', 'company'));
    }

    public function modules()
    {
        $userId = Auth::user()->id;
        $userGroup = UserGroup::where('user_id', $userId)
            ->first();
        $group = Group::where('id', $userGroup->group_id)
            ->first();
        $modulesGroup = ModuleInGroup::where('group_id', $group->id)
            ->get();
        $sectionsGroup = SectionInGroup::where('group_id', $group->id)
            ->get();
        $subSectionsGroup = SubSectionInGroup::where('group_id', $group->id)
            ->get();

        $modulesIds = [];
        foreach ($modulesGroup as $moduleGroup) {
            $modulesIds[] = $moduleGroup->module_id;
        };

        $sectionsIds = [];
        foreach ($sectionsGroup as $sectionGroup) {
            $sectionsIds[] = $sectionGroup->section_id;
        };

        $subSectionsIds = [];
        foreach ($subSectionsGroup as $subSectionGroup) {
            $subSectionsIds[] = $subSectionGroup->sub_section_id;
        };

        $modules = Module::whereIn('id', $modulesIds)
            ->get();
        $sections = Section::whereIn('id', $sectionsIds)
            ->orderBy('order', 'asc')
            ->get();
        /*
        $subSections = SubSection::whereIn('id', $subSectionsIds)
            ->get();
            */
            $user = Auth::user();
            if ($user->user_id == 44) {
                $subSections = SubSection::whereIn('id', $subSectionsIds)
                    ->where(function ($query) {
                        $query->where('section_id', '!=', 5)
                            ->orWhere(function ($q) {
                                $q->where('section_id', 5)
                                    ->where('created_at_user', Auth::user()->name);
                            });
                    })
                    ->get();
            } else {
    
                $admin = User::where('id', Auth::user()->user_id)->first();
    
                $subSections = SubSection::whereIn('id', $subSectionsIds)
                    ->where(function ($query) use ($admin) {
                        $query->where('section_id', '!=', 5)
                            ->orWhere(function ($q) use ($admin) {
                                $q->where('section_id', 5)
                                    ->where('created_at_user', $admin->name);
                            });
                    })
                    ->get();
            }

        $result = $modules->map(function ($module) use ($sections, $subSections) {

            $moduleSections = $sections->sortBy('order')->where('module_id', $module->id)->map(function ($section) use ($subSections) {
                $sectionSubSections = $subSections->where('section_id', $section->id);

                $section->subSections = $sectionSubSections->values();

                return $section;
            });

            $module->sections = $moduleSections->values();

            return $module;
        });

        $modules = $result;

        return $modules;
    }

    public function validateForm()
    {
        $messages = [
            'name.required'         => 'Debe ingresar un nombre.',
            'email.required'         => 'Debe ingresar un Email.',
            'password.required'     => 'Debe ingresar una contraseña.',
        ];

        $rules = [
            'name'                  => 'required',
            'email'                  => 'required',
            'password'              => 'required',
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

    public function AddGroup()
    {
        $this->validateModalGroup();

        $campaigns = Campain::get();

        $user_id = request('user_id');
        $group_id = request('group_id');

        $element = new UserGroup();
        $element->user_id = $user_id;
        $element->group_id = $group_id;
        $element->save();

        $groups_general = Group::get();
        $company = Company::findOrFail(1);
        $users = User::select(
            'users.id as id',
            'users.name as name',
            'users.email as email',
            'users.user_verified_at as user_verified_at',
            'users.created_at as created_at',
            'users.updated_at as updated_at',
            'users.telefono as telefono',
            'users.genero as genero',
            'users.fecha_naci as fecha_naci',
            'users.group_perfil_id as group_perfil_id',
            'users.obs as obs',
            'users.pass_change as pass_change',
            'users.document_type_id as document_type_id',
            'users.document_number as document_number',
            'users.bank_id as bank_id',
            'users.bank_account as bank_account',
            'users.bank_cci as bank_cci',
            'users.fecha_inicio as fecha_inicio',
            'users.fecha_cese as fecha_cese',
            'users.fecha_inicap as fecha_inicap',
            'users.fecha_fincap as fecha_fincap',
            'users.foto_perfil as foto_perfil',
            'users.foto_doc as foto_doc',
            'users.curriculum as curriculum',
            'users.contrato as contrato',
        )
            ->get();
        $groups = UserGroup::select(
            'user_groups.id  as id',
            'user_groups.user_id  as user_id',
            'groups.id as group_id',
            'groups.name as group_name'
        )
            ->leftjoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->get();

        $modules = $this->modules();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return redirect()->back()->with([
            'groups_general' => $groups_general,
            'company' => $company,
            'users' => $users,
            'groups' => $groups,
            'campaigns' => $campaigns,
            'modules' => $modules,
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function SaveUser()
    {

        $user = Auth::user();
        $campaigns = Campain::get();

        if ($user->id === 44) {
            $company = Company::findOrFail(1);
        } else {
            $company = Company::leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
                ->where('user_groups.group_id', 14)
                ->where('user_groups.user_id', $user->id)
                ->first();
        }

        $id = request('id');
        $name = request('name');
        $email = request('email');
        $password = bcrypt(request('password'));
        $telefono = request('telefono');
        $genero = request('genero');
        $fecha_naci = request('fecha_naci');
        $obs = request('obs');

        if (isset($id)) {
            $element = User::findOrFail($id);

            $Variable = $element->email;
            $pos = strpos($Variable, '@');
            $result = substr($Variable, $pos);

            if ($name) {
                $element->name = $name;
            }

            if ($email) {
                $element->email = $email . $result;
            }

            if ($password) {
                $element->password = $password;
                $element->remember_token = $password;
            }

            if ($telefono) {
                $element->telefono = $telefono;
            }

            if ($genero) {
                $element->genero = $genero;
            }

            if ($fecha_naci) {
                $element->fecha_naci = $fecha_naci;
            }

            if ($obs) {
                $element->obs = $obs;
            }
        } else {
            $this->validateForm();

            $element = new User();
            $element->name = $name;
            $element->email = $email . $company->sufijo;
            $element->password = $password;
            $element->remember_token = $password;
            $element->telefono = $telefono;
            $element->genero = $genero;
            $element->fecha_naci = $fecha_naci;
            $element->obs = $obs;
        }

        $element->user_id = Auth::user()->id;
        $element->save();

        $groups_general = Group::get();
        $company = Company::findOrFail(1);
        $users = User::select(
            'users.id as id',
            'users.name as name',
            'users.email as email',
            'users.user_verified_at as user_verified_at',
            'users.created_at as created_at',
            'users.updated_at as updated_at',
            'users.telefono as telefono',
            'users.genero as genero',
            'users.fecha_naci as fecha_naci',
            'users.group_perfil_id as group_perfil_id',
            'users.obs as obs',
            'users.pass_change as pass_change',
            'users.document_type_id as document_type_id',
            'users.document_number as document_number',
            'users.bank_id as bank_id',
            'users.bank_account as bank_account',
            'users.bank_cci as bank_cci',
            'users.fecha_inicio as fecha_inicio',
            'users.fecha_cese as fecha_cese',
            'users.fecha_inicap as fecha_inicap',
            'users.fecha_fincap as fecha_fincap',
            'users.foto_perfil as foto_perfil',
            'users.foto_doc as foto_doc',
            'users.curriculum as curriculum',
            'users.contrato as contrato',
        )
            ->get();
        $groups = UserGroup::select(
            'user_groups.id  as id',
            'user_groups.user_id  as user_id',
            'groups.id as group_id',
            'groups.name as group_name'
        )
            ->leftjoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->get();

        $modules = $this->modules();

        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return redirect()->back()->with([
            'groups_general' => $groups_general,
            'company' => $company,
            'users' => $users,
            'groups' => $groups,
            'campaigns' => $campaigns,
            'modules' => $modules,
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function DeleteUser($id)
    {
        $element = User::findOrFail($id);
        $element->delete();

        $type = 3;
        $title = 'Bien';
        $msg = 'Usuario eliminado exitosamente.';

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg
        ]);
    }

    public function DeleteUserGroup($id)
    {
        $group = UserGroup::findOrFail($id);
        $group->delete();

        $info = [
            'type'  => 1,
            'title' => 'Bien',
            'msg'   => 'Se quitó el grupo con éxito.',
        ];

        return response()->json([
            'info'  => $info,
        ]);
    }
}
