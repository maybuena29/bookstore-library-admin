<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function uploadFile($path, $file)
    {
		$readedFile = $file;
        $filename =time().$readedFile->getClientOriginalName();
        $file->move(public_path($path), $filename);

        return $filepath = $path. $filename;
    }
}
