<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PuzzleImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PuzzleImageController extends Controller
{
    public function create()
    {
        request()->validate([
            'image'=>'required|image',
        ]);
        $data=['user_id'=>Auth::user()->id];
        $index=0;
        $imageUrl;
        do {
            $imageUrl=Auth::user()->id.'/puzzle-images/';
            $imageName = $index.'.'.request()->image->extension();
            $index++;
        } while (Storage::disk('local')->exists($imageUrl.$imageName)!=null);

        Storage::disk('local')->putFileAs($imageUrl, request()->image,$imageName);
        $data+=['img_url'=>$imageUrl.$imageName];

        return PuzzleImage::create($data);
    }

    public function delete(PuzzleImage $image)
    {
        if(Auth::user()->id!=$image->user->id){
            return response(['message'=>'you are not authorized to delete this image'],401);
        }

        Storage::disk('local')->delete($image->img_url);
        $image->delete();
        return response(['message'=>'puzzle image delete successful'],200);
    }

    public function index()
    {
        return Auth::user()->puzzle_images;
    }
}
