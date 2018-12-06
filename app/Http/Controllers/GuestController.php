<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guest;
use App\GuestsLike;
use App\MatchesConversation;
use Illuminate\Support\Facades\Auth;
use \PHPQRCode\QRcode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Notificate;

class GuestController extends Controller
{
    
    /**
     * @OA\post(
     *     path="//guest",
     *     tags={"Guest"},
     *     summary="Create Guest",
     *     description="Create wedding guest by User",
     *     @OA\RequestBody(
     *         description="Guest attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Guest"),
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
        // header('Content-type: image/png');
        request()->validate([
            'email'=>'email',
            'phone'=>'min:9'
        ]);
        // return Auth::user()->id;
        $user=Auth::user();
        $request = ['user_id'=>$user->id,'id'=>Guest::generateId($user->getInitials())]+request()->all();
        $guest = Guest::create($request);
        Storage::disk('local')->makeDirectory($user->id.'/'.$guest->id);
        QRcode::png($guest->id, './storage/app/'.$user->id.'/'.$guest->id."/qrcode.png",'H', 10, 1);
        self::DrawImage('./storage/app/'.$user->id.'/'.$guest->id."/qrcode.png",$guest->id);
        // imagedestroy($jpg_image);
        // file_put_contents('./storage/app/'.$user->id.'/'.$guest->id."/qrcode.png", $jpg_image);
        return response($guest,201);
    }

    public static function DrawImage($imgUrl,$id)
    {

        $w = getimagesize($imgUrl);

        $png_image = imagecreatefrompng($imgUrl);

        $im = imagecreatetruecolor($w[0], $w[1]+30);

        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 0, 0, 0);
        // imagefilledrectangle($im, 0, 0, 399, 29, $white);
        // Replace path by your own font path
        $font =  base_path() . '/public/fonts/Bebas.ttf';
        $textbox = self::calculateTextBox($id,$font,20,0);
        // Add some shadow to the text
        // var_dump($textbox);
        imagecopymerge($im, $png_image, 0, 0, 0, 0, $w[0], $w[1]+30, 100);
        imagettftext($im, 20, 0, ($w[0]/2)-($textbox['width']/2), $w[1]+20, $black, $font, $id);

        // Add the text
        // imagettftext($im, 20, 0, 10, 20, $black, $font, $text);

        // Using imagepng() results in clearer text compared with imagejpeg()
        imagepng($im,$imgUrl);
        imagedestroy($im);
    }

    public static function calculateTextBox($text,$fontFile,$fontSize,$fontAngle) {
        /************
        simple function that calculates the *exact* bounding box (single pixel precision).
        The function returns an associative array with these keys:
        left, top:  coordinates you will pass to imagettftext
        width, height: dimension of the image you have to create
        *************/
        $rect = imagettfbbox($fontSize,$fontAngle,$fontFile,$text);
        $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
        $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
        $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
        $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));

        return array(
            "left"   => abs($minX) - 1,
            "top"    => abs($minY) - 1,
            "width"  => $maxX - $minX,
            "height" => $maxY - $minY,
            "box"    => $rect
        );
    }

    public function getWedding()
    {
        $user = Guest::guest()->user;
        $user->address;
        $user->gift_lists;
        return $user;
    }

    public function singles()
    {
        $guest = Guest::guest();
        $user = $guest->user;
        return $user->guests()
            ->where('is_on_singles_meeting','=',1)
            ->where('id','!=',$guest->id)
            ->where('gender_id','=',$guest->want_gender_id)
            ->get()->diff($guest->guests_liked);
    }

    public function update(Guest $guest)
    {
        request()->validate([
            'email'=>'email',
            'phone'=>'min:9',
            'gender_id'=>'integer',
            'want_gender_id'=>'integer',
            'age'=>'integer',
            'image'=>'image',
            'image_1'=>'image',
            'image_2'=>'image',
            'image_3'=>'image'
        ]);

        if (Guest::guest()->user_id == $guest->user_id) {
            $data = request()->all();
            if(isset(request()->image)){
                $imageUrl=$guest->user_id.'/'.$guest->id.'/profile/';
                $imageName = $guest->id.'.'.request()->image->extension();

                Storage::disk('local')->putFileAs($imageUrl, request()->image, $imageName);
                $data=['profile_img'=>$imageUrl.$imageName]+$data;
            }

            if(isset(request()->image_1)){
                $imageUrl=$guest->user_id.'/'.$guest->id.'/image/';
                $imageName = $guest->id.'-image1.'.request()->image_1->extension();

                Storage::disk('local')->putFileAs($imageUrl, request()->image_1, $imageName);
                $data=['photo1_url'=>$imageUrl.$imageName]+$data;
            }

            if(isset(request()->image_2)){
                $imageUrl=$guest->user_id.'/'.$guest->id.'/image/';
                $imageName = $guest->id.'-image2.'.request()->image_2->extension();

                Storage::disk('local')->putFileAs($imageUrl, request()->image_2, $imageName);
                $data=['photo2_url'=>$imageUrl.$imageName]+$data;
            }

            if(isset(request()->image_3)){
                $imageUrl=$guest->user_id.'/'.$guest->id.'/image/';
                $imageName = $guest->id.'-image3.'.request()->image_3->extension();

                Storage::disk('local')->putFileAs($imageUrl, request()->image_3, $imageName);
                $data=['photo3_url'=>$imageUrl.$imageName]+$data;
            }

            $guest->fill($data);
            $guest->save();

            return $guest;
        }
    }


    public function image(Guest $guest)
    {
        if(Guest::guest()!=null){
            if(Guest::guest()->user_id == $guest->user_id){
                return response()->file('storage/app/'.$guest->profile_img);
            }
        }
        if(Auth::user()->id == $guest->user_id){
            return response()->file('storage/app/'.$guest->profile_img);
        }

        return response(['message'=>'You are not allowed see this image'],401);
    }

    public function image1(Guest $guest)
    {
        if(Guest::guest()!=null){
            if(Guest::guest()->user_id == $guest->user_id){
                if($guest->photo1_url==null){
                    return response(['message'=>'image1 no seted'],400);
                }
                return response()->file('storage/app/'.$guest->photo1_url);
            }
        }
        if(Auth::user()->id == $guest->user_id){
            if($guest->photo1_url==null){
                return response(['message'=>'image1 no seted'],400);
            }
            return response()->file('storage/app/'.$guest->photo1_url);
        }

        return response(['message'=>'You are not allowed see this image'],401);
    }

    public function image2(Guest $guest)
    {
        if(Guest::guest()!=null){
            if(Guest::guest()->user_id == $guest->user_id){
                if($guest->photo2_url==null){
                    return response(['message'=>'image1 no seted'],400);
                }
                return response()->file('storage/app/'.$guest->photo2_url);
            }
        }
        if(Auth::user()->id == $guest->user_id){
            if($guest->photo2_url==null){
                return response(['message'=>'image2 no seted'],400);
            }
            return response()->file('storage/app/'.$guest->photo2_url);
        }

        return response(['message'=>'You are not allowed see this image'],401);
    }

    public function image3(Guest $guest)
    {
        if(Guest::guest()!=null){
            if(Guest::guest()->user_id == $guest->user_id){
                if($guest->photo3_url==null){
                    return response(['message'=>'image2 no seted'],400);
                }
                return response()->file('storage/app/'.$guest->photo3_url);
            }
        }
        if(Auth::user()->id == $guest->user_id){
            if($guest->photo3_url==null){
                return response(['message'=>'image2 no seted'],400);
            }
            return response()->file('storage/app/'.$guest->photo3_url);
        }

        return response(['message'=>'You are not allowed see this image'],401);
    }

    public function uploadPhoto(Guest $guest)
    {

        if (Guest::guest()->user_id == $guest->user_id) {

            if(isset(request()->image1)){
                $imageUrl=$guest->user_id.'/'.$guest->id.'/photo/';
                $imageName = $guest->id.'1.'.request()->image1->extension();

                Storage::disk('local')->putFileAs($imageUrl, request()->image1, $imageName);
                $guest->photo1_url = $imageUrl.$imageName;
            }

            if(isset(request()->image2)){
                $imageUrl=$guest->user_id.'/'.$guest->id.'/photo/';
                $imageName = $guest->id.'2.'.request()->image2->extension();

                Storage::disk('local')->putFileAs($imageUrl, request()->image2, $imageName);
                $guest->photo2_url = $imageUrl.$imageName;
            }

            if(isset(request()->image3)){
                $imageUrl=$guest->user_id.'/'.$guest->id.'/photo/';
                $imageName = $guest->id.'3.'.request()->image3->extension();

                Storage::disk('local')->putFileAs($imageUrl, request()->image3, $imageName);
                $guest->photo3_url = $imageUrl.$imageName;
            }

            $guest->save();

            return $guest;
        }
    }

    public function index()
    {
        return Auth::user()->guests;
    }

    public function confirm()
    {
        $guest = Guest::guest();
        $guest->confirmed = true;
        $guest->save();
        return $guest;
    }

    public function get()
    {
        return Guest::guest();
    }

    public function unconfirm()
    {
        $guest = Guest::guest();
        $guest->confirmed = false;
        $guest->save();
        return $guest;
    }

    public function guestLike(Guest $guest)
    {
        $authGuest=Guest::guest();
        if ($authGuest->user_id != $guest->user_id) {
            return response(['error'=>'You are not allowed like this guest'],401);
        }

        request()->validate([
            'liked'=>'boolean',
        ]);

        if(count($authGuest->guests_liked()->where('id','=',$guest->id)->get())==0){
            $authGuest->guests_liked()->attach($guest,['liked'=>request()->liked]);
            if(count($guest->guests_liked()->where('id','=',$authGuest->id)->get())>0){
                $authGuest->matchs1()->attach($guest);
                return response(['message'=>'guest liked successful','match'=>true],200);
            }
            return response(['message'=>'guest liked successful','match'=>false],200);
        }
        return response(['message'=>'you alredy like this guest'],400);


    }

    public function delete(Guest $guest)
    {
        if (Auth::user()->id != $guest->user_id) {
            return response(['error'=>'You are not allowed like this guest'],401);
        }

        $guest->delete();

        return response(['message'=>'Guest delete successful'],200);
    }

    public function guestMatch(Guest $match)
    {
        if (Guest::guest()->user_id != $match->user_id) {
            return response(['error'=>'You are not allowed match this guest'],401);
        }

        request()->validate([
            'message'=>'required|min:1'
        ]);

        $matchesConversation = new MatchesConversation;

        $matchesConversation->guest_id = Guest::guest()->id;
        $matchesConversation->match_id = $match->id;
        $matchesConversation->message = request()->message;
        $matchesConversation->save();

        $title = "mensagem";

        Notificate::NotificateUser(Guest::guest()->fcm_device_token ,$title, $matchesConversation->message);
        // dd(Guest::guest()->fcm_device_token);
        // dd($matchesConversation->message);
        // dd($title);
        return response(['message'=>'Guest Match successful'],200);
        
    }

    public function guestConversations()
    {
        $sql = 'select m2.id, m2.guest_id, m2.match_id, name, message from
        	(select
        			max(id) as id,
        			if(guest_id = "' . Guest::guest()->id . '", match_id, guest_id) as id_conversation
        		from
        			pro_wedding_test.matches_conversations m
        		where
        			guest_id = "' . Guest::guest()->id . '" or match_id = "' . Guest::guest()->id . '"
        		group by id_conversation) as dados
        	inner join pro_wedding_test.matches_conversations m2 on dados.id = m2.id
            inner join pro_wedding_test.guests g on m2.match_id = g.id
        order by m2.id desc;';

        

        return DB::select($sql);
    }

    public function guestConversationsMatch(Guest $match)
    {
        $sql = 'select
            	g.id as from_id, g.name as from_name, g2.id as to_id, g2.name as to_name, m.id, m.message
            from
            	pro_wedding_test.matches_conversations m
                inner join pro_wedding_test.guests g on m.guest_id = g.id
                inner join pro_wedding_test.guests g2 on m.match_id = g2.id
            where
                (guest_id = "' . Guest::guest()->id . '" or match_id = "' . $match->id . '") and
                (g.id = "' . Guest::guest()->id . '" and g2.id = "' . $match->id . '") or
                (g2.id = "' . Guest::guest()->id . '" and g.id = "' . $match->id . '")
            order by m.id;';

        

        return DB::select($sql);
    }

    public function registerFcmToken()
    {
        request()->validate([
            'token'=>'required'
        ]);
        $guest = Guest::guest();
        $guest->fcm_device_token = request()->token;

        $guest->save();

        return response(['message'=>'Token saved successful'],200);
    }
}


