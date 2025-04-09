<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupCampainAuditDataSold extends Model
{

    use SoftDeletes;
    protected $table = 'groups_campains_audit_data_sold';
    protected $dates = ['deleted_at'];
}