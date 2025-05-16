<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Campain;
use App\Models\Block;
use App\Models\TypeField;
use App\Models\Width;
use App\Models\Field;
use App\Models\GroupFieldEdit;
use App\Models\GroupFieldView;
use App\Models\GroupFieldHaveComment;
use App\Models\TabStateField;
use App\Models\User;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\TabState;
use App\Models\State;
use App\Models\ModuleInGroup;
use App\Models\Module;
use App\Models\SectionInGroup;
use App\Models\Section;
use App\Models\SubSectionInGroup;
use App\Models\SubSection;
use App\Models\BackOffice;
use App\Models\SupInBack;
use App\Models\Sup;
use App\Models\AgentInSup;
use App\Models\Agent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupsController extends Controller
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
        $id = 0;
        $campaigns = Campain::leftjoin('user_groups', 'user_groups.id', '=', 'campains.user_group_id')
            ->select(
                'campains.id as id',
                'campains.name as name',
            )
            ->where('user_groups.group_id', 14)
            ->where('user_groups.user_id', Auth::user()->id)
            ->get();

        $sups = [];
        $agentsInSups = [];
        $agents = [];
        $users = [];

        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('sups', compact('id', 'campaigns', 'modules', 'user', 'company', 'sups', 'agentsInSups', 'agents', 'users'));
    }

    public function indexWithId($id)
    {
        $modules = $this->modules();
        $campaigns = Campain::leftjoin('user_groups', 'user_groups.id', '=', 'campains.user_group_id')
            ->select(
                'campains.id as id',
                'campains.name as name',
            )
            ->where('user_groups.group_id', 14)
            ->where('user_groups.user_id', Auth::user()->id)
            ->get();

        $sups = Sup::leftjoin('users', 'users.id', '=', 'sups.user_id')
            ->leftjoin('campains', 'campains.id', '=', 'sups.camp_id')
            ->select(
                'sups.id as id',
                'sups.user_id as user_id',
                'sups.camp_id as camp_id',
                'sups.state as state',
                'campains.name as camp_name',
                'users.name as user_name',
                'users.email as user_email',
            )
            ->where('camp_id', $id)
            ->get();
        $agentsInSups = AgentInSup::get();
        $agents = Agent::leftjoin('users', 'users.id', '=', 'agents.user_id')
            ->leftjoin('campains', 'campains.id', '=', 'agents.camp_id')
            ->select(
                'agents.id as id',
                'agents.user_id as user_id',
                'agents.camp_id as camp_id',
                'agents.state as state',
                'campains.name as camp_name',
                'users.name as user_name',
                'users.email as user_email',
            )
            ->where('camp_id', $id)
            ->get();
            $userId = Auth::user()->id;
            $users = User::where('user_id',$userId)->get();

        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('sups', compact('id', 'campaigns', 'modules', 'user', 'company', 'sups', 'agentsInSups', 'agents', 'users'));
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

    public function validateModalForm()
    {

        $messages = [
            'user_id.required'         => 'Debe completar el usuario.',
            'campaign_id.required'         => 'Debe completar la campaña.',
        ];

        $rules = [
            'user_id'                  => 'required',
            'campaign_id'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function SaveSup()
    {

        $this->validateModalForm();

        $id = request('id');
        $user_id = request('user_id');
        $camp_id = request('campaign_id');
        $agents = request('agents');

        if (isset($id)) {
            $sup =  Sup::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $sup->updated_at_user = Auth::user()->name;
        } else {
            $sup = new Sup();
            $sup->user_id = $user_id;
            $sup->camp_id = $camp_id;
            $sup->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $sup->save();

        if (isset($id)) {
            AgentInSup::where('sup_id', $id)->delete();
        }

        $agentsData = [];
        if ($agents) {
            foreach ($agents as $agentId) {
                $agentsData[] = [
                    'sup_id' => $sup->id,
                    'agent_id' => $agentId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        AgentInSup::insert($agentsData);

        $modules = $this->modules();
        $campaigns = Campain::get();

        $sups = Sup::leftjoin('users', 'users.id', '=', 'sups.user_id')
            ->leftjoin('campains', 'campains.id', '=', 'sups.camp_id')
            ->select(
                'sups.id as id',
                'sups.user_id as user_id',
                'sups.camp_id as camp_id',
                'sups.state as state',
                'campains.name as camp_name',
                'users.name as user_name',
                'users.email as user_email',
            )
            ->where('camp_id', $id)
            ->get();
        $agentsInSups = AgentInSup::get();
        $agents = Agent::leftjoin('users', 'users.id', '=', 'agents.user_id')
            ->leftjoin('campains', 'campains.id', '=', 'agents.camp_id')
            ->select(
                'agents.id as id',
                'agents.user_id as user_id',
                'agents.camp_id as camp_id',
                'agents.state as state',
                'campains.name as camp_name',
                'users.name as user_name',
                'users.email as user_email',
            )
            ->where('camp_id', $id)
            ->get();
        $users = User::get();

        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return redirect()->back()->with([
            'id' => $camp_id,
            'campaigns' => $campaigns,
            'modules' => $modules,
            'user' => $user,
            'company' => $company,
            'sups' => $sups,
            'agentsInSups' => $agentsInSups,
            'agents' => $agents,
            'users' => $users
        ]);
    }

    public function DeleteSup($id)
    {
        $element = Sup::findOrFail($id);
        $element->delete();

        AgentInBack::where('sup_id', $id)->delete();

        $msg = 'Registro eliminado exitosamente';
        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function DisallowSup($id)
    {
        $element = Sup::findOrFail($id);
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

    public function AllowSup($id)
    {
        $element = Sup::findOrFail($id);
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
