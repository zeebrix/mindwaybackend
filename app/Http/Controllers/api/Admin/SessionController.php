<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\MobileApp\Auth\Sessions\AddSessionRequest;
use App\Http\Requests\API\MobileApp\Auth\Sessions\AddJournalRequest;
use App\Models\SessionAudio;
use App\Models\SessionUpload;
use App\Models\SingleCourse;
use App\Models\HomeEmoji;
use App\Models\SleepScreen;
use App\Models\HomeScreen;
use App\Models\Link;
use App\Models\Category;
use App\Http\Requests\API\MobileApp\Auth\Sessions\AddEmojiRequest;
use App\Models\EmojiAdd;
use App\Models\Music;
use App\Models\Quote;
use App\Models\EmojiImage;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\SleepAudio;

class SessionController extends Controller
{

    public function addSession(AddSessionRequest $request)
    {

    $image = $request->course_thumbnail;
    $imageName = time() . '.' . $image->Extension();
    $request->course_thumbnail->storeAs('sessions', $imageName);

    $addSession = new SessionUpload();

    $addSession->course_thumbnail = $imageName;
    $addSession->course_title = $request->course_title;
    $addSession->course_description = $request->course_description;
    $addSession->course_duration = $request->course_duration;

    $addSession->save();

    return response()->json(['message'=>'Session add successfully!']);
    }

    public function uploadSessionAudio(Request $request)
    {
          //book_audio Uploading
          if (isset($request["audio"]) && !empty($request["audio"]) && isset($_FILES["audio"])) {
            @ini_set("memory_limit", "100M");
            @ini_set('post_max_size', '50M');
            @ini_set('upload_max_filesize', '50M');

            $input["audio"] = $request->file('audio')->storeAs('audiobooks', request()->file('audio')->getClientOriginalName());
            $input['audio_title']=$request['audio_title'];
            $input['duration']=$request['duration'];
            $input['session_id'] = $request['session_id'];
        }

        if ($book = SessionAudio::create($input)) {
            return response()->json([
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Session added successfully!',
                        'data' => $book
            ]);
        }

        //Return Default
        return response()->json([
                    "status" => "success",
                    "code" => 200,
                    "message" => "Something went wrong.",
                    "data" => []
        ]);
    }

   public function getSession(Request $request) {

        if(isset($request['id']) && !empty($request['id']))
        {
        $id = $request['id'];
        $response =[];

            $data = DB::table('session_uploads')
            ->join('session_audio','session_audio.session_id','=','session_uploads.id')
            ->where('session_uploads.id',$id)
            ->first();

            $audio = DB::table('session_uploads')
            ->join('session_audio','session_audio.session_id','=','session_uploads.id')
            ->where('session_uploads.id',$id)
            ->get(['session_audio.id','audio_title','audio','duration']);



            $response[] = array(
                "course_title" => $data->course_title,
                "course_description" => $data->course_description,
                "course_thumbnail" => $data->course_thumbnail,
                "course_duration" => $data->course_duration,
                "color" => $data->color,
                "sessions" => $audio->toArray(),
                // "buss_details" => $buss->toArray(),
                // "route_buss_stops" => $routeStops->toArray(),


            );


                return response()->json([
                            "code" => 200,
                            "status" => "success",
                            "message" => "session list fetched successfully.",
                            "data" => $response
                ]);
            }
            else
            {
                $data = DB::table('session_uploads')
            // ->join('session_audio','session_audio.session_id','=','session_uploads.id')
            ->where('session_uploads.deleted_at',null)
            ->get(['session_uploads.*']);
                // dd($data);
            foreach($data as $get)
            {
                $get_id = $get->id;




            $audio = DB::table('session_uploads')
            ->join('session_audio','session_audio.session_id','=','session_uploads.id')
            ->where('session_uploads.id',$get_id)
            ->get(['session_audio.id','session_id','audio_title','audio','duration']);
            
             $sosAudio = DB::table('session_uploads')
                ->join('sos_audio', 'sos_audio.session_id', '=', 'session_uploads.id')
                ->where('session_uploads.id', $get_id)
                ->get(['sos_audio.id', 'audio_title', 'sos_audio','duration']);



            $response[] = array(
                "course_title" => $get->course_title,
                "course_description" => $get->course_description,
                "course_thumbnail" => $get->course_thumbnail,
                "course_duration" => $get->course_duration,
                "color"=>$get->color,
                "sessions" => $audio->toArray(),
                "sos_audio" => $sosAudio->toArray(),
                // "buss_details" => $buss->toArray(),
                // "route_buss_stops" => $routeStops->toArray(),


            );

        }
                return response()->json([
                            "code" => 200,
                            "status" => "success",
                            "message" => "session list fetched successfully.",
                            "data" => $response
                ]);
            }
        }
    
        
        public function addJournal(AddJournalRequest $request)
    {
        
        
       
    
        $addJournal = new Journal();

        $addJournal->title = $request->title;
        $addJournal->email = $request->email;
        $addJournal->description = $request->description;
        $addJournal->date = $request->date;
        $addJournal->save();

        $journal_id = $addJournal->id;

        $addEmoji = new EmojiImage();

        $addEmoji->journal_id = $journal_id;
        $addEmoji->emoji_name = $request->emoji_name;
        if($request->hasFile('emoji_image')){
         $image = $request->emoji_image;
        $imageName = time() . '.' . $image->Extension();
        $request->emoji_image->storeAs('journals', $imageName);
        $addEmoji->emoji_image = $imageName;

        }
        
        $addEmoji->save();

        return response()->json(['code' => 200, 'status' => "success", 'message' => "Journal added successfully!"]);
    }

