<?php

namespace App\Http\Controllers\Designs;

use App\Jobs\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function upload(Request $request) 
    {
        //validate request
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:2048']
        ]);
        //get the image
        $image = $request->file('image');
        $image_path = $image->getPathName();

        //get original file name and replace spaces with _ and use a timestamp
        //to make it unique
        //Business Cards.png = timestamp()_business_cards.png
        $filename = time()."_". preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));    

        //move image to temporary location (tmp) - uses storeAs which takes in the subfolder where you want to store to inside the main storage folder, and the file name that we got from the request, and the optional parameter which is the disk you want to store to (we created this in the config folder in the filesystems file)
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

        //create DB record for design (we have the relation set with the auth user and the design so we tap into it here)
        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk') //found in site.php in config
        ]);

        //dispatch job to handle image manipulation (calling the job UploadImage with the design passed into this constructor)
        $this->dispatch(new UploadImage($design));
        //eventually we will change this to respond with a design resource (our own custom info we want to send back about this design) but for now we just use the design
        return response()->json($design, 200);

    }
}
