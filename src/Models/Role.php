<?php

namespace wdna\laravel_users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
	
	protected $dates = ['deleted_at'];
	
	protected $fillable = [
		'role','guard_name'
	];
	
	public function users()
	{
		return $this->hasMany('App\User');
	}
	
	public function role_permissions()
	{
		return $this->hasMany('App\RolePermission');
	}
	
	public function role_permission($permission_slug)
	{
		$permission = Permission::where('permission', $permission_slug)->first();
		return $this->hasMany('App\RolePermission')->where('permission_id', $permission->id)->first();
	}
}
