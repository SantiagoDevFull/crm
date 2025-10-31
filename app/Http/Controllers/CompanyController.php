<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\File;
use App\Models\Group;
use App\Models\Module;
use App\Models\ModuleInGroup;
use App\Models\Section;
use App\Models\SectionInGroup;
use App\Models\SubSection;
use App\Models\SubSectionInGroup;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        $modules = $this->modules();
        $companies = Company::leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
            ->leftjoin('users', 'users.id', '=', 'user_groups.user_id')
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
                'users.name as admin',
            )
            ->get();

        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);

        
        $userGroups = UserGroup::join('groups', 'user_groups.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'user_groups.user_id')
            ->select(
                'user_groups.id as user_group_id',
                'users.name as user_name',
                'groups.name as group_name',
            )
            ->where('user_groups.group_id', 14) // administradores
            ->where(function ($query) {
                $query->where('user_groups.state_admin', '!=', '1')
                    ->orWhereNull('user_groups.state_admin');
            })
            ->distinct()
            ->get();

        return view('company', compact('companies', 'user', 'modules', 'userGroups'));
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

    public function getColorsPallette()
    {
        $company = Company::findOrFail(1);

        return response()->json([
            'menu_color' => $company->menu_color,
            'text_color' => $company->text_color
        ]);
    }

    public function getLogo()
    {
        $company = Company::findOrFail(1);
        $file = null;

        if ($company->logo) {
            $file = File::findOrFail($company->logo);
        };

        return $file;
    }

    public function files(Request $request)
    {
        $path = storage_path('app/uploads/' . $request->route('fileName'));

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function validateForm()
    {
        $messages = [
            'name.required'            => 'Debe ingresar un Nombre.',
            'contact.required'         => 'Debe ingresar un Contacto.',
            'pais.required'            => 'Debe seleccionar un País.',
            'asist_type.required'      => 'Debe seleccionar el Tipo de asistencia.',
            'sufijo.required'          => 'Debe ingresar un Sufijo.'
        ];

        $rules = [
            'name'                     => 'required',
            'contact'                  => 'required',
            'pais'                     => 'required',
            'asist_type'               => 'required',
            'sufijo'                   => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function update()
    {

        $this->validateForm();

        $id = request('id');
        $name = request('name');
        $contact = request('contact');
        $pais = request('pais');
        $asist_type = request('asist_type');
        $sufijo = request('sufijo');
        $menu_color = request('menu_color');
        $text_color = request('text_color');
        $logo = request('logo');

        $element = Company::findOrFail($id);
        $element->name = $name;
        $element->contact = $contact;
        $element->pais = $pais;
        $element->asist_type = $asist_type;
        $element->sufijo = $sufijo;
        $element->menu_color = $menu_color;
        $element->text_color = $text_color;

        if ($logo == "null") {
            $element->logo = null;
        } else {
            $element->logo = $logo;
        };

        $element->save();

        $type = 3;
        $title = 'Bien';
        $msg = 'Los datos se guardaron con éxito.';
        $url = route('dashboard.company.index');


        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }
}
