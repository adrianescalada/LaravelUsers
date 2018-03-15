<?php

namespace App\Http\Controllers;

use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Role;
use App\Permission;
use App\RolePermission;
use Auth;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role->role_permission('view_roles')){
        return view('roles.index');
        }else{
            abort(403);
        }
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Role::select('id', 'role')->get();

        return $datatables->collection($query)
            ->addColumn('action', 'roles.actions')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups = Permission::groupBy('group')->pluck('group')->toArray();
        $roleslist = Role::pluck('role','id')->toArray();
        
        $permissions = Permission::all();
       
        return view('roles.create')
        ->with('roleslist', $roleslist)
        ->with('groups', $groups)
        ->with('permissions', $permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'role' => 'required'
           
            
        ]);

        

        if(Auth::user()->role->id == 1){
			$role = Role::create($request->all());
			$permissions = Permission::all();
			foreach($permissions as $permission){
				if($request->get($permission->permission)){
					$role->role_permissions()->create(['permission_id' => $permission->id, 'level' => $request->get('level-'.$permission->id)]);
				}
        }
        Toastr::success('Rol create successfully');
        return redirect()->route('roles.index');
        }else{
            abort(403);
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
     public function show(Role $role)
     {
        if(Auth::user()->role->role_permission('show_roles')){ 
            return redirect()->route('roles.edit', $role->id);
        }else{
            abort(403);
        }
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        if(Auth::user()->role->role_permission('edit_roles')){
            // bloquear modificar permisos de los administradores
           //if(Auth::user()->role->id == 1 && $role->id != 1){
           if(Auth::user()->role->id == 1 && $role->id == 1){
           // dd($role);   
            $permissions = Permission::all();
            $groups = Permission::groupBy('group')->pluck('group')->toArray();
            $roleslist = Role::where('id',$role->id)->pluck('role','id')->toArray();
           
            return view('roles.edit')
            ->with('roleslist', $roleslist)
            ->with('roles', $role)
            ->with('groups', $groups)
            ->with('permissions', $permissions);

            }else{
                abort(403);
            }
        }else{
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'role' => 'required'
            
            
        ]);
    
        if(Auth::user()->role->id == 1){
        	$role->update($request->all());
			$permissions = Permission::all();
			foreach($permissions as $permission){
				if($request->get($permission->permission)){
					if($role->role_permissions->where('permission_id', $permission->id)->count() == 0){
						$role->role_permissions()->create(['permission_id' => $permission->id, 'level' => $request->get('level-'.$permission->id)]);
					}else{
						$role->role_permissions()->where('permission_id', $permission->id)->update(['level' => $request->get('level-'.$permission->id)]);
					}
				}elseif($role->role_permissions->where('permission_id', $permission->id)->count()){
					$role_permission = $role->role_permissions->where('permission_id', $permission->id)->first();
					$role_permission->delete();
				}
        }
        Toastr::success('Role updated successfully', 'Updated!');
        
        return redirect()->route('roles.index');
        }else{
            abort(403);
        }
            
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
     public function destroy(Role $role)
     {
        if(Auth::user()->role->role_permission('delete_roles')){
            if(Auth::user()->role->id == 1 && ($role->id != 1 || $role->id != 2 || $role->id != 3)){
                $role->delete();
                Toastr::success(trans('menu.roles') . trans('flash.deleted') . trans('flash.successfully'), 'success');
                
                        return redirect()->route('roles.index');
            }else{
                abort(403);
            }
        }else{
            abort(403);
        }
    }
 
     
}
