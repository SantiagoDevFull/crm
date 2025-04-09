<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Campain;
use App\Models\TabState;
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
use App\Models\State;
use App\Models\File;
use App\Models\Form;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoldController extends Controller
{

    public function index(Request $request)
    {
        $campain_id = $request->query('id');
        $campain = Campain::where('id', $campain_id)->first();
        $tab_states = TabState::where('campain_id', $campain_id)
                                ->orderBy('tab_states.order','asc')
                                ->get();

        return view('sold', [
                                'campain' => $campain,
                                'tab_states' => $tab_states
                            ]);
    }

    public function create(Request $request)
    {
        $state_id = "0";
        $id_form = "0";
        $tab_state_id = $request->query('id');
        $form_id = $request->query('form_id');
        $tab_state = TabState::where('id', $tab_state_id)->first();
        $form = null;
        $data = null;
        if ($form_id) {
            $id_form = $form_id;
            $form = Form::where('id', $form_id)->first();
        };
        $campain = Campain::where('id', $tab_state->campain_id)->first();
        $tab_states_fields = TabStateField::where('tab_state_id', $tab_state_id)->get();
        $field_ids = [];
        foreach ($tab_states_fields as $tab_state_field) {
            $field_ids[] = $tab_state_field->field_id;
        }
        $states = State::where('tab_state_id', $tab_state_id)
                        ->orderBy('states.order','asc')
                        ->get();
        $blocks = Block::where('campain_id', $campain->id)
                        ->orderBy('blocks.order','asc')
                        ->get();
        $fields = Field::where('fields.campain_id', $campain->id)
                        ->whereIn('fields.id', $field_ids)
                        ->leftjoin('blocks', 'blocks.id', '=', 'fields.block_id')
                        ->leftjoin('type_fields', 'type_fields.id', '=', 'fields.type_field_id')
                        ->leftjoin('widths', 'widths.id', '=', 'fields.width_id')
                        ->select(
                            'blocks.name as block_name',
                            'type_fields.name as type_field_name',
                            'widths.col as width_col',
                            'fields.id as id',
                            'fields.type_field_id as type_field_id',
                            'fields.block_id as block_id',
                            'fields.name as name',
                            'fields.options as options',
                            'fields.order as order',
                            'fields.state as state',
                        )
                        ->orderBy('fields.order','asc')
                        ->get();

        $model = [];

        if ($form) {
            $state_id = $form->state_id;
            $data = json_decode($form->data, true);
        };

        for ($i = 0; $i < count($fields); $i++) {
            $field = $fields[$i];
            $id = (string) $field->id;
            $block_id = (string) $field->block_id;
            $name = $field->name;
            $block_name = $field->block_name;
            $value = "";

            switch ($field->type_field_id) {
                case 4:
                    $value = [];
                    break;
                case 8:
                    $value = false;
                    break;
                case 10:
                    $value = [];
                    break;
                default:
                    $value = "";
                    break;
            }

            if ($data) {
                $block = $data[$block_id];

                if ($block) {
                    $f = $block[$id];

                    if ($f) $value = $f;
                }
            }

            $model[$block_id][$id] = $value;
        };

        return view('soldCreate', [
                                    'id'        => $id_form,
                                    'state_id'  => $state_id,
                                    'campain'   => $campain,
                                    'tab_state' => $tab_state,
                                    'blocks'    => $blocks,
                                    'fields'    => $fields,
                                    'states'    => $states,
                                    'model'     => $model
                                ]);
    }

    public function upload(Request $request) {
        $fileUpload = $request->file('file');

        if ($fileUpload) {
            $fileName = $fileUpload->getClientOriginalName();
            $filePath = 'uploads/' . $fileName;

            if (Storage::exists($filePath)) {
                $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . time() . '.' . $fileUpload->getClientOriginalExtension();
            }

            $path = $fileUpload->storeAs('uploads', $fileName);

            $file = new File();
            $file->name = $fileName;
            $file->path = $path;
            $file->created_at_user = Auth::user()->name;
            $file->save();

            $type = 1;
            $title = '¡Ok!';
            $msg = $file->id;
        } else {
            $type = 2;
            $title = '¡Error!';
            $msg = 'No se envio ningun archivo';
        };

        return response()->json([
                                'type'    => $type,
                                'title'    => $title,
                                'msg'    => $msg,
                            ]);
    }

    public function download($id) {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado.'], 404);
        }

        $filePath = $file->path;
        $fileName = $file->name;

        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'Archivo no existe en el servidor.'], 404);
        }

        return Storage::download($filePath, $fileName);
    }

    public function validateForm()
    {
        $messages = [
            'campain_id.required'       => 'Debe seleccionar una Campaña.',
            'tab_state_id.required'     => 'Debe seleccionar una Pestaña de estado.',
            'state_id.required'         => 'Debe seleccionar una Estado.',
        ];

        $rules = [
            'campain_id'                => 'required',
            'tab_state_id'              => 'required',
            'state_id'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store() {

        $this->validateForm();

        $id = request('id');
        $campain_id = request('campain_id');
        $tab_state_id = request('tab_state_id');
        $state_id = request('state_id');
        $data = request('data');
        $state = 1; //activo

        if (isset($id) && $id > 0) {
            $form =  Form::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $form->updated_at_user = Auth::user()->name;
        } else {
            $form = new Form();
            $form->campain_id = $campain_id;
            $form->tab_state_id = $tab_state_id;
            $form->state = $state;
            $form->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $form->state_id = $state_id;
        $form->data = $data;

        $form->save();

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
                            'type'    => $type,
                            'title'    => $title,
                            'msg'    => $msg,
                        ]);
    }

    public function validateFormList()
    {
        $messages = [
            'campain_id.required'       => 'Debe seleccionar una Campaña.',
            'tab_state_id.required'     => 'Debe seleccionar una Pestaña de estado.',
        ];

        $rules = [
            'campain_id'                => 'required',
            'tab_state_id'              => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list() {
        $this->validateFormList();

        $campain_id = request('campain_id');
        $tab_state_id = request('tab_state_id');

        $tab_states_fields = TabStateField::where('tab_state_id', $tab_state_id)->get();
        $field_ids = [];
        foreach ($tab_states_fields as $tab_state_field) {
            $field_ids[] = $tab_state_field->field_id;
        }

        $forms = Form::where('forms.campain_id', $campain_id)
                        ->where('forms.tab_state_id', $tab_state_id)
                        ->leftjoin('campains', 'campains.id', '=', 'forms.campain_id')
                        ->leftjoin('states', 'states.id', '=', 'forms.state_id')
                        ->select(
                            'forms.id as id',
                            'campains.name as campain_name',
                            'states.name as state_name',
                            'forms.data as data',
                            'forms.created_at_user as created_at_user',
                            'forms.created_at as created_at',
                            'forms.updated_at as updated_at',
                            'forms.state as state',
                        )
                        ->orderBy('forms.id','desc')
                        ->get();

        $fields = Field::where('fields.campain_id', $campain_id)
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

        return [
            'forms'  => $forms,
            'fields' => $fields,
        ];
    }

    public function delete()
    {
        $element = Form::findOrFail(request('id'));
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
        $element = Form::findOrFail(request('id'));
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
        $element = Form::findOrFail(request('id'));
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