    public function getJournal(Request $request)
    {
        if(isset($request['email']) && !empty($request['email']))
        {
            $email=$request['email'];
            $atg = array();

            $getMember = DB::table('journals')

                ->where('email',$email)
                ->get();

                // dd($getMember);

            if ($getMember)
            {
                foreach ($getMember as $data)
                {
                    $email =$data->id;

                        $users = DB::table('emoji_images')

                        ->where('journal_id',$email)

                        ->get(['journal_id','emoji_name','emoji_image']);
                        // dd($users);
                        if ($users)
                        {
                            foreach ($users as $or1234)
                            {
                                //Do nothing
                                // array_push($atg, $or1234);
                            }
                            $data->emoji = $users;
                        }
                        else
                        {

                            return response()->json(['code' => 401, 'status' => "failed", 'message' => "No public member exist!"]);
                        }
                        array_push($atg, $data);
                        //  $data->par_details = $atg;


                }
                return response()->json(['code' => 200, 'status' => "success", 'message' => "All member fectched successfully!", 'data' => $atg]);
            }
        }
    else
    {
        return response()->json(['code'=>401,'status'=>"failed",'message'=>"incomplete parameters email is required"]);
    }
}

public function editJournal(Request $request)
{

    if(isset($request['id']) && !empty($request['id']))
    {
        $image = $request->emoji_image;
        $imageName = time() . '.' . $image->Extension();
        $request->emoji_image->storeAs('journals', $imageName);

       $id= $request['id'];

       $getId = DB::table('journals')
              ->where('id',$id)
              ->update([
                'title'=>$request['title'],
                'description'=>$request['description'],
                'date'=>$request['date'],
              ]);
        $getId = DB::table('emoji_images')
              ->where('journal_id',$id)
              ->update([
                'emoji_name'=>$request['emoji_name'],
                'emoji_image'=>$imageName,

              ]);

              return response()->json(['code' => 200, 'status' => "success", 'message' => "Journal Updated successfully!"]);
    }

    else
    {
        return response()->json(['code'=>401,'status'=>"failed",'message'=>"incomplete parameters id is required"]);
    }
}

public function deleteJournal(Request $request)
{
    if(isset($request['id']) && !empty($request['id']))
    {
        $id= $request['id'];

        $getId = DB::table('journals')
               ->where('journals.id',$id)
               ->delete();

        $getId = DB::table('emoji_images')
               ->where('journal_id',$id)
               ->delete();

               return response()->json(['code' => 200, 'status' => "success", 'message' => "Journal deleted successfully!"]);
    }
    else
    {
        return response()->json(['code'=>401,'status'=>"failed",'message'=>"incomplete parameters id is required"]);
    }
}

 public function addCategory(Request $request)
    {
        $addCategory = new Category();

        $addCategory->name = $request->name;

        $addCategory->save();
        return response()->json(['code'=>200,'status'=>"success",'message'=>"Category added successfully!"]);
    }

    public function getCategory()
    {
        $get = Category::get();

        return response()->json(['code'=>200,'status'=>"success",'Message'=>"Category fetched succefully!",'data'=>$get]);
    }

    public function getSleepCourse()
    {
        $data = DB::table('category_courses')

                ->where('category_courses.deleted_at', null)
                ->get(['category_courses.*']);

            foreach ($data as $get) {
                $get_id = $get->id;




                $audio = DB::table('category_courses')
                    ->join('sleep_audio', 'sleep_audio.course_id', '=', 'category_courses.id')
                    ->where('category_courses.id', $get_id)
                    ->get(['sleep_audio.id','sleep_audio.course_id','sleep_audio.audio','sleep_audio.title','sleep_audio.image','sleep_audio.description','sleep_audio.duration','sleep_audio.color']);



                $response[] = array(
                    "id" => $get->id,
                    "title" => $get->title,
                    "description" => $get->description,
                    "thumbnail" => $get->thumbnail,
                    "category_id" => $get->category_id,
                    "sleep_course" => $audio->toArray(),



                );
            }
            return response()->json([
                "code" => 200,
                "status" => "success",
                "message" => "session list fetched successfully.",
                "data" => $response
            ]);
        }
        
         public function getLinks()
        {
            $getLinks = Link::get();

            return response()->json(['code'=>200,'status'=>"success",'message'=>"Your all links fetched successfully!",'message'=>$getLinks]);
        }
        
        public function getHome()
        {
            $getHome= HomeScreen::get();
            return response()->json(['code'=>200,'status'=>"success",'message'=>"Your record fetched successfully!",'message'=>$getHome]);

        }
        
