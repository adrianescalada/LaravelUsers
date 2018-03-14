<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Role;
use \App\DataTables\FilteredTable as FilteredTable;

use App\Libs\ErrorHttp;

class RoleController extends Controller
{
    protected $ErrorHttp;
    
    /**
     * Start controller
     *
     * @return void
     */
    public function __construct(ErrorHttp $ErrorHttp) {
        $this->ErrorHttp = $ErrorHttp;
    }

    /**
     * Assign a specific user role
     *
     * @param Request $request
     * @return Response
     */
    public function assign(Request $request)
    {
        // Check input
        if (! $request->user_id || ! $request->role) return $this->ErrorHttp->badInput();

        // Find user
        $user = User::find($request->user_id);
        if (!$user) return $this->ErrorHttp->notFound();

        // Search role
        $role = Role::where('name', $request->role)->first();
        if (!$role && $user->hasRole($request->role)) return $this->ErrorHttp->badInput();
                    
        // Assign role
        $user->assignRole($role);               
        return response()->json(['updated' => true], Response::HTTP_OK);
    }

     /**
     * Remove a specific user role
     *
     * @param Request $request
     * @return Response
     */
    public function remove(Request $request)
    {
        // Check input
        if (! $request->user_id || ! $request->role) return $this->ErrorHttp->badInput();

        // Find user
        $user = User::find($request->user_id);
        if (!$user) return $this->ErrorHttp->notFound();

        // Search role
        $role = Role::where('name', $request->role)->first();
        if (!$role || !$user->hasRole($request->role)) return $this->ErrorHttp->badInput();
            
        // Remove role
        $user->removeRole($role);           
        return response()->json(['updated' => true], Response::HTTP_OK);
    }
}
