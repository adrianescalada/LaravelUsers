<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Models\Users\UserExtras;
use App\Models\ElectricMeters\ElectricMeters;

use App\Libs\ErrorHttp;

use \App\DataTables\FilteredTable as FilteredTable;

class InfoController extends Controller
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
     * Get data
     * 
     * @param Request $request
     * @return boolean
     */
    public function get(Request $request)
    {
        $user = Auth::id();
        if (!$user) return $this->ErrorHttp->notFound();

        $dataUser = User::find($user);
        $dataUserExtras = UserExtras::where('id_user', $user)->first();
        if (!$dataUserExtras || !$dataUser) return $this->ErrorHttp->notFound();

        return response()->json(['user' => $dataUser, 'info' => $dataUserExtras], Response::HTTP_OK);
    }

    /**
     * Set data
     * 
     * @param Request $request
     * @return boolean
     */
    public function set(Request $request)
    {
        // Get user id
        $user = Auth::id();
        

        // Check if exists. If exists: replace. Don't exists: create
        $userExtras = UserExtras::firstOrCreate(['id_user' => $user]);
        $userExtras->id_user = $user;
        $userExtras->firstname = $request->firstname;
        $userExtras->lastname = $request->lastname;
        $userExtras->dni = $request->dni;
        $userExtras->birthday = $request->birthday;
        $userExtras->email = $request->email;
        $userExtras->telephone = $request->telephone;
        $userExtras->telephone_alt = $request->telephone_alt;
        $userExtras->address = $request->address;
        $userExtras->cp = $request->cp;
        $userExtras->city = $request->city;
        $userExtras->province = $request->province;
        $userExtras->country = $request->country;

        // Get user model
        $userModel = Auth::user();
        
        /* check user counters */
        if ($request->counters) {
            $counters = $request->counters;
            foreach ($counters as $id) {
                $em = ElectricMeters::where('id',$id)->first();
                $userModel->assignElectricMeter($em);
            }
        }
        
        $saved = $userExtras->save();

        if (!$saved) return $this->ErrorHttp->internalServerError();
        
        return response()->json(['data' => 'OK'], Response::HTTP_CREATED);
    }



    /**
     *  ================ ADMIN ===================
     *
     */

    /**
     * Get data specific user
     * 
     * @param Request $request
     * @return boolean
     */
    public function getById(Request $request)
    {
        $user = $request->id;
        if (!$user) return $this->ErrorHttp->notFound();

        $dataUser = User::find($user);
        $dataUserExtras = UserExtras::where('id_user', $user)->first();
        if (!$dataUserExtras || !$dataUser) return $this->ErrorHttp->notFound();

        return response()->json(['user' => $dataUser, 'info' => $dataUserExtras], Response::HTTP_OK);
    }

    /**
     * Get summary all users
     *
     * @param void
     * @return response
     */
    public function dbGetSummaryAll()
    {
        // Nombre de las tablas
        $nameTableUser = (new User)->getTable();
        $nameTableUserExtras = (new UserExtras)->getTable();
        
        // Peticion a la base de datos
        $dbQuery = DB::table($nameTableUser)
            ->join($nameTableUserExtras, $nameTableUser.'.id', '=', $nameTableUserExtras.'.id_user')
            ->select(
                $nameTableUser.'.id', 
                $nameTableUser.'.name', 
                $nameTableUser.'.email',
                $nameTableUserExtras.'.firstname',
                $nameTableUserExtras.'.lastname',
                $nameTableUserExtras.'.city');

        return $dbQuery;
    }

    /**
     * Datatables: getSummaryAll
     * 
     * @param Request $request
     * @return void
     */
    public function datatablesGetSummaryAll(Request $request)
    {
        // Datatables envia el api_token a traves de la variable input
        if ($request->input) $request->api_token = $request->input->api_token;
        
        $dbSelect = $this->dbGetSummaryAll($request);

        return FilteredTable::getCustomFilterData($dbSelect);
    }

     /**
     * Datatables: getSummaryAll
     * 
     * @param Request $request
     * @return void
     */
    public function gridtablesGetSummaryAll(Request $request)
    {
        
        $dbData = $this->dbGetSummaryAll($request)->get();

        return response()->json($dbData, Response::HTTP_OK);
    }



}

