<?php

namespace App\Http\Controllers;

use App\Models\Campain;
use App\Models\Company;
use App\Models\State;
use App\Models\TabState;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Group;
use App\Models\StateState;
use App\Models\ModuleInGroup;
use App\Models\Module;
use App\Models\SectionInGroup;
use App\Models\Section;
use App\Models\SubSectionInGroup;
use App\Models\SubSection;
use App\Models\Form;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StatesController extends Controller
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
                'campains.name as name'
            )
            ->where('user_groups.group_id', 14)
            ->where('user_groups.user_id', Auth::user()->id)
            ->get();
        $tabStates = [];
        $states = [];
        $stateStates = [];
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('states', compact('id', 'campaigns', 'tabStates', 'states', 'stateStates', 'modules', 'user', 'company'));
    }

    public function indexWithId($id)
    {
        $modules = $this->modules();
        $campaigns = Campain::get();
        $tabStates = TabState::leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->select(
                'tab_states.id as id',
                'campains.id as campaign_id',
                'campains.name as campaign_name',
                'tab_states.name as name',
                'tab_states.order as order',
                'tab_states.state as state',
            )
            ->where('campain_id', $id)
            ->orderBy('tab_states.order', 'asc')
            ->get();
        $states = State::leftjoin('tab_states', 'tab_states.id', '=', 'states.tab_state_id')
            ->leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->select(
                'states.id as id',
                'campains.id as campaign_id',
                'campains.name as campaign_name',
                'tab_states.name as tab_state_name',
                'states.tab_state_id as tab_state_id',
                'states.name as name',
                'states.color as color',
                'states.order as order',
                'states.state as state',
                'states.not as not',
                'states.age as age',
                'states.com as com',
                'states.state_user as state_user',
            )
            ->where('tab_states.campain_id', $id)
            ->orderBy('states.order', 'asc')
            ->get();
        $stateStates = StateState::get();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('states', compact('id', 'campaigns', 'tabStates', 'states', 'stateStates', 'modules', 'user', 'company'));
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
            $subSections = SubSection::whereIn('id', $subSectionsIds)
            ->where(function ($query) {
                $query->where('section_id', '!=', 5)
                    ->orWhere(function ($q) {
                        $q->where('section_id', 5)
                            ->where('created_at_user', Auth::user()->name);
                    });
            })
            ->get();

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

    public function SaveState()
    {
        $this->validateModalForm();

        $id = request('id');
        $campaign_id = request('campaign_id');
        $name = request('name');
        $tab_state_id = request('tab_state_id');
        $color = request('color');
        $order = request('order');
        $state = 1; //activo
        $state_user = request('state_user');
        $not = request('not') ? request('not') : 'off';
        $age = request('age') ? request('age') : 'off';
        $com = request('com') ? request('com') : 'off';
        $state_state = request('states');

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

        if ($state_user && $state_user !== "null") {
            $element->state_user = $state_user;
        } else {
            $element->state_user = 0;
        }

        $element->not = $not;
        $element->age = $age;
        $element->com = $com;

        $element->save();

        if (isset($id)) {
            
            StateState::where('from_state_id', $id)->delete();
        }

        $stateStateData = [];
        if ($state_state) {
            foreach ($state_state as $stateId) {
                $stateStateData[] = [
                    'from_state_id' => $element->id,
                    'to_state_id' => $stateId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        StateState::insert($stateStateData);

        $campaigns = Campain::get();
        $tabStates = TabState::leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->select(
                'tab_states.id as id',
                'campains.id as campaign_id',
                'campains.name as campaign_name',
                'tab_states.name as name',
                'tab_states.order as order',
                'tab_states.state as state',
            )
            ->where('campain_id', $campaign_id)
            ->orderBy('tab_states.order', 'asc')
            ->get();
        $states = State::leftjoin('tab_states', 'tab_states.id', '=', 'states.tab_state_id')
            ->leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->select(
                'states.id as id',
                'campains.id as campaign_id',
                'campains.name as campaign_name',
                'tab_states.name as tab_state_name',
                'states.tab_state_id as tab_state_id',
                'states.name as name',
                'states.color as color',
                'states.order as order',
                'states.state as state',
                'states.not as not',
                'states.age as age',
                'states.com as com',
                'states.state_user as state_user',
            )
            ->where('tab_states.campain_id', $campaign_id)
            ->orderBy('states.order', 'asc')
            ->get();
        $stateStates = StateState::get();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);
        $modules = $this->modules();

        return redirect()->back()->with([
            'id' => $campaign_id,
            'campaigns' => $campaigns,
            'tabStates' => $tabStates,
            'states' => $states,
            'stateStates' => $stateStates,
            'user' => $user,
            'company' => $company,
            'modules' => $modules,
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

    public function DeleteState($id)
    {
        $forms = Form::where('state_id', $id)->get();

        if (count($forms)) {
            return response()->json([
                'error' => 'El estado tiene ventas asociadas.',
            ], 400);
        }

        $element = State::findOrFail($id);
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

    public function DisallowState($id)
    {
        $element = State::findOrFail($id);
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

    public function AllowState($id)
    {
        $element = State::findOrFail($id);
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
