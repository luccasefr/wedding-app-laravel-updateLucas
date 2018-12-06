<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Address;
use App\Font;

class UserController extends Controller
{
     /**
     * @OA\Get(
     *     path="/user",
     *     tags={"User"},
     *     summary="List users",
     *     description="Returns the list of all users",
     *     operationId="getPetById",
     *     externalDocs="",
     *     parameters="",
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
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
    public function get()
    {
        $user=Auth::user();
        $user->gift_lists;
        $user->address;
        return $user;
    }

    /**
     * @OA\post(
     *     path="/user",
     *     tags={"User"},
     *     summary="List users",
     *     description="Returns the list of all users",
     *     operationId="getPetById",
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplier"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     *
     * @param int $id
     */
    public function update(Request $request)
    {
        request()->validate([
            'email' => 'email',
            'wedding_date' => 'dateFormat:d/m/Y H:i',
            'want_to_spent' => 'numeric',
            'waiting_guests' => 'integer'
        ]);

        $user = Auth::user();
        $wedding_address_id = 0;

        if ($user) {

            if (!empty(request()->street) ||
                !empty(request()->number) ||
                !empty(request()->neighborhood) ||
                !empty(request()->city) ||
                !empty(request()->state) ||
                !empty(request()->cep))
            {
                request()->validate([
                    'street' => 'required|max:191',
                    'number' => 'required|max:191',
                    'complement'=> 'max:191',
                    'neighborhood' => 'required|max:191',
                    'city' => 'required|max:191',
                    'state' => 'required|min:2|max:191',
                    'cep' => 'required|min:8|max:191'
                ]);

                if($user->address!=null){
                    $address = Address::find($user->address->id);
                    $address->street = request()->street;
                    $address->number = request()->number;
                    $address->neighborhood = request()->neighborhood;
                    $address->city = request()->city;
                    $address->state = request()->state;
                    $address->cep = request()->cep;

                    if ($address->save()) {
                        $wedding_address_id = $address->id;
                    }
                    $user->fill(['wedding_address_id'=>$wedding_address_id]);
                }else {
                    $address = Address::create(request()->all());
                    $user->fill(['wedding_address_id'=>$address->id]);
                }
            }

            $user->fill(request()->all());

            $user->save();
            return $user;
        }
    }

    
    public function fonts()
    {
        return Font::orderBy('name')->get();
    }
}
