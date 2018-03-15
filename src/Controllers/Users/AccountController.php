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

class AccountController extends Controller
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
     * Updates all user data
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        // Get user id
        $id = Auth::id();
        if (!$id) return $this->ErrorHttp->notFound();
        
        // Find user
        $user = User::find($id);
        if (!$user) return $this->ErrorHttp->notFound();

        // Validate data
        if (!$this->validateData($request)) return $this->ErrorHttp->badInput();
        
        // Save data
        $user->setData($request);
        if (!$user->save()) return $this->ErrorHttp->badInput();
               
        return response()->json(['updated' => true], Response::HTTP_OK);
    }


    /**
     * Updates password
     *
     * @param Request $request
     * @return Response
     */
    public function updatePassword(Request $request)
    {
        // Get user id
        $id = Auth::id();
        if (!$id) return $this->ErrorHttp->notFound();
        
        // Find user
        $user = User::find($id);
        if (!$user) return $this->ErrorHttp->notFound();

        // Validate data
        if (!$this->validateData($request)) return $this->ErrorHttp->badInput();
        
        // Save data
        $user->updatePassword($request);
        if (!$user->save()) return $this->ErrorHttp->badInput();
               
        return response()->json(['updated' => true], Response::HTTP_OK);
    }

    /**
     * Returns a specific user data by id or the full user list
     *
    * @param Request $request
     * @return User
     */
    public function get(Request $request)
    {
        // Get user id
        $id = Auth::id();
        if (!$id) return $this->ErrorHttp->notFound();

        // Find user
        $user = User::find($id);
        if (!$user) return $this->ErrorHttp->notFound();
        
        // Get role
        $roleName = $user->roles()->first()->name;
        $user->role = $roleName;

        return response()->json(['user' => $user], Response::HTTP_OK);
    }

}
