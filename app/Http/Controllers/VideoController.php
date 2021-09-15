<?php

namespace App\Http\Controllers;

use App\Jobs\FileConversion;
use App\Video;
use FFMpeg\FFMpeg;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Livewire\WithFileUploads;
use Owenoj\LaravelGetId3\GetId3;
use Pawlox\VideoThumbnail\Facade\VideoThumbnail;

class VideoController extends Controller
{
    use DispatchesJobs;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $videos = auth()->user()->videos()->where('status', 'converted')->orderByDesc('created_at')->paginate(10);
        return view('home', compact('videos'));
    }

    /**
     * Store new Video
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if ($request->hasFile('video')) {
            $validator = Validator::make($request->all(), [
                'title' => ['required'],
                'video' => [
                    'required',
                    'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts', 'max:100040',
                ]
            ]);

            //if validation fails then send status false
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 500);
            }

            $file = $request->video;
            $title = $request->title;
            $url = $file->getClientOriginalName();
            $ext = $file->extension();
            $name = pathinfo($url, PATHINFO_FILENAME);

            $data = ['title'=>$title,'name' => $name, 'url' => $url, 'user_id' => auth()->user()->id, 'status' => 'pending'];
            //create video record in database
            $video = Video::query()->create($data);

            $user = auth()->user();
            $path = 'uploads/' . $user->id . '/';

            //store video to disk
            $request->video->storeAs($path, $url, 'public');

            //start video format conversion process in background
            FileConversion::dispatch($video, $user);
        }
        return response()->json(['status' => true, 'user_id' => $user->id], 200);
    }
}
