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

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FieldController extends Controller
{

    public function index(Request $request)
    {
        $campain_id = 0;
        $campains = Campain::get();

        if ($request->query('id')) {
            $campain_id = $request->query('id');
        };

        return view('field', [
                                'campain_id'      => $campain_id,
                                'campains'      => $campains
                            ]);
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
            )
            ->where('fields.campain_id', $campain_id)
            ->orderBy('fields.order','asc')
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

    public function store()
    {

        $this->validateModalForm();

        $id = request('id');
        $campain_id = request('campain_id');
        $block_id = request('block_id');
        $type_field_id = request('type_field_id');
        $width_id = request('width_id');
        $name = request('name');
        $options = request('options');
        $order = request('order');
        $unique = request('unique');
        $required = request('required');
        $bloq_mayus = request('bloq_mayus');
        $in_solds_list = request('in_solds_list');
        $in_notifications = request('in_notifications');
        $has_edit = request('has_edit');
        $in_general_search = request('in_general_search');
        $user_group_edit_ids = request('group_edit_ids');
        $user_group_view_ids = request('group_view_ids');
        $user_group_have_comment_ids = request('group_have_comment_ids');
        $tab_state_ids = request('tab_state_ids');
        $state = 1; //activo

        $group_field_edit = json_decode($user_group_edit_ids);
        $group_field_view = json_decode($user_group_view_ids);
        $group_field_have_comment = json_decode($user_group_have_comment_ids);
        $tab_state_field = json_decode($tab_state_ids);

        if (isset($id)) {
            $field =  Field::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $field->updated_at_user = Auth::user()->name;
        } else {
            $field = new Field();
            $field->campain_id = $campain_id;
            $field->block_id = $block_id;
            $field->type_field_id = $type_field_id;
            $field->width_id = $width_id;
            $field->state = $state;
            $field->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $field->name = $name;
        $field->options = $options;
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
        foreach ($group_field_edit as $groupId) {
            $groupFieldEditData[] = [
                'field_id' => $field->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        $groupFieldViewData = [];
        foreach ($group_field_view as $groupId) {
            $groupFieldViewData[] = [
                'field_id' => $field->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        $groupFieldHaveCommentData = [];
        foreach ($group_field_have_comment as $groupId) {
            $groupFieldHaveCommentData[] = [
                'field_id' => $field->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        $tabStateFieldData = [];
        foreach ($tab_state_field as $tabStateId) {
            $tabStateFieldData[] = [
                'field_id' => $field->id,
                'tab_state_id' => $tabStateId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        GroupFieldEdit::insert($groupFieldEditData);
        GroupFieldView::insert($groupFieldViewData);
        GroupFieldHaveComment::insert($groupFieldHaveCommentData);
        TabStateField::insert($tabStateFieldData);

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
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

    public function delete()
    {
        $element = Field::findOrFail(request('id'));
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

    public function deshabilitar()
    {
        $element = Field::findOrFail(request('id'));
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

    public function habilitar()
    {
        $element = Field::findOrFail(request('id'));
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