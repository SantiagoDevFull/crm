<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Day;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class HorarioController extends Controller
{

    public function index()
    {

        // $horarios = Horario::leftjoin('days', 'days.horario_id', '=', 'horarios.id')
        //                     ->select(
        //                         'horarios.id as id',
        //                         'horarios.name as name',
        //                         'horarios.sede_id as sede_id',
        //                         'horarios.tolerancia_min as tolerancia_min',
        //                         'horarios.state as state',
        //                         'days.day as day_day',
        //                         'days.inicio as day_inicio',
        //                         'days.final as day_final',
        //                         DB::raw('JSON_ARRAYAGG(
        //                             JSON_OBJECT(
        //                                 "day", days.day,
        //                                 "inicio", days.inicio,
        //                                 "final", days.final
        //                             )
        //                         ) as days')
        //                     )
        //                     ->groupBy('horarios.id')
        //                     ->get();
        $horarios = Horario::with(['days' => function ($query) {
                                $query->select('horario_id', 'day', 'inicio', 'final');
                            }])
                            ->select('id', 'name', 'sede_id', 'tolerancia_min', 'state')
                            ->get();

        $horarios = $horarios->map(function ($horario) {
                        return [
                            'id' => $horario->id,
                            'name' => $horario->name,
                            'sede_id' => $horario->sede_id,
                            'tolerancia_min' => $horario->tolerancia_min,
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

        return view('horario')->with(compact('horarios'));
    }

    public function validateModal()
    {
        $messages = [
            'model.name.required' => 'Debe ingresar un nombre.',
            'model.tolerancia_min.required' => 'Debe ingresar la Tolerancia (mins).',
            'details.*.day.required' => 'El campo día es requerido.',
            'details.*.start.required' => 'El campo entrada es requerido.',
            'details.*.end.required' => 'El campo salida es requerido.',
        ];

        $rules = [
            'model.name' => 'required',
            'model.tolerancia_min' => 'required',
            'details.*.day' => 'required',
            'details.*.start' => 'required',
            'details.*.end' => 'required',
        ];

        // Validate the request
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

    public function store()
    {
        $this->validateModal();

        $id = request('model.id');
        $name = request('model.name');
        $tolerancia_min = request('model.tolerancia_min');
        $motivo_tardanza = request('model.motivo_tardanza');
        $motivo_temprano = request('model.motivo_temprano');
        $restringir_last = request('model.restringir_last');
        $restringir_gest = request('model.restringir_gest');
        $details = request('details');

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
        $horario->motivo_tardanza = $motivo_tardanza;
        $horario->motivo_temprano = $motivo_temprano;
        $horario->restringir_last = $restringir_last;
        $horario->restringir_gest = $restringir_gest;
        $horario->save();

        foreach ($details as $item) {
            $element = new Day();
            $element->horario_id = $horario->id;
            $element->day = $item['day'];
            $element->inicio = $item['start'];
            $element->final = $item['end'];
            $element->save();
        }

        $type = 3;
        $title = 'Bien';
        $url = route('dashboard.horario.index');


        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }

    public function delete()
    {

        $id = request('id');

        $days = Day::where('horario_id', $id);
        $days->delete();

        $horario = Horario::findOrFail($id);
        $horario->delete();

        $type = 3;
        $title = 'Bien';
        $msg = 'Horario eliminado exitosamente.';
        $url = route('dashboard.horario.index');


        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }
}
