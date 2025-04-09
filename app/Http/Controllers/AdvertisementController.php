<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\GroupAdvertisement;
use App\Models\Group;

use Illuminate\Support\Facades\Auth;

class AdvertisementController extends Controller
{

    public function index()
    {
        $advertisements = Advertisement::get();
        $groups = Group::get();

        return view('advertisement', [
                                        'advertisements'  => $advertisements,
                                        'groups'          => $groups,
                                    ]);
    }

    public function validateModalForm()
    {

        $messages = [
            'title.required'  => 'Debe completar el título.',
        ];

        $rules = [
            'title'           => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {

        $this->validateModalForm();

        $id = request('id');
        $title = request('title');
        $text = request('text');
        $upload_id = request('upload_id');
        $user_group_advertisement = request('group_advertisement_ids');
        $state = 1; //activo

        $group_advertisement = json_decode($user_group_advertisement);

        if (isset($id)) {
            $advertisement =  Advertisement::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $advertisement->updated_at_user = Auth::user()->name;
        } else {
            $advertisement = new Advertisement();
            $advertisement->state = $state;
            $advertisement->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $advertisement->title = $title;
        $advertisement->text = $text;

        if ($upload_id) {
            $advertisement->upload_id = $upload_id;
        }

        $advertisement->save();

        if (isset($id)) {
            GroupAdvertisement::where('advertisement_id', $id)->delete();
        }

        $groupAdvertisement = [];
        foreach ($group_advertisement as $groupId) {
            $groupAdvertisement[] = [
                'advertisement_id' => $advertisement->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        GroupAdvertisement::insert($groupAdvertisement);

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function getAdvertisement()
    {
        $id = request('id');
        $advertisement = Advertisement::findOrFail($id);
        $groups_advertisements = GroupAdvertisement::where('advertisement_id', $id)
                                                            ->leftjoin('groups', 'groups.id', '=', 'groups_advertisements.group_id')
                                                            ->select(
                                                                'groups.id as id',
                                                                'groups.name as name',
                                                            )
                                                            ->get();

        return response()->json([
                                'advertisement'       => $advertisement,
                                'group_advertisement' => $groups_advertisements,
                            ]);
    }

    public function delete()
    {
        $element = Advertisement::findOrFail(request('id'));
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
        $element = Advertisement::findOrFail(request('id'));
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
        $element = Advertisement::findOrFail(request('id'));
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