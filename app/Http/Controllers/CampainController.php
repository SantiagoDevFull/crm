<?php

namespace App\Http\Controllers;

use App\Models\Campain;
use App\Models\Country;
use App\Models\RangeDate;
use App\Models\Group;
use App\Models\GroupCampainAuthorizateDuplicateSold;
use App\Models\GroupCampainUploadMassiveSold;
use App\Models\GroupCampainExportSold;
use App\Models\GroupCampainViewEdition;
use App\Models\GroupCampainAuditDataSold;

use Illuminate\Support\Facades\Auth;

class CampainController extends Controller
{

    public function index()
    {
        $campains = Campain::get();
        $countries = Country::get();
        $rangeDates = RangeDate::get();
        $groups = Group::get();

        return view('campain', [
                                'campains'      => $campains,
                                'countries'     => $countries,
                                'range_dates'   => $rangeDates,
                                'groups'        => $groups,
                            ]);
    }

    public function list()
    {
        $campain = Campain::get();
        return $campain;
    }

    public function validateModalForm()
    {

        $messages = [
            'name.required'         => 'Debe completar el nombre.',
            'country_id.required'         => 'Debe seleccionar un país.',
        ];

        $rules = [
            'name'                  => 'required',
            'country_id'                  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {

        $this->validateModalForm();

        $id = request('id');
        $name = request('name');
        $description = request('description');
        $country_id = request('country_id');
        $range_date_id = request('range_date_id');
        $geolocation = request('geolocation');
        $view_products = request('view_products');
        $back_state = request('back_state');
        $sold_new_window = request('sold_new_window');
        $sold_exists_window = request('sold_exists_window');
        $sold_notes = request('sold_notes');
        $list_sold_notes = request('list_sold_notes');
        $change_state_list_sold = request('change_state_list_sold');
        $option_duplicate_sold = request('option_duplicate_sold');
        $show_history_sold = request('show_history_sold');
        $user_group_export_sold_ids = request('group_export_sold_ids');
        $user_group_view_edition_ids = request('group_view_edition_ids');
        $user_group_upload_massive_sold_ids = request('group_upload_massive_sold_ids');
        $user_group_authorizate_duplicate_sold_ids = request('group_authorizate_duplicate_sold_ids');
        $user_group_audit_data_sold_ids = request('group_audit_data_sold_ids');
        $state = 1; //activo

        $group_campain_export_solds = json_decode($user_group_export_sold_ids);
        $group_campain_view_edition = json_decode($user_group_view_edition_ids);
        $group_campain_upload_massive_sold = json_decode($user_group_upload_massive_sold_ids);
        $group_campain_authorizate_duplicate_solds = json_decode($user_group_authorizate_duplicate_sold_ids);
        $group_campain_audit_data_sold = json_decode($user_group_audit_data_sold_ids);

        if (isset($id)) {
            $campain =  Campain::findOrFail($id);
            $msg = 'Registro actualizado exitosamente';
            $campain->updated_at_user = Auth::user()->name;
        } else {
            $campain = new Campain();
            $campain->state = $state;
            $campain->created_at_user = Auth::user()->name;
            $msg = 'Registro creado exitosamente';
        }

        $campain->name = $name;
        $campain->description = $description;
        $campain->country_id = $country_id;
        $campain->range_date_id = $range_date_id;
        $campain->geolocation = 0;
        if ($geolocation) $campain->geolocation = 1;
        $campain->view_products = 0;
        if ($view_products) $campain->view_products = 1;
        $campain->back_state = 0;
        if ($back_state) $campain->back_state = 1;
        $campain->sold_new_window = 0;
        if ($sold_new_window) $campain->sold_new_window = 1;
        $campain->sold_exists_window = 0;
        if ($sold_exists_window) $campain->sold_exists_window = 1;
        $campain->sold_notes = 0;
        if ($sold_notes) $campain->sold_notes = 1;
        $campain->list_sold_notes = 0;
        if ($list_sold_notes) $campain->list_sold_notes = 1;
        $campain->change_state_list_sold = 0;
        if ($change_state_list_sold) $campain->change_state_list_sold = 1;
        $campain->option_duplicate_sold = 0;
        if ($option_duplicate_sold) $campain->option_duplicate_sold = 1;
        $campain->show_history_sold = 0;
        if ($show_history_sold) $campain->show_history_sold = 1;

        $campain->save();

        if (isset($id)) {
            GroupCampainAuthorizateDuplicateSold::where('campain_id', $id)->delete();
            GroupCampainUploadMassiveSold::where('campain_id', $id)->delete();
            GroupCampainExportSold::where('campain_id', $id)->delete();
            GroupCampainViewEdition::where('campain_id', $id)->delete();
            GroupCampainAuditDataSold::where('campain_id', $id)->delete();
        }

        $groupCampainExportSolds = [];
        foreach ($group_campain_export_solds as $groupId) {
            $groupCampainExportSolds[] = [
                'campain_id' => $campain->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        $groupCampainViewEdition = [];
        foreach ($group_campain_view_edition as $groupId) {
            $groupCampainViewEdition[] = [
                'campain_id' => $campain->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        $groupCampainAuthorizateDuplicateSolds = [];
        foreach ($group_campain_authorizate_duplicate_solds as $groupId) {
            $groupCampainAuthorizateDuplicateSolds[] = [
                'campain_id' => $campain->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        $groupCampainAuditDataSold = [];
        foreach ($group_campain_audit_data_sold as $groupId) {
            $groupCampainAuditDataSold[] = [
                'campain_id' => $campain->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        $groupCampainUploadMassiveSold = [];
        foreach ($group_campain_upload_massive_sold as $groupId) {
            $groupCampainUploadMassiveSold[] = [
                'campain_id' => $campain->id,
                'group_id' => $groupId,
                'created_at_user' => Auth::user()->name,
            ];
        }

        GroupCampainExportSold::insert($groupCampainExportSolds);
        GroupCampainViewEdition::insert($groupCampainViewEdition);
        GroupCampainAuditDataSold::insert($groupCampainAuditDataSold);
        GroupCampainAuthorizateDuplicateSold::insert($groupCampainAuthorizateDuplicateSolds);
        GroupCampainUploadMassiveSold::insert($groupCampainUploadMassiveSold);

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function getCampain()
    {
        $id = request('id');
        $campain = Campain::findOrFail($id);
        $groups_campains_export_sold = GroupCampainExportSold::where('campain_id', $id)
                                                            ->leftjoin('groups', 'groups.id', '=', 'groups_campains_export_solds.group_id')
                                                            ->select(
                                                                'groups.id as id',
                                                                'groups.name as name',
                                                            )
                                                            ->get();
        $groups_campains_view_edition = GroupCampainViewEdition::where('campain_id', $id)
                                                            ->leftjoin('groups', 'groups.id', '=', 'groups_campains_view_edition.group_id')
                                                            ->select(
                                                                'groups.id as id',
                                                                'groups.name as name',
                                                            )
                                                            ->get();
        $groups_campains_audit_data_sold = GroupCampainAuditDataSold::where('campain_id', $id)
                                                                    ->leftjoin('groups', 'groups.id', '=', 'groups_campains_audit_data_sold.group_id')
                                                                    ->select(
                                                                        'groups.id as id',
                                                                        'groups.name as name',
                                                                    )
                                                                    ->get();
        $groups_campains_authorizate_duplicate_sold = GroupCampainAuthorizateDuplicateSold::where('campain_id', $id)
                                                                                        ->leftjoin('groups', 'groups.id', '=', 'groups_campains_authorizate_duplicate_solds.group_id')
                                                                                        ->select(
                                                                                            'groups.id as id',
                                                                                            'groups.name as name',
                                                                                        )
                                                                                        ->get();
        $groups_campains_upload_massive_sold = GroupCampainUploadMassiveSold::where('campain_id', $id)
                                                                                ->leftjoin('groups', 'groups.id', '=', 'groups_campains_upload_massive_sold.group_id')
                                                                                ->select(
                                                                                    'groups.id as id',
                                                                                    'groups.name as name',
                                                                                )
                                                                                ->get();

        return response()->json([
                                'campain'                                       => $campain,
                                'groups_campains_export_sold'                   => $groups_campains_export_sold,
                                'groups_campains_view_edition'                  => $groups_campains_view_edition,
                                'groups_campains_audit_data_sold'               => $groups_campains_audit_data_sold,
                                'groups_campains_authorizate_duplicate_sold'    => $groups_campains_authorizate_duplicate_sold,
                                'groups_campains_upload_massive_sold'           => $groups_campains_upload_massive_sold,
                            ]);
    }

    public function delete()
    {
        $element = Campain::findOrFail(request('id'));
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
        $element = Campain::findOrFail(request('id'));
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
        $element = Campain::findOrFail(request('id'));
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
