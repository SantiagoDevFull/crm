<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\GroupAdvertisement;
use App\Models\Advertisement;
use App\Models\Logins;
use App\Models\File;
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

class EnterpriseController extends Controller
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


        $companyQuery = Company::leftJoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
            ->select(
                'companies.id as id',
                'companies.name as name',
                'companies.short_name as short_name',
                'companies.document as document',
                'companies.pais as pais',
                'companies.contact as contact',
                'companies.asist_type as asist_type',
                'companies.sufijo as sufijo',
                'companies.logo as logo',
            );

        if ($user->id == 44) {
            $companyQuery->where('companies.id', 1);
        } else {
            $companyQuery->where('user_groups.group_id', 14)
                ->where('user_groups.user_id', $user->id);
        }

        $company = $companyQuery->first();

        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);

        

        return view('enterprise', compact('company', 'campaigns', 'modules', 'user'));
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
                $sectionSubSections = $subSections->where('section_id', $section->id)->sortBy('order');

                $section->subSections = $sectionSubSections->values();

                return $section;
            });

            $module->sections = $moduleSections->values();

            return $module;
        });

        $modules = $result;

        return $modules;
    }

    public function DeleteCompany($id)
    {

        $element = Company::where('id', $id)->first();

        $user_group = UserGroup::where('id', $element->user_group_id)->first();
        if ($user_group) {
            $user_group->state_admin = 0;
            $user_group->save();
        }

        $element->delete();

        $file_element = File::where('name', $element->logo)->first();
        if ($file_element) {
            $file_element->state = 0;
            $file_element->save();
            $file_element->delete();
        }

        $type = 3;
        $title = 'Bien';
        $msg = 'Compañía eliminado exitosamente.';

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg
        ]);
    }

    public function DeleteAdmin($id)
    {

        $element = Company::where('id', $id)->first();

        $user_group = UserGroup::where('id', $element->user_group_id)->first();
        $user_group->state_admin = 0;
        $user_group->save();

        $user = User::where('id', $user_group->user_id)->first();
        $parte = explode('@', $user->email)[0];
        $new_email = $parte . '@xxxxxxxxx.com';
        $user->email = $new_email;
        $user->save();

        $element->user_group_id = null;
        $element->save();

        $type = 3;
        $title = 'Bien';
        $msg = 'Administrador eliminado exitosamente.';

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg
        ]);
    }

    public function SaveCompany(Request $request)
    {
        $id = request('id');
        $name = request('name');
        $short_name = request('short_name');
        $document = request('document');
        $pais = request('pais');
        $contact = request('contact');
        $asist_type = request('asist_type');
        $sufijo = request('sufijo');
        $menu_color = request('menu_color');
        $text_color = request('text_color');
        $user_group_id = request('user_group_id');
        $logo = request('logo');



        if (isset($id)) {
            $company = Company::findOrFail($id);
            $file_element = File::where('name', $company->logo)->first();
            if ($file_element) {
                $file_element->state = 0;
                $file_element->save();
                $file_element->delete();
            }
        } else {
            $company = new Company();
        }

        $user_group = UserGroup::where('id', $user_group_id)->first();

        if ($user_group) {
            $user_group->state_admin = 1;
            $user_group->save();
            $company->user_group_id = $user_group->id;

            $user = User::where('id', $user_group->user_id)->first();
            $parte = explode('@', $user->email)[0];
            $new_email = $parte . $sufijo;
            $user->email = $new_email;
            $user->save();
        }


        $originalFullName = $logo->getClientOriginalName();

        $folder = 'public/uploads';
        $path = $logo->storeAs($folder, $originalFullName);

        $company->name = $name;
        $company->short_name = $short_name;
        $company->document = $document;
        $company->pais = $pais;
        $company->contact = $contact;
        $company->asist_type = $asist_type;
        $company->sufijo = $sufijo;
        $company->logo = $originalFullName;
        $company->menu_color = $menu_color;
        $company->text_color = $text_color;
        $company->created_at_user = Auth::user()->name;
        $company->save();

        if (!isset($id)) {
            $obj = new File();
            $obj->name = $originalFullName;
            $obj->path = $path;
            $obj->state = 1;
            $obj->created_at_user = Auth::user()->name;
            $obj->save();
        }

        return redirect('/enterprise/companies');
    }
}
