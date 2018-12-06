<?php

namespace App\Http\Controllers;

use App\Event;
use App\Address;
use App\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * @OA\post(
     *     path="/user/event",
     *     tags={"User"},
     *     summary="Create User's Event",
     *     description="Create User's Event of Wedding",
     *     @OA\RequestBody(
     *         description="Saving Event to Database with Adress",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Event"),
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
            'name'=>'required|string',
            'date'=>'dateFormat:d/m/Y H:i',
            'street'=>'required|string',
            'number'=>'required|string',
            'neighborhood'=>'required|string',
            'city'=>'required|string',
            'state'=>'required|string',
            'cep'=>'required|string'
        ]);

        $address = Address::create(request()->all());
        return Event::create(['address_id'=>$address->id,'user_id'=>Auth::user()->id]+request()->all());
    }

    /**
     * @OA\delete(
     *     path="/event/{event}",
     *     tags={"Event"},
     *     summary="Delete Chroma Image",
     *     description="Delete Chroma image of the User",
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         description="Id of the event",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
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
    public function delete(Event $event)
    {
        if(Auth::user()->id!=$event->user_id){
            return response(['message'=>'you are not authorized to delete this event list'],401);
        }

        $event->delete();
        return response(['message'=>'Event deleted successful'],200);
    }

    /**
     * @OA\get(
     *     path="/events",
     *     tags={"Event"},
     *     summary="List Events",
     *     description="Return the List of all Events of related to the Wedding",
     *     parameters="",
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="array",
     *            @OA\Items(ref="#/components/schemas/Event"),
     *         ),
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
    public function index()
    {
        $user = Auth::user();
        if($user){
            return $user->events;
        }else {
            $guest = Guest::guest();
            return $guest->events;
        }
    }

    /**
     * @OA\post(
     *     path="/event/{event}/confirm",
     *     tags={"Event"},
     *     summary="Confirm Presence on wedding",
     *     description="Confirm presence on Event by event's id if you are a guest",
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         description="Id of the event",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
     *     @OA\RequestBody(
     *         description="Saving the wedding confirmed status of the guest",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EventGuest"),
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
    public function confirm(Event $event)
    {
        if($event->guests()->updateExistingPivot(Guest::guest()->id,['confirmed'=>true])){
            $event->guests;
            return $event;
        }else {
            return response(['error'=>'you are not invited to this event'],401);
        }
    }

    /**
     * @OA\get(
     *     path="/event/{event}/invite",
     *     tags={"Event"},
     *     summary="Return the updated list of invited Guest",
     *     description="Return the updated list of invited and confirmed Guests by event's id, for easy visualization on the manager project",
     *     @OA\Parameter(
     *         name="event",
     *         in="path",
     *         description="Id of the event",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
     *     @OA\RequestBody(
     *         description="Saving the wedding confirmed status of the guest",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EventGuest"),
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
    public function updateInvites(Event $event)
    {
        request()->validate([
            'guest_ids'=>'required' 
        ]);

        if(Auth::user()->id!=$event->user_id){
            return response(['error'=>'this event is not yours'],401);
        }
        
        $event->guests()->detach();
        $ids=json_decode(request()->guest_ids);
        foreach ($ids as $id) {
            
            $event->guests()->attach(Guest::find($id));
        }
        $event->guests;
        return $event;
    }

    
}
