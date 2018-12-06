<?php

namespace App\Http\Controllers;

use App\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use \PHPQRCode\QRcode;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GuestController;

class QrCodeImageController extends Controller
{
    public function get(Guest $guest)
    {
        if(Auth::user()->id!=$guest->user->id){
            return response(['message'=>'you are not authorized to see this qrcode'],401);
        }
        if(!Storage::disk('local')->exists($guest->user->id.'/'.$guest->id)){
            Storage::disk('local')->makeDirectory($guest->user->id.'/'.$guest->id);
        }
        if(!Storage::disk('local')->exists($guest->user->id.'/'.$guest->id.'/qrcode.png')){
            QRcode::png($guest->id, './storage/app/'.$guest->user->id.'/'.$guest->id."/qrcode.png",'H', 10, 1);
            GuestController::DrawImage('./storage/app/'.$guest->user->id.'/'.$guest->id."/qrcode.png",$guest->id);
        }
        return response()->file('storage/app/'.$guest->user->id.'/'.$guest->id.'/qrcode.png');
    }

    public function getPdf(Guest $guest)
    {
        if(!Storage::disk('local')->exists($guest->user->id.'/'.$guest->id)){
            Storage::disk('local')->makeDirectory($guest->user->id.'/'.$guest->id);
        }
        if(!Storage::disk('local')->exists($guest->user->id.'/'.$guest->id.'/qrcode.png')){
            QRcode::png($guest->id, './storage/app/'.$guest->user->id.'/'.$guest->id."/qrcode.png",'H', 10, 1);
            GuestController::DrawImage('./storage/app/'.$guest->user->id.'/'.$guest->id."/qrcode.png",$guest->id);
        }
        ob_clean();
        ob_start();
        if(Auth::user()->id!=$guest->user->id){
            return response(['message'=>'you are not authorized to see this qrcode'],401);
        }
        $mpdf = new Mpdf();
        // $mpdf->showImageErrors = true;
        $mpdf->imageVars['myvariable'] = file_get_contents('storage/app/'.$guest->user->id.'/'.$guest->id.'/qrcode.png');
        $html = '<img src="var:myvariable" />';
        $mpdf->WriteHTML($html);
        ob_end_flush();
        return response($mpdf->Output(),200)->header('Content-Type', 'application/pdf');
    }

    function cmToPixels($cm,$dpi)
    {
        return ($cm*$dpi)/2.5;
    }

    public function getAll()
    {
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Origin: *');
        $size = request()->query('px');
        $dpi = request()->query('dpi')!==null ? request()->query('dpi') : 150;
        if(request()->query('cm')!==null){
            $size = $this->cmToPixels(request()->query('cm'),$dpi);
        }

        ob_clean();
        ob_start();
        $user = Auth::user();
        $guests = $user->guests;
        $mpdf = new Mpdf(['dpi'=>$dpi]);
        $mpdf->showImageErrors = true;
        $html='';
        foreach ($guests as $guest) {
            $mpdf->imageVars[$guest->id.'-img'] = file_get_contents('storage/app/'.$user->id.'/'.$guest->id.'/qrcode.png');
            $w = getimagesize('storage/app/'.$user->id.'/'.$guest->id.'/qrcode.png');
            $html = $html.'<img style="width:'.(isset($size) ? $size : $w[0]).'px;margin:10px;" src="var:'.$guest->id.'-img" />';
        }
        $mpdf->WriteHTML($html);
        ob_end_flush();
        return response($mpdf->Output(),200)->header('Content-Type', 'application/pdf');
    }
}
