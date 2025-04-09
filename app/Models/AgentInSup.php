<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentInSup extends Model
{

    use SoftDeletes;
    protected $table = 'agents_in_sups';
    protected $dates = ['deleted_at'];
}