         public function addEmoji(AddEmojiRequest $request)
        {
            $image = $request->emoji;
            $imageName = time() . '.' . $image->Extension();
            $request->emoji->storeAs('emoji', $imageName);

            $addEmoji =new EmojiAdd();

            $addEmoji->name = $request->name;
            $addEmoji->emoji = $imageName;

            $addEmoji->save();

            return response()->json(['code'=>200,'status'=>"success",'message'=>"Emoji added successfully!"]);
        }

        public function getEmoji()
        {
            $getEmoji = EmojiAdd::get();

            return response()->json(['code'=>200,'status'=>"success",'message'=>"Emoji fetched successfully!",'data'=>$getEmoji]);
        }
        
        public function getMusic()
        {
            $getMusic = Music::get();

            return response()->json(['code'=>200,'status'=>"success",'message'=>"Music fetched successfully!",'data'=>$getMusic]);
        }
        
         public function getSleepScreen()
        {
            $getSleepScreen = SleepScreen::get();
            return response()->json(['code'=>200,'status'=>"success",'message'=>"Sleep screen fetched successfully!",'data'=>$getSleepScreen]);
        }

        public function getHomeEmoji()
        {
            $getHomeEmoji = HomeEmoji::get();
            return response()->json(['code'=>200,'status'=>"success",'message'=>"Home emoji fetched successfully!",'data'=>$getHomeEmoji]);
        }

        public function getSingleCourse()
        {
            $getSingleCourse = SingleCourse::get();
            return response()->json(['code'=>200,'status'=>"success",'message'=>"Single course fetched successfully!",'data'=>$getSingleCourse]);
        }
    
// public function getQuote()
// {
//     $quoteOfDayKey = 'quote_of_day';
//     $currentDate = now()->format('Y-m-d');
//     $getQuote = Cache::get($quoteOfDayKey);

//     if (!$getQuote) {
//         $getQuote = Quote::inRandomOrder()->limit(1)->get();
//         Cache::put($quoteOfDayKey, $getQuote, now()->endOfDay());
//     }

//     return response()->json([
//         'code' => 200,
//         'status' => "success",
//         'message' => "Random quote fetched successfully!",
//         'data' => [$getQuote]
//     ]);
// }
         public function getCourse($id, $course_order_by) {
         
            
             $qcourseDayKey = $id;
    $currentDate = now()->format('Y-m-d');
    $getCourse = Cache::get($qcourseDayKey);

//     if (!$getCourse) {
       
    
 
//       $getCourse = SessionAudio::join('session_uploads', 'session_uploads.id', '=', 'session_audio.session_id')
//     ->where('session_audio.session_id', $id)
//   ->where('session_audio.course_order_by', $course_order_by)
//     ->limit(1)
//     ->get()
//     ;
    
//         Cache::put($qcourseDayKey, $getCourse, now()->endOfDay());
//     }
            
      $getCourse = SessionAudio::join('session_uploads', 'session_uploads.id', '=', 'session_audio.session_id')
    ->where('session_audio.session_id', $id)
  ->where('session_audio.course_order_by', $course_order_by)
    ->limit(1)
    ->get()
    ;
      
            return response()->json(['code'=>200,'status'=>"success",'message'=>"Single course fetched successfully!",'data'=>$getCourse]);
        
        }
        
        
        
        
        
    public function getQuote($id, $date)
    {
        $userId = $id;
        $quoteOfDayKey = 'quote_of_day_' . $userId;
        $storedDate = Cache::get($quoteOfDayKey);

        if ($storedDate !== $date) {
            $quote = Quote::inRandomOrder()->first();
            Cache::put($quoteOfDayKey, $date, now()->endOfDay());
            Cache::put('quote_' . $userId, $quote, now()->endOfDay());
        } else {
            $quote = Cache::get('quote_' . $userId);
        }

        return response()->json([
            'code' => 200,
            'status' => "success",
            'message' => "Your record fetched successfully!",
            'data' => [$quote]
        ]);
    }

    public function getHomeSleepAudio($id, $date)
    {
        $userId = $id;
        $quoteOfDayKey = 'sleep_of_day' . $userId;
        $storedDate = Cache::get($quoteOfDayKey);

        if ($storedDate !== $date) {
            $getHomeSleepAudio = SleepAudio::inRandomOrder()->limit(1)->get();
            Cache::put($quoteOfDayKey, $date, now()->endOfDay());
            Cache::put('sleep_' . $userId, $getHomeSleepAudio, now()->endOfDay());
        } else {
            $getHomeSleepAudio = Cache::get('sleep_' . $userId);
        }

        return response()->json([
            'code' => 200,
            'status' => "success",
            'message' => "Random Audio sleep for home fetched successfully!",
            'data' => $getHomeSleepAudio
        ]);
    }

 
 
 public function getRandomCourse() {
   
            
      $getCourse = SessionAudio::join('session_uploads', 'session_uploads.id', '!=', 'session_audio.session_id')
    ->inRandomOrder()
    ->limit(1)
    ->get()
    ;
      
            return response()->json(['code'=>200,'status'=>"success",'message'=>"Rendom course fetched successfully!",'data'=>$getCourse]);
        
        }
 

}
