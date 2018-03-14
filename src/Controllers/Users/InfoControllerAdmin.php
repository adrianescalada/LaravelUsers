<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Models\Users\UserExtras;

use App\Libs\ErrorHttp;

use \App\DataTables\FilteredTable as FilteredTable;

class InfoControllerAdmin extends Controller
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


}

