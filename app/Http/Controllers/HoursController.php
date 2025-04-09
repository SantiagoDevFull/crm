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

class HoursController extends Controller
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
        $company = Company::findOrFail(1);
        $campaigns = Campain::get();
        $horarios = $this->getHorarios();
        $modules = $this->modules();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);

        return view('hours', compact('horarios','campaigns','modules','user','company'));
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
                            ->orderBy('order','asc')
                            ->get();
        $subSections = SubSection::whereIn('id', $subSectionsIds)
                                ->get();

        $result = $modules->map(function ($module) use ($sections, $subSections) {

            $moduleSections = $sections->sortBy('order')->where('module_id', $module->id)->map(function ($section) use ($subSections)
            {
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

    public function getHorarios()
    {
        $horarios = Horario::with(['days' => function ($query) {
                                $query->select('horario_id', 'day', 'inicio', 'final');
                            }])
                            ->select('id', 'name', 'sede_id', 'tolerancia_min', 'motivo_tardanza', 'motivo_temprano', 'restringir_last', 'restringir_gest', 'state')
                            ->get();

        $horarios = $horarios->map(function ($horario) {
                        return [
                            'id' => $horario->id,
                            'name' => $horario->name,
                            'sede_id' => $horario->sede_id,
                            'tolerancia_min' => $horario->tolerancia_min,
                            'motivo_tardanza' => $horario->motivo_tardanza,
                            'motivo_temprano' => $horario->motivo_temprano,
                            'restringir_last' => $horario->restringir_last,
                            'restringir_gest' => $horario->restringir_gest,
                            'state' => $horario->state,
                            'days' => $horario->days->map(function ($day) {
                                return [
                                    'day' => $day->day,
                                    'inicio' => $day->inicio,
                                    'final' => $day->final
                                ];
                            })
                        ];
                    });

        $horarios = json_decode(json_encode($horarios));

        return $horarios;
    }

    public function validateModal()
    {
        $messages = [
            'name.required' => 'Debe ingresar un nombre.',
            'tolerancia_min.required' => 'Debe ingresar la Tolerancia (mins).',
            // 'details.*.day.required' => 'El campo día es requerido.',
            // 'details.*.start.required' => 'El campo entrada es requerido.',
            // 'details.*.end.required' => 'El campo salida es requerido.',
        ];

        $rules = [
            'name' => 'required',
            'tolerancia_min' => 'required',
            // 'details.*.day' => 'required',
            // 'details.*.start' => 'required',
            // 'details.*.end' => 'required',
        ];

        request()->validate($rules, $messages);

        return request()->all();
    }

    public function getDay()
    {
        $id = request('id');
        $days = Day::select('day', 'inicio as start', 'final as end')
            ->where('horario_id', $id)->get();
        return $days;
    }

    public function SaveHour(Request $request)
    {
        $this->validateModal();

        $campaigns = Campain::get();
        $id = request('id');
        $name = request('name');
        $tolerancia_min = request('tolerancia_min');
        $motivo_tardanza = request('motivo_tardanza');
        $motivo_temprano = request('motivo_temprano');
        $restringir_last = request('restringir_last');
        $restringir_gest = request('restringir_gest');

        // days
        $in_time_monday = request('in-time-monday');
        $out_time_monday = request('out-time-monday');
        $in_time_tuesday = request('in-time-tuesday');
        $out_time_tuesday = request('out-time-tuesday');
        $in_time_wednesday = request('in-time-wednesday');
        $out_time_wednesday = request('out-time-wednesday');
        $in_time_thursday = request('in-time-thursday');
        $out_time_thursday = request('out-time-thursday');
        $in_time_friday = request('in-time-friday');
        $out_time_friday = request('out-time-friday');
        $in_time_saturday = request('in-time-saturday');
        $out_time_saturday = request('out-time-saturday');
        $in_time_sunday = request('in-time-sunday');
        $out_time_sunday = request('out-time-sunday');

        $details = [
            [
                'day' => "Lunes",
                'start' => $in_time_monday,
                'end' => $out_time_monday
            ],
            [
                'day' => "Martes",
                'start' => $in_time_tuesday,
                'end' => $out_time_tuesday
            ],
            [
                'day' => "Miércoles",
                'start' => $in_time_wednesday,
                'end' => $out_time_wednesday
            ],
            [
                'day' => "Jueves",
                'start' => $in_time_thursday,
                'end' => $out_time_thursday
            ],
            [
                'day' => "Viernes",
                'start' => $in_time_friday,
                'end' => $out_time_friday
            ],
            [
                'day' => "Sábado",
                'start' => $in_time_saturday,
                'end' => $out_time_saturday
            ],
            [
                'day' => "Domingo",
                'start' => $in_time_sunday,
                'end' => $out_time_sunday
            ],
        ];

        if (isset($id)) {
            $horario = Horario::findOrFail($id);
            Day::where('horario_id', $id)->delete();
            $msg = "Horario actualizado con éxito.";
        } else {
            $horario = new Horario();
            $horario->state = "Activo";
            $msg = "Horario creado con éxito.";
        }

        $horario->name = $name;
        $horario->tolerancia_min = $tolerancia_min;

        if ($motivo_tardanza == "on") {
            $horario->motivo_tardanza = 1;
        } else {
            $horario->motivo_tardanza = 0;
        };

        if ($motivo_temprano == "on") {
            $horario->motivo_temprano = 1;
        } else {
            $horario->motivo_temprano = 0;
        };

        if ($restringir_last == "on") {
            $horario->restringir_last = 1;
        } else {
            $horario->restringir_last = 0;
        };

        if ($restringir_gest == "on") {
            $horario->restringir_gest = 1;
        } else {
            $horario->restringir_gest = 0;
        };

        $horario->save();

        foreach ($details as $item) {
            $element = new Day();
            $element->horario_id = $horario->id;
            $element->day = $item['day'];
            $element->inicio = $item['start'];
            $element->final = $item['end'];
            $element->save();
        }

        $horarios = $this->getHorarios();
        $modules = $this->modules();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return redirect()->back()->with([
            'horarios' => $horarios,
            'campaigns' => $campaigns,
            'modules' => $modules,
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function DeleteHour($id)
    {
        $days = Day::where('horario_id', $id);
        $days->delete();

        $horario = Horario::findOrFail($id);
        $horario->delete();

        $type = 3;
        $title = 'Bien';
        $msg = 'Horario eliminado exitosamente.';


        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
        ]);
    }

    public function DisallowHour($id)
    {
        $element = Horario::findOrFail($id);
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

    public function AllowHour($id)
    {
        $element = Horario::findOrFail($id);
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