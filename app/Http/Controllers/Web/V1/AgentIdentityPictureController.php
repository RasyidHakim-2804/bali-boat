<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\Storage;

class AgentIdentityPictureController extends Controller
{
    use JsonResponsesTrait;

    public function get($image_name)
    {
        $path = '/private/upload/images/agent/' . $image_name;

        // if(!Storage::exists($path)){
        //     abort(404);
        // }

        return Storage::response($path);
    }
}
