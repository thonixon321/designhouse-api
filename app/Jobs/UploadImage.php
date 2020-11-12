<?php

namespace App\Jobs;

use Image;
use File;
use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
//ShouldQueue makes sure this job can get taken care of in the background
//while the user continues to do other things (like add meta data for the image they are uploading) - this is also set up in the .env file by changing the default 'sync' to 'database' and we made the migration for the jobs table, which will handle these queued jobs by running php artisan queue:table and php artisan migrate; then after that when a user uploads a file, they get an immediate response back and don't have to wait for it to upload to s3 or wherever, the jobs table will get that entry and on the server or in some command you have to run php artisan queue:work (set a timer or something) and that queued job will process or fail. If it fails, you have the option to try again (send an email saying it failed maybe)
class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        //set as class variable
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

     //upload the image file
    public function handle()
    {
        //disk accessed from the design data we passed in
        $disk = $this->design->disk;
        $file_name = $this->design->image;
        //file path on our system
        $original_file = storage_path() . '/uploads/original/' . $file_name;

        //do a try catch in case there is an error with an upload and we want to notify the user
        try {
            //create Large image and save to tmp disk (using the Image intervention package we pulled in)
            Image::make($original_file)
                ->fit(800, 600, function($constraint){
                    //can add in constraints here to the resize image process
                    //we don't want to distort the aspect ratio of the image (so if image doesn't work at 800, 600 it will change the 600 to fit the aspect ratio)
                    $constraint->aspectRatio();
                })
                ->save($large = storage_path('uploads/large/'. $file_name)); //save to the similar spot as the original file location

            //create Thumbnail image and save to tmp disk (using the Image intervention package we pulled in)
            Image::make($original_file)
            ->fit(250, 200, function($constraint){
                $constraint->aspectRatio();
            })
            ->save($thumbnail = storage_path('uploads/thumbnail/'. $file_name)); //save to the similar spot as the original file location

            //now store images to permanent disk (could be amazon s3 or local public)
            //original image - put() moves image from src location to a destination, first param is the destination you're trying to put the files to, second is the file itself
           if(Storage::disk($disk)->put('uploads/designs/original/'.$file_name, fopen($original_file, 'r+'))) {
               //delete the temp file from system
               File::delete($original_file);
           }
           //large image 
           if(Storage::disk($disk)->put('uploads/designs/large/'.$file_name, fopen($large, 'r+'))) {
            //delete the temp file from system
            File::delete($large);
            }
            //thumbnail image 
           if(Storage::disk($disk)->put('uploads/designs/thumbnail/'.$file_name, fopen($thumbnail, 'r+'))) {
            //delete the temp file from system
            File::delete($thumbnail);
            }

            //update database record with success flag
            $this->design->update([
                'upload_successful' => true
            ]);

        } catch(Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
