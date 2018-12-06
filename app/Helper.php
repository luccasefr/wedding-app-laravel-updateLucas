<?php

namespace App;
use App\Invite;
use GDText\Box;
use GDText\Color;
use Mpociot\Firebase\SyncsWithFirebase;

class Helper
{

    
    public static function cmToPixels($cm,$dpi)
    {
        return ($cm*$dpi)/2.5;
    }

    public static function resize_image($file, $w, $h, $crop=FALSE)
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }

    public static function drawInvite(Invite $invite,$guest)
    {
        // $invite = Auth::user()->invite;
        $texts = $invite->texts()->orderBy('layer')->get();
        $dpi = 150;
        $width = Helper::cmToPixels(9,$dpi);
        $height = Helper::cmToPixels(5,$dpi);

        $img = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);

        self::drawBg($invite->bg_url,$img,$width,$height);

        self::drawTexts($texts,$img,$dpi);

        $images = $invite->images()->orderBy('layer')->get();

        self::drawImages($images,$img,$dpi);

        self::drawQrCode($guest,$img,$dpi);

        return $img;
    }

    static function drawQrCode($guest,$img,$dpi)
    {
        $qrCodeUrl = 'storage/app/'.$guest->user_id.'/'.$guest->id.'/qrcode.png';

        list($width, $height) = getimagesize($qrCodeUrl);

        $newwidth=Helper::cmToPixels(3,$dpi);
        $newheight=($newwidth*$height)/$width;


        $imageResized = imagecreatetruecolor($newwidth, $newheight);

        $src = imagecreatefrompng($qrCodeUrl);

        imagecopyresampled($imageResized, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        imagecopymerge($img,$imageResized,Helper::cmToPixels(5.2,$dpi),Helper::cmToPixels(0.8,$dpi),0,0,$newwidth,$newheight,100);
    }

    static function drawTexts($texts,$img,$dpi)
    {
        foreach ($texts as $text) {
            $font =  base_path() . '/public/fonts/'.$text->font->font_url;
            $box = new Box($img);
            $box->setFontFace($font);
            $box->setFontColor(Color::parseString($text->hexColor));
            $box->setFontSize(Helper::cmToPixels($text->font_size/10,$dpi));
            $box->setBox(Helper::cmToPixels($text->x,$dpi), Helper::cmToPixels($text->y,$dpi), Helper::cmToPixels($text->width,$dpi), Helper::cmToPixels($text->height,$dpi));
            $box->draw($text->text);
        }
    }

    static function drawBg($bg_url,$img,$destW,$destH)
    {
        $bg_size = getimagesize('public/'.$bg_url);

        if($bg_size[0]>$bg_size[1]){
            $bg_img = imagecreatefromjpeg('public/'.$bg_url);
            // imagecopymerge($img, $bg_img, 0, 0, 0, 0, $detW, ($destW*$bg_size[1])/$bg_size[0], 100);
            imagecopymerge($img, Helper::resize_image('public/'.$bg_url,$destW,($destW*$bg_size[1])/$bg_size[0],true), 0, 0, 0, 0, $destW, $destH, 100);
        }else {
            // $bg_img = imagecreatefromjpeg('public/bgImgs/'.$bg_url);
            imagecopymerge($img, Helper::resize_image('public/'.$bg_url,($destH*$bg_size[0])/$bg_size[1],$destH,true), 0, 0, 0, 0, $destW, $destH, 100);
        }
    }

    static function drawImages($imgs,$img,$dpi)
    {
        foreach ($imgs as $image) {
            $newwidth=Helper::cmToPixels($image->width,$dpi);
            $newheight=Helper::cmToPixels($image->height,$dpi);
            $imageUrl = 'storage/app/'.$image->image_url;

            list($width, $height) = getimagesize($imageUrl);

            $imageResized = imagecreatetruecolor($newwidth, $newheight);

            $src;
            if (exif_imagetype($imageUrl) == IMAGETYPE_JPEG) {
                $src = imagecreatefromjpeg('storage/app/'.$image->image_url);
            }else if(exif_imagetype($imageUrl) == IMAGETYPE_PNG){
                $src = imagecreatefrompng('storage/app/'.$image->image_url);
            }
            imagecopyresampled($imageResized, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            imagecopymerge($img,$imageResized,Helper::cmToPixels($image->x,$dpi),Helper::cmToPixels($image->y,$dpi),0,0,$newwidth,$newheight,100);
        }
    }
}
