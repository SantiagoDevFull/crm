<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupCampainExportSold extends Model
{

    use SoftDeletes;
    protected $table = 'groups_campains_export_solds';
    protected $dates = ['deleted_at'];
}