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
use App\Models\File;
use App\Models\Campain;
use App\Models\Country;
use App\Models\RangeDate;
use App\Models\TabState;
use App\Models\ModuleInGroup;
use App\Models\Module;
use App\Models\SectionInGroup;
use App\Models\Section;
use App\Models\SubSectionInGroup;
use App\Models\SubSection;
use App\Models\State;
use App\Models\Form;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TabStatesController extends Controller
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
        $id = NULL;
        $campaigns = Campain::leftjoin('user_groups', 'user_groups.id', '=', 'campains.user_group_id')
        ->select(
            'campains.id as id',
            'campains.name as name',
        )
            ->where('user_groups.group_id', 14)
            ->where('user_groups.user_id', Auth::user()->id)
            ->get();
        $tabStates = [];
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('tab-states', compact('id', 'campaigns', 'tabStates', 'modules', 'user', 'company'));
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
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('tab-states', compact('id', 'campaigns', 'tabStates', 'modules', 'user', 'company'));
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
        $subSections = SubSection::whereIn('id', $subSectionsIds)
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

        $tab_states = TabState::leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->select(
                'tab_states.id as id',
                'campains.name as campain_name',
                'tab_states.name as name',
                'tab_states.order as order',
                'tab_states.state as state',
            )
            ->where('campain_id', $campain_id)
            ->orderBy('tab_states.order', 'asc')
            ->get();

        return $tab_states;
    }

    public function validateModalForm()
    {

        $messages = [
            'name.required'         => 'Debe completar el nombre.',
            'order.required'         => 'Debe completar el Orden.',
        ];

        $rules = [
            'name'                  => 'required',
            'order'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function SaveTabState()
    {

        $this->validateModalForm();

        $id = request('id');
        $campain_id = request('campaign_id');
        $name = request('name');
        $order = request('order');
        $state = 1; //activo

        if (isset($id)) {
            $tab_state =  TabState::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $tab_state->updated_at_user = Auth::user()->name;
        } else {
            $tab_state = new TabState();
            $tab_state->campain_id = $campain_id;
            $tab_state->state = $state;
            $tab_state->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $tab_state->name = $name;
        $tab_state->order = $order;

        $tab_state->save();

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
            ->where('campain_id', $campain_id)
            ->orderBy('tab_states.order', 'asc')
            ->get();

        $modules = $this->modules();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return redirect()->back()->with([
            'id' => $campain_id,
            'campaigns' => $campaigns,
            'tabStates' => $tabStates,
            'modules' => $modules,
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function getTabState()
    {
        $id = request('id');
        $elements = TabState::findOrFail($id);
        return $elements;
    }

    public function DeleteTabState($id)
    {
        $states = State::where('tab_state_id', $id)->get();
        $forms = Form::where('tab_state_id', $id)->get();

        if (count($states) || count($forms)) {
            return response()->json([
                'error' => 'La pestaña de estado tiene estados y/o ventas asociadas.',
            ], 400);
        }

        $element = TabState::findOrFail(request('id'));
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

    public function DisallowTabState($id)
    {
        $element = TabState::findOrFail($id);
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

    public function AllowTabState($id)
    {
        $element = TabState::findOrFail($id);
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
