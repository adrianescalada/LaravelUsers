<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Role;

use Illuminate\Support\Facades\Auth;

use App\Libs\ErrorHttp;

class AccountAdminController extends Controller
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
     * Returns true if the user info is valid
     * 
     * @param Request $request
     * @return boolean
     */
    private function validateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users',
            'password' => 'sometimes|required|string|min:6',
        ]);

        return (! $validator->fails());
    }

    /**
     * Create a new user to the database
     *
     * @param Request $request
     * @return Response
     */
    public function createUser(Request $request)
    {
        // Validate data
        if (!$this->validateData($request)) return $this->ErrorHttp->badInput();
        
        // New user
        $user = new User;
        if (!$user->save()) return $this->ErrorHttp->badInput();
        $user->setData($request);
        // Generate token
        $user->generateToken();
        
        //TODO revisar Create role
        //$role = Role::where('role','user')->first();
        
        //$user->assignRole($role);    

        return response()->json($user, Response::HTTP_OK);
    }

     /**
     * Delete a specific user
     *
     * @param $request
     * @return void
     */
    public function deleteById (Request $request) {
        $id = $request->id;
        if (!$id) return $this->ErrorHttp->notFound();

        $user = User::find($id);
        if(!$user) return $this->ErrorHttp->notFound();

        $delete = $user->delete();
        if (!$delete) return $this->ErrorHttp->internalServerError();
        
        return response()->json("ok", Response::HTTP_OK);        
    }

    /**
     * Updates password
     *
     * @param Request $request
     * @return Response
     */
    public function updatePasswordById(Request $request)
    {
        // Get user id
        $id = $request->id;
        if (!$id) return $this->ErrorHttp->notFound();
        
        // Find user
        $user = User::find($id);
        if (!$user) return $this->ErrorHttp->notFound();

        // Validate data
        if (!$this->validateData($request)) return $this->ErrorHttp->badInput();
        
        // Save data
        $user->updatePassword($request);
        if (!$user->save()) return $this->ErrorHttp->badInput();
               
        return response()->json($user, Response::HTTP_OK);
    }

}
