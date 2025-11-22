<?php

namespace App\Trait;

use Illuminate\Support\Facades\Storage;

trait FileConfigTrait
{
    public function picsEnv($pics){
        if($pics){
            if(config('app.env')=='local'){
                $pics ='http://127.0.0.1:8000'.Storage::url($pics);
            }else{
                // Ensure 'public/' is present between 'app/' and the relative path
                $path = ltrim($pics, '/');
                if (!str_starts_with($path, 'public/')) {
                    $path = 'public/' . $path;
                }
                $pics =  asset('ngconsulting/storage/app/' . $path);
            }

            return $pics;
        }

        return null;

    }

}
