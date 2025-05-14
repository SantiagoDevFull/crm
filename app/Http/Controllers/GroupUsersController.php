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

class GroupUsersController extends Controller
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
        $company = Company::findOrFail(1);
        $modules = $this->modules();
        $user_id = Auth::user()->id;

        if ($user_id == 44) {
            $allModules = $this->allModules();
        } else {
            $allModules = $this->modules();
        }

        $campaigns = Campain::get();

        if ($user_id == 44) {
            $companies = Company::where('id', 1)
                ->get();
        } else {
            $companies = Company::leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
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
                )
                ->where('user_groups.user_id', $user_id)
                ->get();
        }


        $userId = Auth::user()->id;
        if ($userId == 44) {
            $groups = Group::leftjoin('horarios', 'horarios.id', '=', 'groups.horario_id')
                ->leftjoin('companies', 'companies.id', '=', 'groups.company_id')
                ->leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
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
                ->where('groups.created_at_user', Auth::user()->name)
                ->get();
            echo "entre";
        } else {
            $groups = Group::leftjoin('horarios', 'horarios.id', '=', 'groups.horario_id')
                ->leftjoin('companies', 'companies.id', '=', 'groups.company_id')
                ->leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
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
                ->where('user_groups.user_id', $user_id)
                ->where('user_groups.group_id', 14)
                ->get();
        }
        $hours = Horario::get();
        $modulesGroup = ModuleInGroup::get();
        $sectionsGroup = SectionInGroup::get();
        $subSectionsGroup = SubSectionInGroup::get();
        $user = User::findOrFail($userId);


        /*
        foreach ($allModules as $module) {
            echo "📦 Módulo: " . $module->id . PHP_EOL . "<br>";

            foreach ($module->sections as $section) {
                echo "  └── 📁 Sección: " . $section->id . PHP_EOL . "<br>";

                foreach ($section->subSections as $subSection) {
                    echo "      └──────── 📄 SubSección: " . $subSection->id . PHP_EOL . "<br>";
                }
            }
        }
            */




        return view('groups', compact('companies', 'groups', 'hours', 'campaigns', 'modules', 'allModules', 'modulesGroup', 'sectionsGroup', 'subSectionsGroup', 'user', 'company'));
    }

    public function modules()
    {
        $user = Auth::user();

        $userGroup = UserGroup::where('user_id', $user->id)
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

            $moduleSections = $sections->where('module_id', $module->id)->map(function ($section) use ($subSections) {
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

    public function allModules()
    {

        $user_name = Auth::user()->name;

        $modules = Module::get();
        $sections = Section::get();
        //$subSections = SubSection::where('created_at_user',$user_name)->get();
        $subSections = SubSection::get();

        $result = $modules->map(function ($module) use ($sections, $subSections) {

            $moduleSections = $sections->where('module_id', $module->id)->map(function ($section) use ($subSections) {
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
            'company_id.required'      => 'Debe seleccionar una Compañía.',
            'name.required'            => 'Debe ingresar un Nombre.',
            //'ip.required'              => 'Debe ingresar una IP.'
        ];

        $rules = [
            'company_id'            => 'required',
            'name'                  => 'required',
            //'ip'                    => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function SaveGroup(Request $request)
    {
        $this->validateForm();

        Log::info("aqui estoy");
        $campaigns = Campain::get();
        $id = request('id');
        $company_id = request('company_id');
        $name = request('name');
        $ip = request('ip');
        $permissions = request('permissions');
        $horario_id = request('horario_id');
        $selectedNodes = json_decode(request('selected_nodes'), true);


        $moduleIds = [];
        $sectionIds = [];
        $subSectionIds = [];

        foreach ($selectedNodes as $item) {
            if (preg_match('/^module_(\d+)/', $item, $matches)) {
                $moduleIds[] = (int)$matches[1];
            } elseif (preg_match('/^section_(\d+)/', $item, $matches)) {
                $sectionIds[] = (int)$matches[1];
            } elseif (preg_match('/^subSection_(\d+)/', $item, $matches)) {
                $subSectionIds[] = (int)$matches[1];
            }
        }

        $sections = Section::whereIn('id', $sectionIds)
            ->get();
        $subSections = SubSection::whereIn('id', $subSectionIds)
            ->get();

        echo Log::info("secciones primero : " . implode(',', $sectionIds));

        foreach ($sections as $section) {
            $moduleIds[] = $section->module_id;
        };
        foreach ($subSections as $subSection) {
            $sectionIds[] = $subSection->section_id;
        };

        $moduleIds = array_unique($moduleIds);
        $sectionIds = array_unique($sectionIds);
        $subSectionIds = array_unique($subSectionIds);

        Log::info("modulos : " . implode(',', $moduleIds));
        Log::info("secciones : " . implode(',', $sectionIds));
        Log::info("sub : " . implode(',', $subSectionIds));

        if (isset($id)) {
            $element = Group::findOrFail($id);
            ModuleInGroup::where('group_id', $id)->delete();
            SectionInGroup::where('group_id', $id)->delete();
            SubSectionInGroup::where('group_id', $id)->delete();
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

        foreach ($moduleIds as $moduleId) {
            $new = new ModuleInGroup();
            $new->module_id = $moduleId;
            $new->group_id = $element->id;
            $new->created_at_user = Auth::user()->name;
            $new->save();
        }

        foreach ($sectionIds as $sectionId) {
            $new = new SectionInGroup();
            $new->section_id = $sectionId;
            $new->group_id = $element->id;
            $new->created_at_user = Auth::user()->name;
            $new->save();
        }

        foreach ($subSectionIds as $subSectionId) {
            $new = new SubSectionInGroup();
            $new->sub_section_id = $subSectionId;
            $new->group_id = $element->id;
            $new->created_at_user = Auth::user()->name;
            $new->save();
        }

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

        $modules = $this->modules();
        $allModules = $this->allModules();

        $modulesGroup = ModuleInGroup::get();
        $sectionsGroup = SectionInGroup::get();
        $subSectionsGroup = SubSectionInGroup::get();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return redirect()->back()->with([
            'companies' => $companies,
            'groups' => $groups,
            'hours' => $hours,
            'campaigns' => $campaigns,
            'modules' => $modules,
            'allModules' => $allModules,
            'modulesGroup' => $modulesGroup,
            'sectionsGroup' => $sectionsGroup,
            'subSectionsGroup' => $subSectionsGroup,
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function DeleteGroup($id)
    {
        $usersGroups = UserGroup::where('group_id', $id)->get();

        if (count($usersGroups)) {
            return response()->json([
                'error' => 'El grupo tiene usuarios asignados.',
            ], 400);
        }

        $element = Group::findOrFail($id);
        $element->delete();

        $type = 3;
        $title = 'Bien';
        $msg = 'Grupo eliminado exitosamente.';


        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
        ]);
    }

    public function DisallowGroup($id)
    {
        $element = Group::findOrFail($id);
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

    public function AllowGroup($id)
    {
        $element = Group::findOrFail($id);
        $element->state = 1;
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
}
