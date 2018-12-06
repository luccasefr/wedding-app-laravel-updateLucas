<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Action;
use Illuminate\Support\Facades\Auth;
use App\Notificate;

class ActionController extends Controller
{
    /**
     * @OA\post(
     *     path="/action",
     *     tags={"Action"},
     *     summary="Create Chroma Image",
     *     description="Create Chroma image",
     *     @OA\RequestBody(
     *         description="Adding Chroma Image to Database",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ChromaImage"),
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         
     *         
     *     ),
     *     
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     *
     * 
     */
    public function create()
    {
        request()->validate([
            'title'=>'required',
            'expense_value'=>'numeric',
            'expense_date'=>'date_format:d/m/Y',
            'notify_date_from'=>'date_format:d/m/Y',
            'notify_date_to'=>'date_format:d/m/Y'
        ]);

        $data=request()->all()+['user_id'=>Auth::user()->id];

        if(isset(request()->expense_value) && isset(request()->expense_date)){
            $data+=['expense'=>true];
        }elseif ((!isset(request()->expense_value) && isset(request()->expense_date)) || (isset(request()->expense_value) && !isset(request()->expense_date))) {
            return response(['message'=>'expense_value or expense_date is not set'],400);
        }

        if(isset(request()->notify_date_from) && isset(request()->notify_date_to) && isset(request()->message)){
            $data+=['notify_guests'=>true];
        }else if (((!isset(request()->notify_date_from) || !isset(request()->notify_date_to)) && isset(request()->message)) || ((isset(request()->notify_date_from) || isset(request()->notify_date_to)) && !isset(request()->message))) {
            return response(['message'=>'notify_date_from, notify_date_to or message is not set'],400);
        }
        

        return Action::create($data);
    }

        /**
     * @OA\Get(
     *      path="/actions",
     *      tags={"Action"},
     *      summary="Get list of Actions",
     *      description="Returns list of Actions",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *            type="array",
     *            @OA\Items(ref="#/components/schemas/Action")
     *         ),
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     */
    public function index()
    {
        return Auth::user()->actions;
    }
}
