<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait ImageTrait {

    public function uploads($file, $path)
    {
        if($file) {

            $fileName   = time().'.'. $file->getClientOriginalExtension();
            Storage::disk('public')->put($path . $fileName, File::get($file));
            $filePath   = $path . $fileName;

            return $filePath;
        }
    }
    public function deleteImage($path){
       if(Storage::disk('public')->exists($path)){
        Storage::disk('public')->delete($path);
       }
    }

}
