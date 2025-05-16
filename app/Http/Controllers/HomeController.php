<?php

namespace App\Http\Controllers;

use App\Models\Campain;
use App\Models\Company;
use App\Models\ModuleInGroup;
use App\Models\Module;
use App\Models\SectionInGroup;
use App\Models\Section;
use App\Models\SubSectionInGroup;
use App\Models\SubSection;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\Logins;
use App\Models\Form;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
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
    public function index(Request $request)
    {
        if (view()->exists($request->path())) {
            return view($request->path());
        }
        return abort(404);
    }

    public function modules()
    {
        $userId = Auth::user()->id;
        $userGroup = UserGroup::where('user_id', $userId)
            ->first();

        $result = [];

        if ($userGroup) {

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
        }


        return $result;
    }

    public function root()
    {
        $modules = $this->modules();
        // $company = Company::findOrFail(1);

        $userLogued = Auth::user();
        $user = User::leftjoin('user_groups', 'user_groups.user_id', '=', 'users.id')
            ->leftjoin('companies', 'companies.user_group_id', '=', 'user_groups.id')
            ->select(
                'users.id as id',
                'users.name as name',
                'users.user_id as user_id'
            )
            ->where('user_groups.user_id', $userLogued->id)
            ->first();

        if ($userLogued->id == 44) {
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
                ->where('user_groups.user_id', $userLogued->id)
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

        return view('index', compact('modules', 'company', 'user'));
    }

    public function enterprise()
    {
        $company = Company::findOrFail(1);
        $logins = Logins::orderBy('created_at', 'desc')
            ->get();
        $modules = $this->modules();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);

        return view('enterprise-timeline', compact('modules', 'logins', 'user', 'company'));
    }

    public function sales()
    {
        $company = Company::findOrFail(1);
        $forms = Form::orderBy('created_at', 'desc')
            ->get();
        $modules = $this->modules();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);

        return view('sales-timeline', compact('modules', 'forms', 'user', 'company'));
    }

    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function FormSubmit(Request $request)
    {
        return view('form-repeater');
    }
}
