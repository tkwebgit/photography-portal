<?php

namespace App\Http\Controllers;
use App\User;
use App\Upload;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getuploads()
    {
        $uploads = Upload::with('comments','user')->orderBy('updated_at', 'desc')->get();
        foreach($uploads as $ups){
            foreach($ups->comments as $cms){
                $user = User::where('id',$cms->user_id)->first();
                $cms['user'] = $user;
            }
        }
        return response([
            'status'=> true,
            'uploads'=>$uploads
        ],200);
    }
    public function myUploads()
    {
        $myuploads = Upload::where('user_id',auth()->user()->id)->with('comments','user')->orderBy('updated_at', 'desc')->get();
        foreach($myuploads as $ups){
            foreach($ups->comments as $cms){
                $user = User::where('id',$cms->user_id)->first();
                $cms['user'] = $user;
            }
        }
        return response([
            'status'=> true,
            'myuploads'=>$myuploads
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'photo' => 'required',
            'comment' => 'required'
        ]);
        
        $commenttocreate = array_except($attributes, ['photo']);
        $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
        $img = preg_replace('#^data:image/[^;]+;base64,#', '', $request->photo);
        
        \Storage::disk('public')->put('images/user'.auth()->user()->id.'/'.$name, base64_decode($img));
         $attributes['path'] = $name;

        $attributes['user_id'] = auth()->user()->id;
        $phototocreate = array_except($attributes, ['comment','photo']);
        $imageuploaded = Upload::create($phototocreate);
        
         $comment = \App\Comment::create([
            'upload_id'=>$imageuploaded->id,
            'user_id'=>auth()->user()->id,
            'comment'=>$attributes['comment'],
            'caption'=>true
        ]);
        return response([
            'status'=>true,
            'message'=>'Successfully uploaded',
            'image'=>$imageuploaded,
            'comment'=>$comment
        ],200);
    }
    public function profile(Request $request)
    {
        $attributes = $request->validate([
            'photo' => 'required',
        ]);
    
        $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
        $img = preg_replace('#^data:image/[^;]+;base64,#', '', $request->photo);
        
        \Storage::disk('public')->put('profiles/'.$name, base64_decode($img));
        $user = User::where('id',auth()->user()->id)->first();
        $user->update([
            'profile'=>$name
        ]);
        return response([
            'status'=>true,
            'message'=>'Successfully updated profile',
            'user'=>$user,
            'name'=>$name
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Upload  $upload
     * @return \Illuminate\Http\Response
     */
    public function show(Upload $upload)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Upload  $upload
     * @return \Illuminate\Http\Response
     */
    public function edit(Upload $upload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Upload  $upload
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Upload $upload)
    {
        $attributes = $request->validate([
            'photo' => 'required',
        ]);
        
        $commenttocreate = array_except($attributes, ['photo']);
        $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
        $img = preg_replace('#^data:image/[^;]+;base64,#', '', $request->photo);
        
        \Storage::disk('public')->put('images/user'.auth()->user()->id.'/'.$name, base64_decode($img));
         $attributes['path'] = $name;
        $upload->update([
            'path'=>$name
        ]);
        $captionfilter = ['user_id'=>auth()->user()->id,'caption'=>true];
        $captionupdated = \App\Comment::where($captionfilter)->first();
        $captionupdated->update([
            'comment'=>$attributes['caption']
        ]);
        return response([
            'status'=>true,
            'message'=>'Successfully updated',
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Upload  $upload
     * @return \Illuminate\Http\Response
     */
    public function destroy(Upload $upload)
    {
       $upload->delete();
       return response([
           'message'=>'Successfully deleted'
       ],200);
    }
}
