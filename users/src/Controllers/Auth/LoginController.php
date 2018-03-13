<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Auth\Events\Logout;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\LoginActivity;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Login the user or return a error response if authentication fails
     *
     * @param Request $request
     * @return Response 
     */
    public function login(Request $request)
    {
        
       
        $this->validateLogin($request);

    
        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->generateToken();
    
            return response()->json($user->toArray(), Response::HTTP_OK);
        }
        
        return response()->json([
            'error' => 'Unauthorized login'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Logout the user
     *
     * @return Response 
     */
    public function logout(Request $request)
    {
       
        $user = Auth::guard('api')->user();
       
        if ($user) {
            LoginActivity::create([
                'user_id'       =>  $user->id,
                'user_agent'    =>  \Illuminate\Support\Facades\Request::header('User-Agent'),
                'ip_address'    =>  \Illuminate\Support\Facades\Request::ip(),
                'type'    =>  'out'
            ]);

            $user->api_token = null;
            $user->save();
        }
         
        Auth::logout();
       
        return response()->json(['message' => 'User logged out'], Response::HTTP_OK);
    }



}
