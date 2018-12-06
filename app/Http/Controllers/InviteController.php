<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Invite;
use App\InviteText;
use App\Guest;
use App\Helper;
use GDText\Box;
use GDText\Color;
use Mpdf\Mpdf;
use \PHPQRCode\QRcode;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GuestController;

class InviteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function index()
    {
        //
    }

    /**
     * Cria um novo convite. Se o convite já existir o sistema retorna o existente
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\post(
     *     path="/invite",
     *     tags={"Invite"},
     *     summary="Create Invite",
     *     description="Create the User's Wedding Invite",
     *     @OA\RequestBody(
     *         description="Invite attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Invite"),
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
        $invite = Auth::user()->invite;
        if($invite){
            $invite->texts;
            $invite->images;
            return response($invite,201);
        }
        $invite = Auth::user()->invite()->save(new Invite(['bg_url'=>'/bgImgs/bg01.jpg']));
        $invite->texts;
        $invite->images;
        return response($invite,201);
    }

    /**
     * Cria um novo texto para o convite
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\post(
     *     path="/invite/text",
     *     tags={"Invite"},
     *     summary="Create Invite's text",
     *     description="Create the Invite's text message",
     *     @OA\RequestBody(
     *         description="Text attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InviteText"),
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
    public function createText(Request $request)
    {
        $user = Auth::user();
        $invite = $user->invite;
        // return $invite ;
        if ($invite!=null) {
            request()->validate([
                'text' => 'required|max:191',
                'width' => 'numeric',
                'height' => 'numeric',
                'x' => 'numeric',
                'y' => 'numeric',
                'layer' => 'integer',
                'font_id' => 'integer|nullable',
                'font_size' => 'numeric',
                'hexColor'=>'string'
            ]);

            return InviteText::create(['invite_id'=>$user->invite->id]+request()->all());
        } else {
            return response(['message'=>'Invite not found'],404);
        }
    }

    /**
     * Cria um novo texto para o convite
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\put(
     *     path="/invite/text/{text}",
     *     tags={"Invite"},
     *     summary="Update Invite's text",
     *     description="Update the Invite's text message",
     *     @OA\Parameter(
     *         name="text",
     *         in="path",
     *         description="Invite's Id",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
     *     @OA\RequestBody(
     *         description="Text attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InviteText"),
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
    public function updateText(InviteText $text)
    {
        if(Auth::user()->invite->id!=$text->invite->id){
            return response(['message'=>'you are not authorized to alter this text'],401);
        }
        request()->validate([
            'text' => 'required|max:191',
            'width' => 'numeric',
            'height' => 'numeric',
            'x' => 'numeric',
            'y' => 'numeric',
            'layer' => 'integer',
            'font_id' => 'integer|nullable',
            'font_size' => 'numeric',
            'hexColor'=>'string'
        ]);

        $text->text = request()->text;
        $text->width = request()->width;
        $text->height = request()->height;
        $text->x = request()->x;
        $text->y = request()->y;
        $text->layer = request()->layer;
        $text->font_id = request()->font_id;
        $text->font_size = request()->font_size;
        $text->hexColor = request()->hexColor !=null ? request()->hexColor:$text->hexColor;

        $text->save();
        return $text;
    }

    /**
     * @OA\delete(
     *     path="/invite/text/{text}",
     *     tags={"Invite"},
     *     summary="Delete Invite's Text",
     *     description="Delete Invite's Text by Invite's Id ",
     *     @OA\Parameter(
     *         name="text",
     *         in="path",
     *         description="Invite's Id",
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
    public function deleteText(InviteText $text)
    {
        if(!Auth::user()->invite){
            return response(['message'=>'you are not authorized to delete this text'],401);
        }
        if(Auth::user()->invite->id!=$text->invite->id){
            return response(['message'=>'you are not authorized to delete this text'],401);
        }

        $text->delete();
        return response(['message'=>'Invite Text deleted successful'],200);
    }

    /**
     * @OA\put(
     *     path="/invite/image/{image}",
     *     tags={"Invite"},
     *     summary="Update Image Url",
     *     description="If the User doesn't have a Invite, it will create a New Invite, or Update the existing Invite",
     *     @OA\Parameter(
     *         name="text",
     *         in="path",
     *         description="Invite's Id",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
     *     @OA\RequestBody(
     *         description="Invite attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Invite"),
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
    public function update()
    {
        if(!Auth::user()->invite){
            Auth::user()->invite()->save(new Invite());
        }

        Auth::user()->invite->bg_url = request()->bg_url;
        Auth::user()->invite->save();
        return Auth::user()->invite;
    }

    /**
     * @OA\get(
     *     path="/user/invites",
     *     tags={"User"},
     *     summary="List Invites",
     *     description="Return the List of all Invites per User",
     *     parameters="",
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="array",
     *            @OA\Items(ref="#/components/schemas/Invite")
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
    public function getAll()
    {
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Origin: *');
        $dpi = request()->query('dpi')!==null ? request()->query('dpi') : 150;
        $html='';
        $mpdf = new Mpdf(['dpi'=>$dpi]);
        $mpdf->showImageErrors = true;
        foreach (Auth::user()->guests as $guest) {
            ob_start();
            imagepng(Helper::drawInvite(Auth::user()->invite,$guest));
            $contents =  ob_get_contents();
            ob_end_clean();
            $mpdf->imageVars[$guest->id.'-img'] = $contents;
            $w = Helper::cmToPixels(9,$dpi);
            $html = $html.'<img style="width:'.$w.'px;margin:10px;" src="var:'.$guest->id.'-img" />';
            // code...
        }
        $mpdf->WriteHTML($html);
        return response($mpdf->Output(),401)->header('Content-Type', 'application/pdf');
    }

    /**
     * @OA\get(
     *     path="/guest/{guest}/invite",
     *     tags={"Guest"},
     *     summary="Show Invite by id",
     *     description="Show Invite to Guest by Guest Id",
     *     @OA\Parameter(
     *         name="guest",
     *         in="path",
     *         description="Guest Id",
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
    public function get(Guest $guest)
    {
        if(!Storage::disk('local')->exists($guest->user->id.'/'.$guest->id)){
            Storage::disk('local')->makeDirectory($guest->user->id.'/'.$guest->id);
        }
        if(!Storage::disk('local')->exists($guest->user->id.'/'.$guest->id.'/qrcode.png')){
            QRcode::png($guest->id, './storage/app/'.$guest->user->id.'/'.$guest->id."/qrcode.png",'H', 10, 1);
            GuestController::DrawImage('./storage/app/'.$guest->user->id.'/'.$guest->id."/qrcode.png",$guest->id);
        }
        header('Content-Type: image/png');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Origin: *');
        imagepng(Helper::drawInvite(Auth::user()->invite,$guest));
        return response(null,200)->header('Content-Type', 'image/png');
    }

}
