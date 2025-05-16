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
use App\Models\GroupCampainAuthorizateDuplicateSold;
use App\Models\GroupCampainUploadMassiveSold;
use App\Models\GroupCampainExportSold;
use App\Models\GroupCampainViewEdition;
use App\Models\GroupCampainAuditDataSold;
use App\Models\ModuleInGroup;
use App\Models\Module;
use App\Models\SectionInGroup;
use App\Models\Section;
use App\Models\SubSectionInGroup;
use App\Models\SubSection;
use App\Models\Form;
use App\Models\Field;
use App\Models\Block;
use App\Models\TabState;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignsController extends Controller
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

        $campaigns = Campain::leftjoin('user_groups', 'user_groups.id', '=', 'campains.user_group_id')
        ->select(
            'campains.id as id',
            'campains.name as name',
            'campains.description as description',
            'campains.state as state',
            'campains.geolocation as geolocation'
        )
            ->where('user_groups.group_id', 14)
            ->where('user_groups.user_id', Auth::user()->id)
            ->get();


        $countries = Country::get();
        $rangeDates = RangeDate::get();
        $userId = Auth::user()->id;
        $groups = Group::leftjoin('horarios', 'horarios.id', '=', 'groups.horario_id')
            ->leftjoin('companies', 'companies.id', '=', 'groups.company_id')
            ->leftjoin('user_groups', 'user_groups.id', '=', 'companies.user_group_id')
            ->select(
                'groups.id as id',
                'groups.name as name'
            )
            ->where('user_groups.user_id', $userId)
            ->where('user_groups.group_id', 14)
            ->get();
        $groupsAuthorizeDuplicateSold = GroupCampainAuthorizateDuplicateSold::get();
        $groupsUploadMassiveSold = GroupCampainUploadMassiveSold::get();
        $groupsExportSold = GroupCampainExportSold::get();
        $groupsViewEdition = GroupCampainViewEdition::get();
        $groupsAuditDataSold = GroupCampainAuditDataSold::get();
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return view('campaigns', compact('campaigns', 'groups', 'countries', 'rangeDates', 'groupsAuthorizeDuplicateSold', 'groupsUploadMassiveSold', 'groupsExportSold', 'groupsViewEdition', 'groupsAuditDataSold', 'modules', 'user', 'company'));
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

    public function SaveCampaign()
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
        $group_campain_export_solds = request('export_list_solds');
        $group_campain_view_edition = request('show_sold_edit');
        $group_campain_audit_data_sold = request('audit_data_sold');
        $group_campain_upload_massive_sold = request('charge_massive_sold');
        $group_campain_authorizate_duplicate_solds = request('autorize_duplicate_sold');
        $state = 1; //activo

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
        if ($description) {
            $campain->description = $description;
        } else {
            $campain->description = "";
        }
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

        $user_group_id = UserGroup::where('user_id', Auth::user()->id)
            ->where('group_id', 14)->first();
        $campain->user_group_id = $user_group_id->id ?? null;
        $campain->save();

        if (empty($id)) {
            $sub_section = new SubSection();
            $sub_section->name = $name;
            $sub_section->section_id = 5;
            $sub_section->url = "sales/solds/$campain->id";
            $sub_section->created_at_user = Auth::user()->name;
            $sub_section->save();

            $sub_section_in_group = new SubSectionInGroup();
            $sub_section_in_group->sub_section_id = $sub_section->id;
            $sub_section_in_group->group_id = 14;
            $sub_section_in_group->created_at_user = Auth::user()->name;
            $sub_section_in_group->save();
        }

        if (isset($id)) {
            GroupCampainAuthorizateDuplicateSold::where('campain_id', $id)->delete();
            GroupCampainUploadMassiveSold::where('campain_id', $id)->delete();
            GroupCampainExportSold::where('campain_id', $id)->delete();
            GroupCampainViewEdition::where('campain_id', $id)->delete();
            GroupCampainAuditDataSold::where('campain_id', $id)->delete();
        }

        $groupCampainExportSolds = [];
        if ($group_campain_export_solds) {
            foreach ($group_campain_export_solds as $groupId) {
                $groupCampainExportSolds[] = [
                    'campain_id' => $campain->id,
                    'group_id' => $groupId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        $groupCampainViewEdition = [];
        if ($group_campain_view_edition) {
            foreach ($group_campain_view_edition as $groupId) {
                $groupCampainViewEdition[] = [
                    'campain_id' => $campain->id,
                    'group_id' => $groupId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        $groupCampainAuthorizateDuplicateSolds = [];
        if ($group_campain_authorizate_duplicate_solds) {
            foreach ($group_campain_authorizate_duplicate_solds as $groupId) {
                $groupCampainAuthorizateDuplicateSolds[] = [
                    'campain_id' => $campain->id,
                    'group_id' => $groupId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        $groupCampainAuditDataSold = [];
        if ($group_campain_audit_data_sold) {
            foreach ($group_campain_audit_data_sold as $groupId) {
                $groupCampainAuditDataSold[] = [
                    'campain_id' => $campain->id,
                    'group_id' => $groupId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        $groupCampainUploadMassiveSold = [];
        if ($group_campain_upload_massive_sold) {
            foreach ($group_campain_upload_massive_sold as $groupId) {
                $groupCampainUploadMassiveSold[] = [
                    'campain_id' => $campain->id,
                    'group_id' => $groupId,
                    'created_at_user' => Auth::user()->name,
                ];
            }
        }

        GroupCampainExportSold::insert($groupCampainExportSolds);
        GroupCampainViewEdition::insert($groupCampainViewEdition);
        GroupCampainAuditDataSold::insert($groupCampainAuditDataSold);
        GroupCampainAuthorizateDuplicateSold::insert($groupCampainAuthorizateDuplicateSolds);
        GroupCampainUploadMassiveSold::insert($groupCampainUploadMassiveSold);

        $campaigns = Campain::get();
        $countries = Country::get();
        $rangeDates = RangeDate::get();
        $groups = Group::get();
        $groupsAuthorizeDuplicateSold = GroupCampainAuthorizateDuplicateSold::get();
        $groupsUploadMassiveSold = GroupCampainUploadMassiveSold::get();
        $groupsExportSold = GroupCampainExportSold::get();
        $groupsViewEdition = GroupCampainViewEdition::get();
        $groupsAuditDataSold = GroupCampainAuditDataSold::get();

        $modules = $this->modules();
        $userId = Auth::user()->id;
        $user = User::findOrFail($userId);
        $company = Company::findOrFail(1);

        return redirect()->back()->with([
            'campaigns' => $campaigns,
            'countries' => $countries,
            'rangeDates' => $rangeDates,
            'groups' => $groups,
            'groupsAuthorizeDuplicateSold' => $groupsAuthorizeDuplicateSold,
            'groupsUploadMassiveSold' => $groupsUploadMassiveSold,
            'groupsExportSold' => $groupsExportSold,
            'groupsViewEdition' => $groupsViewEdition,
            'groupsAuditDataSold' => $groupsAuditDataSold,
            'modules' => $modules,
            'user' => $user,
            'company' => $company,
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

    public function DeleteCampaign($id)
    {

        Log::info($id);
        $fields = Field::where('campain_id', $id)->get();
        $forms = Form::where('campain_id', $id)->get();
        $blocks = Block::where('campain_id', $id)->get();
        $tabStates = TabState::where('campain_id', $id)->get();

        if (count($fields) || count($forms) || count($blocks) || count($tabStates)) {
            return response()->json([
                'error' => 'La campaña tiene campos, bloque de campos, pestaña de estados, estados y/o ventas asociadas.',
            ], 400);
        }

        $element = Campain::findOrFail($id);

        $camp = SubSection::where('name', $element->name)->first();

        if ($camp) {
            $camp->delete();
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

    public function DisallowCampaign($id)
    {
        $element = Campain::findOrFail($id);
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

    public function AllowCampaign($id)
    {
        $element = Campain::findOrFail($id);
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
