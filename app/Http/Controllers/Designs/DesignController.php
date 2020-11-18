<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\IDesign;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;

class DesignController extends Controller
{
    protected $designs;
    
    //inject the repository contracts in the controller (could be as many as you want in the construct parameter here) - IDesign is implemented in the DesignRepository. The DesignRepository extends the BaseRepository which gives us access to the Design model ($designs) in it's construct method
    public function __construct(IDesign $designs)
    {
        $this->designs = $designs;
    }

    //index method for making working with repositories easier - they just return all the records in the DB
    public function index()
    {
        //now that we have access to the all() from the above constructor -
        //so we don't have to do Design::all() and pull in the model directly, this is now hooked up to the repository contract which defines methods we want to use on a model, and it makes our code more modular since we can further control how we access the DB. So if we decided to not use Eloquent for accessing the DB, we could easily swap it out for some other DB engine, and as long as we bind it to the contracts everything should work fine. The methods that access the DB are in just one location now and can be accessed from anywhere (kind of like a vuex store) - withCriteria allow us to use additional filters on the data (all designs)
        $designs = $this->designs->withCriteria([
            new LatestFirst(),
            new IsLive(),
            new ForUser(2),
            new EagerLoad(['user', 'comments'])
        ])->all();
        //this is how you access all of the designs while using the Design Resource - as opposed to returning just one with new DesignResource($design);
        return DesignResource::collection($designs);
    }

    public function findDesign($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request, $id) //$id was passed by the route 
    {
        $design = $this->designs->find($id);
        //this is where we use the design policy to make sure the user updating the design is in fact the owner of that design - we also handle an unauthorized response by overriding the default one in Exceptions/Handler
        $this->authorize('update', $design);

        $this->validate($request, [
            //add $id here cause if user is editing data and they didn't change
            //the title (therefore not unique) we don't want to send an error in that case, only when it is truly a different design being edited
            'title' => ['required', 'unique:designs,title,'. $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required']
        ]);
        //you can see some of the benefits of extracting out the update functionality here from being directly on the model to being used in a repository. For example, if an admin wanted to update the design, you wouldn't need to make another update method of the admin controller, you could reuse this same method here      
        $design = $this->designs->update($id,[
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            //want to check if the upload was a success before allowing this to be live
            'is_live' => ! $design->upload_successful ? false : $request->is_live
        ]);

        //apply the tags (uses a trait on the model that was pulled in from the cviebrock taggable package we installed)
        //retag removes any existing tags on a model then retags with the ones that are in the request
        $this->designs->applyTags($id, $request->tags);


        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = $this->designs->find($id);
        //check if user is authorized
        $this->authorize('delete', $design);
        
        //delete files associated with the record
        foreach(['thumbnail', 'large', 'original'] as $size) {
            //check if file exists in DB
            if (Storage::disk($design->disk)->exists("uploads/designs/{$size}/".$design->image)) {
                //clear the files out
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/".$design->image);
            }
        }

        //delete record from DB
        $this->designs->delete($id);
        return response()->json(['message' => 'Record deleted'], 200);
    }

    public function like($id)
    {
        $this->designs->like($id);

        return response()->json(['message' => 'Successful'], 200);
    }

    //check if current user has liked the design
    public function checkIfUserHasLiked($designId)
    {
       $isLiked = $this->designs->isLikedByUser($designId);

       return response()->json(['liked' => $isLiked], 200);
    }
}
