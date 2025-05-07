<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::middleware(['check.user.ip'])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'root']);
    Route::get('/enterprise', [App\Http\Controllers\HomeController::class, 'enterprise'])->name('dashboard.home.enterprise');
    Route::get('/sales', [App\Http\Controllers\HomeController::class, 'sales'])->name('dashboard.home.sales');

    // esta ruta evita renderizar mas vistas
    // Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index']);
    //Language Translation

    Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

    Route::post('/formsubmit', [App\Http\Controllers\HomeController::class, 'FormSubmit'])->name('FormSubmit');

    Route::post('/profile/save-image-profile', [App\Http\Controllers\ProfileController::class, 'SaveImageProfile'])->name('SaveImageProfile');
    Route::post('/enterprise/save-company', [App\Http\Controllers\EnterpriseController::class, 'SaveCompany'])->name('SaveCompany');
    Route::post('/enterprise/save-hour', [App\Http\Controllers\HoursController::class, 'SaveHour'])->name('SaveHour');
    Route::post('/enterprise/save-group', [App\Http\Controllers\GroupUsersController::class, 'SaveGroup'])->name('SaveGroup');
    Route::post('/enterprise/save-user', [App\Http\Controllers\UsersController::class, 'SaveUser'])->name('SaveUser');
    Route::post('/enterprise/add-group-user', [App\Http\Controllers\UsersController::class, 'AddGroup'])->name('AddGroup');
    Route::post('/enterprise/save-advertisement', [App\Http\Controllers\AdvertisementsController::class, 'SaveAdvertisement'])->name('SaveAdvertisement');

    Route::patch('/enterprise/disallow-hour/{id}', [App\Http\Controllers\HoursController::class, 'DisallowHour'])->name('DisallowHour');
    Route::patch('/enterprise/allow-hour/{id}', [App\Http\Controllers\HoursController::class, 'AllowHour'])->name('AllowHour');
    Route::patch('/enterprise/disallow-group/{id}', [App\Http\Controllers\GroupUsersController::class, 'DisallowGroup'])->name('DisallowGroup');
    Route::patch('/enterprise/allow-group/{id}', [App\Http\Controllers\GroupUsersController::class, 'AllowGroup'])->name('AllowGroup');
    Route::patch('/enterprise/disallow-advertisement/{id}', [App\Http\Controllers\AdvertisementsController::class, 'DisallowAdvertisement'])->name('DisallowAdvertisement');
    Route::patch('/enterprise/allow-advertisement/{id}', [App\Http\Controllers\AdvertisementsController::class, 'AllowAdvertisement'])->name('AllowAdvertisement');

    Route::delete('/enterprise/delete-hour/{id}', [App\Http\Controllers\HoursController::class, 'DeleteHour'])->name('DeleteHour');
    Route::delete('/enterprise/delete-group/{id}', [App\Http\Controllers\GroupUsersController::class, 'DeleteGroup'])->name('DeleteGroup');
    Route::delete('/enterprise/delete-user/{id}', [App\Http\Controllers\UsersController::class, 'DeleteUser'])->name('DeleteUser');
    Route::delete('/enterprise/delete-company/{id}', [App\Http\Controllers\EnterpriseController::class, 'DeleteCompany'])->name('DeleteCompany');
    Route::delete('/enterprise/delete-admin/{id}', [App\Http\Controllers\EnterpriseController::class, 'DeleteAdmin'])->name('DeleteAdmin');
    Route::delete('/enterprise/delete-user-group/{id}', [App\Http\Controllers\UsersController::class, 'DeleteUserGroup'])->name('DeleteUserGroup');
    Route::delete('/enterprise/delete-advertisement/{id}', [App\Http\Controllers\AdvertisementsController::class, 'DeleteAdvertisement'])->name('DeleteAdvertisement');

    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index']);
    Route::get('/enterprise/companies', [App\Http\Controllers\CompanyController::class, 'index']);
    Route::get('/enterprise/config', [App\Http\Controllers\EnterpriseController::class, 'index'])->name('dashboard.company.index');
    Route::get('/enterprise/hours', [App\Http\Controllers\HoursController::class, 'index'])->name('dashboard.horario.index');
    Route::get('/enterprise/groups', [App\Http\Controllers\GroupUsersController::class, 'index'])->name('dashboard.group.user.index');
    Route::get('/enterprise/users', [App\Http\Controllers\UsersController::class, 'index'])->name('dashboard.user.index');
    Route::get('/enterprise/advertisements', [App\Http\Controllers\AdvertisementsController::class, 'index'])->name('dashboard.advertisement.index');

    Route::post('/sales/save-campaign', [App\Http\Controllers\CampaignsController::class, 'SaveCampaign'])->name('SaveCampaign');
    Route::post('/sales/save-tab-state', [App\Http\Controllers\TabStatesController::class, 'SaveTabState'])->name('SaveTabState');
    Route::post('/sales/save-state', [App\Http\Controllers\StatesController::class, 'SaveState'])->name('SaveState');
    Route::post('/sales/save-block', [App\Http\Controllers\BlockCampsController::class, 'SaveBlock'])->name('SaveBlock');
    Route::post('/sales/save-field', [App\Http\Controllers\FieldsController::class, 'SaveField'])->name('SaveField');
    Route::post('/sales/save-back', [App\Http\Controllers\BackOfficesController::class, 'SaveBack'])->name('SaveBack');
    Route::post('/sales/save-sup', [App\Http\Controllers\SupsController::class, 'SaveSup'])->name('SaveSup');
    Route::post('/sales/save-agent', [App\Http\Controllers\AgentsController::class, 'SaveAgent'])->name('SaveAgent');
    Route::post('/sales/save-sold', [App\Http\Controllers\SoldsController::class, 'SaveSold'])->name('SaveSold');

    Route::patch('/sales/disallow-campaign/{id}', [App\Http\Controllers\CampaignsController::class, 'DisallowCampaign'])->name('DisallowCampaign');
    Route::patch('/sales/allow-campaign/{id}', [App\Http\Controllers\CampaignsController::class, 'AllowCampaign'])->name('AllowCampaign');
    Route::patch('/sales/disallow-tab-state/{id}', [App\Http\Controllers\TabStatesController::class, 'DisallowTabState'])->name('DisallowTabState');
    Route::patch('/sales/allow-tab-state/{id}', [App\Http\Controllers\TabStatesController::class, 'AllowTabState'])->name('AllowTabState');
    Route::patch('/sales/disallow-state/{id}', [App\Http\Controllers\StatesController::class, 'DisallowState'])->name('DisallowState');
    Route::patch('/sales/allow-state/{id}', [App\Http\Controllers\StatesController::class, 'AllowState'])->name('AllowState');
    Route::patch('/sales/disallow-block/{id}', [App\Http\Controllers\BlockCampsController::class, 'DisallowBlock'])->name('DisallowBlock');
    Route::patch('/sales/allow-block/{id}', [App\Http\Controllers\BlockCampsController::class, 'AllowBlock'])->name('AllowBlock');
    Route::patch('/sales/disallow-field/{id}', [App\Http\Controllers\FieldsController::class, 'DisallowField'])->name('DisallowField');
    Route::patch('/sales/allow-field/{id}', [App\Http\Controllers\FieldsController::class, 'AllowField'])->name('AllowField');
    Route::patch('/sales/disallow-back/{id}', [App\Http\Controllers\BackOfficesController::class, 'DisallowBack'])->name('DisallowBack');
    Route::patch('/sales/allow-back/{id}', [App\Http\Controllers\BackOfficesController::class, 'AllowBack'])->name('AllowBack');
    Route::patch('/sales/disallow-sup/{id}', [App\Http\Controllers\SupsController::class, 'DisallowSup'])->name('DisallowSup');
    Route::patch('/sales/allow-sup/{id}', [App\Http\Controllers\SupsController::class, 'AllowSup'])->name('AllowSup');
    Route::patch('/sales/disallow-agent/{id}', [App\Http\Controllers\AgentsController::class, 'DisallowAgent'])->name('DisallowAgent');
    Route::patch('/sales/allow-agent/{id}', [App\Http\Controllers\AgentsController::class, 'AllowAgent'])->name('AllowAgent');
    Route::patch('/sales/disallow-sold/{id}', [App\Http\Controllers\SoldsController::class, 'DisallowSold'])->name('DisallowSold');
    Route::patch('/sales/allow-sold/{id}', [App\Http\Controllers\SoldsController::class, 'AllowSold'])->name('AllowSold');

    Route::delete('/sales/delete-campaign/{id}', [App\Http\Controllers\CampaignsController::class, 'DeleteCampaign'])->name('DeleteCampaign');
    Route::delete('/sales/delete-tab-state/{id}', [App\Http\Controllers\TabStatesController::class, 'DeleteTabState'])->name('DeleteTabState');
    Route::delete('/sales/delete-state/{id}', [App\Http\Controllers\StatesController::class, 'DeleteState'])->name('DeleteState');
    Route::delete('/sales/delete-block/{id}', [App\Http\Controllers\BlockCampsController::class, 'DeleteBlock'])->name('DeleteBlock');
    Route::delete('/sales/delete-field/{id}', [App\Http\Controllers\FieldsController::class, 'DeleteField'])->name('DeleteField');
    Route::delete('/sales/delete-back/{id}', [App\Http\Controllers\BackOfficesController::class, 'DeleteBack'])->name('DeleteBack');
    Route::delete('/sales/delete-sup/{id}', [App\Http\Controllers\SupsController::class, 'DeleteSup'])->name('DeleteSup');
    Route::delete('/sales/delete-agent/{id}', [App\Http\Controllers\AgentsController::class, 'DeleteAgent'])->name('DeleteAgent');
    Route::delete('/sales/delete-sold/{id}', [App\Http\Controllers\SoldsController::class, 'DeleteSold'])->name('DeleteSold');

    Route::get('/sales/campaigns', [App\Http\Controllers\CampaignsController::class, 'index'])->name('dashboard.campain.index');
    Route::get('/sales/tab-states', [App\Http\Controllers\TabStatesController::class, 'index'])->name('dashboard.tab_state.index');
    Route::get('/sales/tab-states/{id}', [App\Http\Controllers\TabStatesController::class, 'indexWithId']);
    Route::get('/sales/states', [App\Http\Controllers\StatesController::class, 'index'])->name('dashboard.state.index');
    Route::get('/sales/states/{id}', [App\Http\Controllers\StatesController::class, 'indexWithId']);
    Route::get('/sales/blocks', [App\Http\Controllers\BlockCampsController::class, 'index'])->name('dashboard.block.index');
    Route::get('/sales/blocks/{id}', [App\Http\Controllers\BlockCampsController::class, 'indexWithId']);
    Route::get('/sales/fields', [App\Http\Controllers\FieldsController::class, 'index'])->name('dashboard.field.index');
    Route::get('/sales/fields/{id}', [App\Http\Controllers\FieldsController::class, 'indexWithId']);
    Route::get('/sales/backs', [App\Http\Controllers\BackOfficesController::class, 'index']);
    Route::get('/sales/backs/{id}', [App\Http\Controllers\BackOfficesController::class, 'indexWithId']);
    Route::get('/sales/sups', [App\Http\Controllers\SupsController::class, 'index']);
    Route::get('/sales/sups/{id}', [App\Http\Controllers\SupsController::class, 'indexWithId']);
    Route::get('/sales/agents', [App\Http\Controllers\AgentsController::class, 'index']);
    Route::get('/sales/agents/{id}', [App\Http\Controllers\AgentsController::class, 'indexWithId']);
    Route::get('/sales/solds/{id}', [App\Http\Controllers\SoldsController::class, 'index'])->name('dashboard.sold.index');
    Route::get('/sales/solds/{id}/{tab_state_id}', [App\Http\Controllers\SoldsController::class, 'indexWithTabStateId']);
    Route::get('/sales/solds/{id}/{tab_state_id}/{form_id}', [App\Http\Controllers\SoldsController::class, 'indexWithFormId']);

    Route::get('/sales/solds-export/{id}/{tab_state_id}', [App\Http\Controllers\SoldsController::class, 'export']);
});
// Route::middleware('auth')->group(function () {

//   //ADMINISTRACION DE USUARIOS - USUARIOS
//   Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
//   Route::get('/usuarios', 'UserController@index')->name('dashboard.user.index');
//   Route::post('/usuarios-store', 'UserController@store')->name('dashboard.user.store');
//   Route::post('/usuarios-eliminar', 'UserController@delete')->name('dashboard.user.delete');
//   Route::post('/usuarios-listar-grupos', 'UserController@listGroup')->name('dashboard.user.list.group');
//   Route::post('/usuarios-eliminar-grupos', 'UserController@deleteGroup')->name('dashboard.user.delete.group');
//   Route::post('/usuarios-agregar-grupos', 'UserController@addGroup')->name('dashboard.user.add.group');

// });
