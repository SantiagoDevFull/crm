<?php

namespace App\Http\Controllers;

use App\Exports\EmptyExport;
use App\Exports\SoldExport;
use App\Models\Agent;
use App\Models\AgentInSup;
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
use App\Models\Form;
use App\Models\File;
use App\Models\GroupCampainExportSold;
use App\Models\ModuleInGroup;
use App\Models\Module;
use App\Models\SectionInGroup;
use App\Models\Section;
use App\Models\StateGroup;
use App\Models\StateState;
use App\Models\SubSectionInGroup;
use App\Models\SubSection;
use App\Models\Sup;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SoldsController extends Controller
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
    public function index($id)
    {
        $modules = $this->modules();
        // $campaigns = Campain::get();
        $campaigns = Campain::whereNull('deleted_at')->get();
        $campaign = Campain::findOrFail($id);
        $tab_states = TabState::where('campain_id', $id)
            ->orderBy('tab_states.order', 'asc')
            ->get();

        $tab_state_id = 0;
        $tab_state_ids = [];

        if (!$tab_states->isEmpty()) {
            $tab_state_id = $tab_states[0]->id;
        };
        for ($i = 0; $i < count($tab_states); $i++) {
            $ts = $tab_states[$i];

            array_push($tab_state_ids, $ts->id);
        };

        $states = State::whereIn('tab_state_id', $tab_state_ids)
            ->orderBy('states.order', 'asc')
            ->get();

        $tab_states_fields = TabStateField::get();
        $field_ids = [];
        $fields_parse = [];
        $fields_obj = [];
        foreach ($tab_states_fields as $tab_state_field) {
            $field_ids[] = $tab_state_field->field_id;
            $fields_parse[$tab_state_field->tab_state_id][] = $tab_state_field->field_id;
            $fields_obj[$tab_state_field->tab_state_id] = [];
        };

        $userId = Auth::user()->id;

        $isSup = Sup::where('user_id', $userId)->where('camp_id', $id)->first();

        if ($isSup) {

            $agents = AgentInSup::leftjoin('agents', 'agents.id', '=', 'agents_in_sups.agent_id')
                ->leftjoin('users', 'users.id', '=', 'agents.user_id')
                ->select(
                    'agents_in_sups.id as id',
                    'users.name as user_name',
                )
                ->where('agents_in_sups.sup_id', $isSup->id)
                ->where('agents.camp_id', $id)
                ->get();

            foreach ($agents as $agent) {
                echo $agent . "<br>";
            }

            $userNames = $agents->pluck('user_name')->toArray();
            array_push($userNames,  Auth::user()->name);




            $forms = Form::where('forms.campain_id', $id)
                ->leftjoin('campains', 'campains.id', '=', 'forms.campain_id')
                ->leftjoin('states', 'states.id', '=', 'forms.state_id')
                ->select(
                    'forms.id as id',
                    'campains.name as campain_name',
                    'states.name as state_name',
                    'forms.campain_id as campain_id',
                    'forms.tab_state_id as tab_state_id',
                    'forms.state_id as state_id',
                    'forms.data as data',
                    'forms.created_at_user as created_at_user',
                    'forms.created_at as created_at',
                    'forms.updated_at as updated_at',
                    'forms.state as state',
                )
                ->orderBy('forms.id', 'desc')
                ->whereIn('forms.created_at_user', $userNames)
                ->get();
        } else {

            $isAdmin = UserGroup::where('user_id', $userId)->where('group_id', 14)->first();

            if ($isAdmin) {
                $forms = Form::where('forms.campain_id', $id)
                    ->leftjoin('campains', 'campains.id', '=', 'forms.campain_id')
                    ->leftjoin('states', 'states.id', '=', 'forms.state_id')
                    ->select(
                        'forms.id as id',
                        'campains.name as campain_name',
                        'states.name as state_name',
                        'forms.campain_id as campain_id',
                        'forms.tab_state_id as tab_state_id',
                        'forms.state_id as state_id',
                        'forms.data as data',
                        'forms.created_at_user as created_at_user',
                        'forms.created_at as created_at',
                        'forms.updated_at as updated_at',
                        'forms.state as state',
                    )
                    ->orderBy('forms.id', 'desc')
                    ->get();
            } else {
                $forms = Form::where('forms.campain_id', $id)
                    ->leftjoin('campains', 'campains.id', '=', 'forms.campain_id')
                    ->leftjoin('states', 'states.id', '=', 'forms.state_id')
                    ->select(
                        'forms.id as id',
                        'campains.name as campain_name',
                        'states.name as state_name',
                        'forms.campain_id as campain_id',
                        'forms.tab_state_id as tab_state_id',
                        'forms.state_id as state_id',
                        'forms.data as data',
                        'forms.created_at_user as created_at_user',
                        'forms.created_at as created_at',
                        'forms.updated_at as updated_at',
                        'forms.state as state',
                    )
                    ->orderBy('forms.id', 'desc')
                    ->where('forms.created_at_user', Auth::user()->name)
                    ->get();
            }
        }

        $forms = $forms->groupBy('tab_state_id');
        $forms = $forms->toArray();

        $fields = Field::where('fields.campain_id', $id)
            ->whereIn('fields.id', $field_ids)
            ->where('fields.in_solds_list', 1)
            ->select(
                'fields.id as id',
                'fields.name as name',
                'fields.block_id as block_id',
                'fields.type_field_id as type_field_id',
            )
            ->orderBy('fields.order', 'asc')
            ->get();

        foreach ($fields_parse as $fp => $v) {
            $filteredFields = $fields->filter(function ($field) use ($v) {
                return in_array($field->id, $v);
            });
            $fields_obj[$fp] = $filteredFields;
        };

        $fields = $fields_obj;

        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);
        $user_groups = UserGroup::where('user_id', $userId)->get();
        $groupIds = $user_groups->pluck('group_id')->toArray();

        $groups = GroupCampainExportSold::whereIn('group_id', $groupIds)
            ->where('campain_id', $id)
            ->get();

        $cont = $groups->count();


        return view('solds', compact(
            'id',
            'tab_state_id',
            'campaigns',
            'campaign',
            'tab_states',
            'tab_states_fields',
            'forms',
            'fields',
            'modules',
            'user',
            'company',
            'states',
            'user_groups',
            'cont'
        ));
    }

    public function export($id, $tab_state_id)
    {

        $solds = Form::leftjoin('tab_states', 'tab_states.id', '=', 'forms.tab_state_id')
            ->leftjoin('states', 'states.id', '=', 'forms.state_id')
            ->leftjoin('campains', 'campains.id', '=', 'forms.campain_id')
            ->select(
                'campains.name as campain_name',
                'tab_states.name as tab_state_name',
                'states.name as state_name',
                DB::raw('DATE_FORMAT(forms.created_at, "%Y-%m-%d") as form_created_at'),
                'forms.created_at_user as created_at_user',
            )
            ->where('forms.campain_id', $id)
            ->where('forms.tab_state_id', $tab_state_id)
            ->get();

        $datetime = CarbonImmutable::now('America/Lima')->format('Y-m-d h:i:s a');
        $excel = new SoldExport($solds, $datetime);
        return Excel::download($excel, 'reporte-ventas.xlsx');
    }

    public function indexWithTabStateId($id, $tab_state_id)
    {
        $modules = $this->modules();
        $form_id = 0;
        $campaigns = Campain::whereNull('deleted_at')->get();
        $campaign = Campain::findOrFail($id);

        $tab_states_fields = TabStateField::where('tab_state_id', $tab_state_id)
            ->get();
        $field_ids = [];
        foreach ($tab_states_fields as $tab_state_field) {
            $field_ids[] = $tab_state_field->field_id;
        }

        $blocks = Block::where('campain_id', $id)
            ->orderBy('blocks.order', 'asc')
            ->get();


        $state = State::where('tab_state_id', $tab_state_id)
            ->orderBy('states.order', 'asc')
            ->get();

        $stateIds = $state->pluck('id')->toArray();

        $userId = Auth::user()->id;
        $grupos_users = UserGroup::leftjoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->select(
                'groups.name as group_name',
                'user_groups.group_id as group_id',
                'user_groups.user_id as user_id'
            )
            ->where('user_id', Auth::user()->id)
            ->get();
        $gruposIds = $grupos_users->pluck('group_id')->toArray();

        $state_groups = StateGroup::whereIn('group_id', $gruposIds)->get();
        $newStatesIds = $state_groups->pluck('state_id')->toArray();

        $isAdmin = UserGroup::where('user_id', $userId)->where('group_id', 14)->first();


        /*
        if ($isAdmin) {

            Antiguo
            $states = StateState::leftJoin('states', 'states.id', '=', 'states_states.to_state_id')
            ->leftJoin('tab_states', 'tab_states.id', '=', 'states.tab_state_id')
            ->leftJoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->select(
                'states_states.id as id',
                'states.name as name'
            )
            ->where('tab_states.campain_id', $id)
            ->orderBy('states.order', 'asc')
            ->get();
            
            Nuevo
            $states = State::leftjoin('tab_states', 'tab_states.id', '=', 'states.tab_state_id')
            ->leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->leftjoin('states_states', 'states_states.from_state_id', '=', 'states.id')
            ->select(
                'states_states.from_state_id as id',
                'states.name as name',
            )
            ->where('tab_states.campain_id', $id)
            ->groupBy('states_states.from_state_id', 'states.name')
            ->orderBy('states.order', 'asc')
            ->get();
                
        } else {
            $states = StateState::leftjoin('states', 'states.id', '=', 'states_states.from_state_id')
                ->select(
                    'states_states.from_state_id as id',
                    'states.name as name'
                )
                ->whereIn('from_state_id', $newStatesIds)
                //->whereIn('states_states.from_state_id', $newStatesIds)
                ->get();
        }
      */

        $states = State::leftjoin('tab_states', 'tab_states.id', '=', 'states.tab_state_id')
            ->leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->leftjoin('states_states', 'states_states.from_state_id', '=', 'states.id')
            ->select(
                'states_states.from_state_id as id',
                'states.name as name',
            )
            ->where('tab_states.campain_id', $id)
            ->groupBy('states_states.from_state_id', 'states.name')
            ->orderBy('states.order', 'asc')
            ->get();


        $grupos = UserGroup::where('user_id', $userId)->pluck('group_id');

        $fields = Field::where('fields.campain_id', $id)
            ->whereIn('fields.id', $field_ids)
            ->leftJoin('blocks', 'blocks.id', '=', 'fields.block_id')
            ->leftJoin('widths', 'widths.id', '=', 'fields.width_id')
            ->leftJoin('groups_fields_edit', function ($join) use ($grupos) {
                $join->on('groups_fields_edit.field_id', '=', 'fields.id')
                    ->whereIn('groups_fields_edit.group_id', $grupos)
                    ->whereNull('groups_fields_edit.deleted_at');
            })
            ->leftJoin('groups_fields_view', function ($join) use ($grupos) {
                $join->on('groups_fields_view.field_id', '=', 'fields.id')
                    ->whereIn('groups_fields_view.group_id', $grupos)
                    ->whereNull('groups_fields_view.deleted_at');
            })
            ->select(
                'fields.id',
                'fields.name',
                'fields.block_id',
                'fields.type_field_id',
                'fields.options',
                'widths.col as width_col',
                'fields.range as range',
                'fields.unique',
                DB::raw("CASE WHEN groups_fields_edit.group_id IN (" . $grupos->implode(',') . ") THEN 1 ELSE 1 END as can_edit"),
                DB::raw("CASE WHEN groups_fields_view.group_id IN (" . $grupos->implode(',') . ") THEN 1 ELSE 1 END as can_view")
            )
            ->orderBy('fields.order', 'asc')
            ->get();
        $fields = $fields->groupBy('block_id');
        $fields = $fields->toArray();

        $form = NULL;

        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('forms', compact('id', 'form_id', 'campaigns', 'tab_state_id', 'campaign', 'fields', 'blocks', 'states', 'form', 'modules', 'user', 'company'));
    }

    public function indexWithFormId($id, $tab_state_id, $form_id)
    {
        $modules = $this->modules();
        $campaigns = Campain::whereNull('deleted_at')->get();
        $userId = Auth::user()->id;
        $campaign = Campain::findOrFail($id);

        $tab_states_fields = TabStateField::where('tab_state_id', $tab_state_id)
            ->get();
        $field_ids = [];
        foreach ($tab_states_fields as $tab_state_field) {
            $field_ids[] = $tab_state_field->field_id;
        }

        $blocks = Block::where('campain_id', $id)
            ->orderBy('blocks.order', 'asc')
            ->get();

        $state = State::where('tab_state_id', $tab_state_id)
            ->orderBy('states.order', 'asc')
            ->get();
        $stateIds = $state->pluck('id')->toArray();


        $grupos_users = UserGroup::leftjoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->select(
                'groups.name as group_name',
                'user_groups.group_id as group_id',
                'user_groups.user_id as user_id'
            )
            ->where('user_id', Auth::user()->id)
            ->get();
        $gruposIds = $grupos_users->pluck('group_id')->toArray();
        $state_groups = StateGroup::whereIn('group_id', $gruposIds)->get();
        $newStatesIds = $state_groups->pluck('state_id')->toArray();
        $isAdmin = UserGroup::where('user_id', $userId)->where('group_id', 14)->first();

        /*
        if ($isAdmin) {
            $states = StateState::leftjoin('states', 'states.id', '=', 'states_states.to_state_id')
                ->select(
                    'states_states.to_state_id as id',
                    'states.name as name'
                )
                ->whereIn('from_state_id', $stateIds)
                //->whereIn('states_states.from_state_id', $newStatesIds)
                ->get();
        } else {
            $states = StateState::leftjoin('states', 'states.id', '=', 'states_states.from_state_id')
                ->select(
                    'states_states.from_state_id as id',
                    'states.name as name'
                )
                ->whereIn('from_state_id', $newStatesIds)
                //->whereIn('states_states.from_state_id', $newStatesIds)
                ->get();
        }
                */

        $states = State::leftjoin('tab_states', 'tab_states.id', '=', 'states.tab_state_id')
            ->leftjoin('campains', 'campains.id', '=', 'tab_states.campain_id')
            ->leftjoin('states_states', 'states_states.from_state_id', '=', 'states.id')
            ->select(
                'states_states.from_state_id as id',
                'states.name as name',
            )
            ->where('tab_states.campain_id', $id)
            ->groupBy('states_states.from_state_id', 'states.name')
            ->orderBy('states.order', 'asc')
            ->get();

        $grupos = UserGroup::where('user_id', $userId)->pluck('group_id');

        $fields = Field::where('fields.campain_id', $id)
            ->whereIn('fields.id', $field_ids)
            ->leftJoin('blocks', 'blocks.id', '=', 'fields.block_id')
            ->leftJoin('widths', 'widths.id', '=', 'fields.width_id')
            ->leftJoin('groups_fields_edit', function ($join) use ($grupos) {
                $join->on('groups_fields_edit.field_id', '=', 'fields.id')
                    ->whereIn('groups_fields_edit.group_id', $grupos)
                    ->whereNull('groups_fields_edit.deleted_at');
            })
            ->leftJoin('groups_fields_view', function ($join) use ($grupos) {
                $join->on('groups_fields_view.field_id', '=', 'fields.id')
                    ->whereIn('groups_fields_view.group_id', $grupos)
                    ->whereNull('groups_fields_view.deleted_at');
            })
            ->select(
                'fields.id',
                'fields.name',
                'fields.block_id',
                'fields.type_field_id',
                'fields.options',
                'widths.col as width_col',
                'fields.unique as unique',
                DB::raw("CASE WHEN groups_fields_edit.group_id IN (" . $grupos->implode(',') . ") THEN 1 ELSE 0 END as can_edit"),
                DB::raw("CASE WHEN groups_fields_view.group_id IN (" . $grupos->implode(',') . ") THEN 1 ELSE 0 END as can_view")
            )
            ->orderBy('fields.order', 'asc')
            ->get();


        $fields = $fields->groupBy('block_id');
        $fields = $fields->toArray();

        $form = Form::where('forms.id', $form_id)
            ->leftjoin('campains', 'campains.id', '=', 'forms.campain_id')
            ->leftjoin('states', 'states.id', '=', 'forms.state_id')
            ->select(
                'forms.id as id',
                'campains.name as campain_name',
                'states.name as state_name',
                'forms.campain_id as campain_id',
                'forms.tab_state_id as tab_state_id',
                'forms.state_id as state_id',
                'forms.data as data',
                'forms.created_at_user as created_at_user',
                'forms.created_at as created_at',
                'forms.updated_at as updated_at',
                'forms.state as state',
            )
            ->first();
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);


        return view('forms', compact('id', 'form_id', 'campaigns', 'tab_state_id', 'campaign', 'fields', 'blocks', 'states', 'form', 'modules', 'user', 'company'));
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

    public function SaveSold()
    {
        $id = request('id');
        $campain_id = request('campaign_id');
        $tab_state_id = request('tab_state_id');
        $state_id = request('state_id');
        $data = request()->all();
        $state = 1;

        unset($data['_token'], $data['id'], $data['campaign_id'], $data['tab_state_id'], $data['state_id']);

        $tab_state_id = State::findOrFail($state_id);

        foreach ($data as $block_id => $fields) {
            foreach ($fields as $field_id => $value) {
                $field = Field::find($field_id);

                if ($field && $field->unique == 1) {

                    $exists = Form::where('campain_id', $campain_id)
                        ->where(function ($q) use ($field_id, $value) {
                            $q->whereRaw("JSON_EXTRACT(data, '$.\"$field_id\"') = ?", [$value])
                                ->orWhereRaw("data LIKE ?", ['%"' . $field_id . '":"' . $value . '"%']);
                        })
                        ->exists();

                    if ($exists) {
                        return back()->withErrors([
                            "field_$field_id" => "El valor '{$value}' ya existe en esta campaña para el campo '{$field->name}'."
                        ])->withInput();
                    }
                }
            }
        }

        foreach ($data as $key => $val) {
            foreach ($val as $k => $v) {
                if ($v instanceof UploadedFile) {
                    $file = $v;
                    $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $fileExtension = strtolower($file->getClientOriginalExtension());
                    $uniqueFileName = $fileName . '_' . time() . '.' . $fileExtension;

                    $path = $file->storeAs('public/uploads', $uniqueFileName);

                    $file = new File();
                    $file->name = $uniqueFileName;
                    $file->path = $path;
                    $file->created_at_user = Auth::user()->name;
                    $file->save();

                    $data[$key][$k] = $file->id;
                }
            }
        }

        $jsonData = json_encode($data);

        // 🔹 Guardar o actualizar
        if (!empty($id)) {
            $form = Form::findOrFail($id);
            $form->updated_at_user = Auth::user()->name;
        } else {
            $form = new Form();
            $form->campain_id = $campain_id;
            $form->state = $state;
            $form->created_at_user = Auth::user()->name;
        }

        $form->tab_state_id = $tab_state_id->tab_state_id;
        $form->state_id = $state_id;
        $form->data = $jsonData;
        $form->save();

        // 🔹 Redirección (igual que tu código original)
        $campaigns = Campain::whereNull('deleted_at')->get();
        $campaign = Campain::findOrFail($campain_id);
        $tab_states = TabState::where('campain_id', $campain_id)
            ->orderBy('tab_states.order', 'asc')
            ->get();

        $tab_state_id = $tab_states->first()->id ?? 0;
        $tab_state_ids = $tab_states->pluck('id')->toArray();

        $states = State::whereIn('tab_state_id', $tab_state_ids)
            ->orderBy('states.order', 'asc')
            ->get();

        $tab_states_fields = TabStateField::get();
        $field_ids = $tab_states_fields->pluck('field_id');

        $forms = Form::where('forms.campain_id', $campain_id)
            ->leftJoin('campains', 'campains.id', '=', 'forms.campain_id')
            ->leftJoin('states', 'states.id', '=', 'forms.state_id')
            ->select(
                'forms.id',
                'campains.name as campain_name',
                'states.name as state_name',
                'forms.campain_id',
                'forms.tab_state_id',
                'forms.data',
                'forms.created_at_user',
                'forms.created_at',
                'forms.updated_at',
                'forms.state'
            )
            ->orderByDesc('forms.id')
            ->get()
            ->groupBy('tab_state_id')
            ->toArray();

        $fields = Field::where('fields.campain_id', $campain_id)
            ->whereIn('fields.id', $field_ids)
            ->where('fields.in_solds_list', 1)
            ->select('fields.id', 'fields.name', 'fields.block_id', 'fields.type_field_id')
            ->orderBy('fields.order', 'asc')
            ->get();

        $url = '/sales/solds/' . $campain_id;
        $user = Auth::user();
        $company = Company::findOrFail(1);
        $modules = $this->modules();

        return redirect()->intended($url)->with([
            'id' => $campain_id,
            'tab_state_id' => $tab_state_id,
            'campaigns' => $campaigns,
            'campaign' => $campaign,
            'tab_states' => $tab_states,
            'tab_states_fields' => $tab_states_fields,
            'forms' => $forms,
            'fields' => $fields,
            'user' => $user,
            'company' => $company,
            'modules' => $modules,
            'states' => $states,
        ]);
    }

    public function DeleteSold($id)
    {
        $element = Form::findOrFail($id);
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

    public function DisallowSold($id)
    {
        $element = Form::findOrFail($id);
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

    public function AllowSold($id)
    {
        $element = Form::findOrFail($id);
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
