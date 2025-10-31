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
use App\Models\Form;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FieldsController extends Controller
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
        $blocks = [];
        $type_fields = TypeField::get();
        $widths = Width::get();
        $groups = Group::get();
        $tab_states = [];
        $states = State::get();
        $fields = [];

        $groupFieldEdit = GroupFieldEdit::get();
        $groupFieldView = GroupFieldView::get();
        $groupFieldHaveComment = GroupFieldHaveComment::get();
        $tabStateField = TabStateField::get();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('fields', compact('id', 'campaigns', 'blocks', 'type_fields', 'widths', 'groups', 'tab_states', 'states', 'fields', 'groupFieldEdit', 'groupFieldView', 'groupFieldHaveComment', 'tabStateField', 'modules', 'user', 'company'));
    }

    public function indexWithId($id)
    {
        $modules = $this->modules();
        $campaigns = Campain::leftjoin('user_groups', 'user_groups.id', '=', 'campains.user_group_id')
        ->select(
            'campains.id as id',
            'campains.name as name'
        )
        ->where('user_groups.group_id', 14)
        ->where('user_groups.user_id', Auth::user()->id)
        ->get();
        $blocks = Block::where('campain_id', $id)->get();
        $type_fields = TypeField::get();
        $widths = Width::get();
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
                ->get();
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
                ->where('user_groups.user_id', $userId)
                ->where('user_groups.group_id', 14)
                ->get();
        }
        $tab_states = TabState::where('campain_id', $id)->get();
        $states = State::get();
        $fields = Field::leftjoin('campains', 'campains.id', '=', 'fields.campain_id')
            ->leftjoin('blocks', 'blocks.id', '=', 'fields.block_id')
            ->leftjoin('type_fields', 'type_fields.id', '=', 'fields.type_field_id')
            ->leftjoin('widths', 'widths.id', '=', 'fields.width_id')
            ->select(
                'fields.id as id',
                'campains.id as campaign_id',
                'campains.name as campaign_name',
                'blocks.id as block_id',
                'blocks.name as block_name',
                'type_fields.id as type_field_id',
                'type_fields.name as type_field_name',
                'widths.col as width_col',
                'fields.name as name',
                'fields.order as order',
                'fields.state as state',
                'fields.unique as unique',
                'fields.required as required',
                'fields.bloq_mayus as bloq_mayus',
                'fields.in_solds_list as in_solds_list',
                'fields.in_notifications as in_notifications',
                'fields.in_general_search as in_general_search',
                'fields.has_edit as has_edit',
                'fields.options as options',
                'fields.range as range'
            )
            ->where('fields.campain_id', $id)
            ->orderBy('fields.order', 'asc')
            ->get();

        $groupFieldEdit = GroupFieldEdit::get();
        $groupFieldView = GroupFieldView::get();
        $groupFieldHaveComment = GroupFieldHaveComment::get();
        $tabStateField = TabStateField::get();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('fields', compact('id', 'campaigns', 'blocks', 'type_fields', 'widths', 'groups', 'tab_states', 'states', 'fields', 'groupFieldEdit', 'groupFieldView', 'groupFieldHaveComment', 'tabStateField', 'modules', 'user', 'company'));
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
            'campain_id.required'         => 'Debe seleccionar una Campaña.',
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

        $blocks = Block::where('campain_id', $campain_id)->get();

        $type_fields = TypeField::get();

        $widths = Width::get();

        $groups = Group::get();

        $tab_states = TabState::where('campain_id', $campain_id)->get();

        $states = State::get();

        $fields = Field::leftjoin('campains', 'campains.id', '=', 'fields.campain_id')
            ->leftjoin('blocks', 'blocks.id', '=', 'fields.block_id')
            ->leftjoin('type_fields', 'type_fields.id', '=', 'fields.type_field_id')
            ->leftjoin('widths', 'widths.id', '=', 'fields.width_id')
            ->select(
                'fields.id as id',
                'campains.name as campain_name',
                'blocks.name as block_name',
                'type_fields.name as type_field_name',
                'widths.col as width_col',
                'fields.name as name',
                'fields.order as order',
                'fields.state as state',
                'fields.range as range',
            )
            ->where('fields.campain_id', $campain_id)
            ->orderBy('fields.order', 'asc')
            ->get();

        return [
            'fields' => $fields,
            'blocks' => $blocks,
            'type_fields' => $type_fields,
            'widths' => $widths,
            'groups' => $groups,
            'states' => $states,
            'tab_states' => $tab_states,
        ];
    }

    public function validateModalForm()
    {

        $messages = [
            'name.required'         => 'Debe completar el nombre.',
            'order.required'         => 'Debe completar el Orden.',
            'block_id.required'         => 'Debe elegir un Bloque de campos.',
            'type_field_id.required'         => 'Debe elegir un tipo de campo.',
            'width_id.required'         => 'Debe elegir un ancho para el campo.',
        ];

        $rules = [
            'name'                  => 'required',
            'order'                  => 'required',
            'block_id'                  => 'required',
            'type_field_id'                  => 'required',
            'width_id'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function SaveField()
    {

        $this->validateModalForm();

        $id = request('id');
        $campain_id = request('campaign_id');
        $block_id = request('block_id');
        $type_field_id = request('type_field_id');
        $width_id = request('width_id');
        $name = request('name');
        $options = request('options');
        $range = request('range');
        $order = request('order');
        $unique = request('unique');
        $required = request('required');
        Log::info($required);
        $bloq_mayus = request('bloq_mayus');
        $in_solds_list = request('in_solds_list');
        $in_notifications = request('in_notifications');
        $has_edit = request('has_edit');
        $in_general_search = request('in_general_search');
        $group_field_edit = request('edits');
        $group_field_view = request('views');
        $group_field_have_comment = request('comments');
        $tab_state_field = request('tab_states');
        $state = 1; //activo

        if (isset($id)) {
            $field =  Field::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $field->updated_at_user = Auth::user()->name;
        } else {
            $field = new Field();
            $field->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $field->campain_id = $campain_id;
        $field->block_id = $block_id;
        $field->type_field_id = $type_field_id;
        $field->width_id = $width_id;
        $field->state = $state;

        $field->name = $name;
        $field->options = $options;
        $field->range = $range;
        $field->order = $order;
        $field->unique = 0;
        if ($unique) $field->unique = 1;
        $field->required = 0;
        if ($required) $field->required = 1;
        $field->bloq_mayus = 0;
        if ($bloq_mayus) $field->bloq_mayus = 1;
        $field->in_solds_list = 0;
        if ($in_solds_list) $field->in_solds_list = 1;
        $field->in_notifications = 0;
        if ($in_notifications) $field->in_notifications = 1;
        $field->has_edit = 0;
        if ($has_edit) $field->has_edit = 1;
        $field->in_general_search = 0;
        if ($in_general_search) $field->in_general_search = 1;

        $field->save();

        if (isset($id)) {
            GroupFieldEdit::where('field_id', $id)->delete();
            GroupFieldView::where('field_id', $id)->delete();
            GroupFieldHaveComment::where('field_id', $id)->delete();
            TabStateField::where('field_id', $id)->delete();
        }

        $groupFieldEditData = [];
        if ($group_field_edit) {
            foreach ($group_field_edit as $groupId) {
                $groupFieldEditData[] = [
                    'field_id' => $field->id,
                    'group_id' => $groupId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        $groupFieldViewData = [];
        if ($group_field_view) {
            foreach ($group_field_view as $groupId) {
                $groupFieldViewData[] = [
                    'field_id' => $field->id,
                    'group_id' => $groupId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        $groupFieldHaveCommentData = [];
        if ($group_field_have_comment) {
            foreach ($group_field_have_comment as $groupId) {
                $groupFieldHaveCommentData[] = [
                    'field_id' => $field->id,
                    'group_id' => $groupId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        $tabStateFieldData = [];
        if ($tab_state_field) {
            foreach ($tab_state_field as $tabStateId) {
                $tabStateFieldData[] = [
                    'field_id' => $field->id,
                    'tab_state_id' => $tabStateId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        GroupFieldEdit::insert($groupFieldEditData);
        GroupFieldView::insert($groupFieldViewData);
        GroupFieldHaveComment::insert($groupFieldHaveCommentData);
        TabStateField::insert($tabStateFieldData);

        $campaigns = Campain::get();
        $blocks = Block::where('campain_id', $id)->get();
        $type_fields = TypeField::get();
        $widths = Width::get();
        $groups = Group::get();
        $tab_states = TabState::where('campain_id', $id)->get();
        $states = State::get();
        $fields = Field::leftjoin('campains', 'campains.id', '=', 'fields.campain_id')
            ->leftjoin('blocks', 'blocks.id', '=', 'fields.block_id')
            ->leftjoin('type_fields', 'type_fields.id', '=', 'fields.type_field_id')
            ->leftjoin('widths', 'widths.id', '=', 'fields.width_id')
            ->select(
                'fields.id as id',
                'campains.name as campain_name',
                'blocks.name as block_name',
                'type_fields.name as type_field_name',
                'widths.col as width_col',
                'fields.name as name',
                'fields.order as order',
                'fields.state as state',
                'fields.range as range',
            )
            ->where('fields.campain_id', $id)
            ->orderBy('fields.order', 'asc')
            ->get();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);
        $modules = $this->modules();

        return redirect()->back()->with([
            'id' => $campain_id,
            'campaigns' => $campaigns,
            'blocks' => $blocks,
            'type_fields' => $type_fields,
            'widths' => $widths,
            'groups' => $groups,
            'tab_states' => $tab_states,
            'states' => $states,
            'fields' => $fields,
            'user' => $user,
            'company' => $company,
            'modules' => $modules,
        ]);
    }

    public function getBlock()
    {
        $campain_id = request('campain_id');
        $elements = Block::where('campain_id', $campain_id)->get();
        return $elements;
    }

    public function getTypeField()
    {
        $elements = TypeField::get();
        return $elements;
    }

    public function getWidth()
    {
        $elements = Width::get();
        return $elements;
    }

    public function getUserGroup()
    {
        $elements = UserGroup::get();
        return $elements;
    }

    public function getState()
    {
        $elements = State::get();
        return $elements;
    }

    public function getField()
    {
        $id = request('id');
        $field = Field::findOrFail($id);
        $tab_states_fields = TabStateField::where('field_id', $id)
            ->leftjoin('tab_states', 'tab_states.id', '=', 'tab_states_fields.tab_state_id')
            ->select(
                'tab_states.id as id',
                'tab_states.name as name',
            )
            ->get();
        $groups_fields_edit = GroupFieldEdit::where('field_id', $id)
            ->leftjoin('groups', 'groups.id', '=', 'groups_fields_edit.group_id')
            ->select(
                'groups.id as id',
                'groups.name as name',
            )
            ->get();
        $groups_fields_view = GroupFieldView::where('field_id', $id)
            ->leftjoin('groups', 'groups.id', '=', 'groups_fields_view.group_id')
            ->select(
                'groups.id as id',
                'groups.name as name',
            )
            ->get();
        $groups_fields_have_comment = GroupFieldHaveComment::where('field_id', $id)
            ->leftjoin('groups', 'groups.id', '=', 'groups_fields_have_comment.group_id')
            ->select(
                'groups.id as id',
                'groups.name as name',
            )
            ->get();

        return response()->json([
            'field'             => $field,
            'tab_states_fields'  => $tab_states_fields,
            'groups_fields_edit'  => $groups_fields_edit,
            'groups_fields_view'  => $groups_fields_view,
            'groups_fields_have_comment'  => $groups_fields_have_comment,
        ]);
    }

    public function DeleteField($id)
    {
        $element = Field::findOrFail($id);

        $block = Block::findOrFail($element->block_id);
        $forms = Form::where('campain_id', $block->campain_id)->get();

        if (count($forms)) {
            return response()->json([
                'error' => 'El campo tiene ventas asociadas.',
            ], 400);
        }

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

    public function DisallowField($id)
    {
        $element = Field::findOrFail($id);
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

    public function AllowField($id)
    {
        $element = Field::findOrFail($id);
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
