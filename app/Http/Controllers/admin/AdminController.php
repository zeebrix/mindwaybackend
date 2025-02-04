<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SessionAudio;
use App\Models\SessionUpload;
use App\Models\HomeScreen;
use Illuminate\Http\Request;
use App\Models\SleepAudio;
use App\Models\Music;
use App\Models\EmojiAdd;
use App\Models\SosAudio;
use App\Models\Link;
use App\Models\CategoryCourse;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\SingleCourse;
use App\Models\HomeEmoji;
use App\Models\Session;
use App\Models\Quote;
use App\Models\SleepScreen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralEmail;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\CounsellingSession;
use PDF;
use App\Models\Program;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Model\UpdateContact;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Counselor;
use App\Models\CustomreBrevoData;
use App\Models\ProgramPlan;
use App\Models\User;
use App\Services\SlotGenerationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Models\CustomerRelatedProgram;
use App\Models\ProgramMultiLogin;
use Brevo\Client\Model\CreateContact;
use PragmaRX\Google2FA\Google2FA;
use App\Models\ProgramDepartment;
use App\Services\BrevoService;
use SendinBlue\Client\Model\RemoveContactFromList;
use SendinBlue\Client\ApiException;

class AdminController extends Controller
{
    public function setting()
    {
        $user = auth()->user();

        // Pass current 2FA state, secret, and QR code (if enabled)
        $qrCodeUrl = null;
        $secret = null;

        if ($user->uses_two_factor_auth) {
            $google2fa = new Google2FA();
            $secret = $user->google2fa_secret;
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );
        }
        return view('admin.setting', compact('qrCodeUrl', 'secret', 'user'));
    }
    public function saveSetting(Request $request)
    {
        $user = auth()->user();
        $google2fa = new Google2FA();

        if ($request->has('enable_2fa')) {
            if(!$user->uses_two_factor_auth)
            {
                $secret = $google2fa->generateSecretKey();
                $user->google2fa_secret = $secret;
                $user->uses_two_factor_auth = true;
                $user->save();    
            }
            
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );

            return redirect()->route('admin.setting')
                ->with(['success' => 'Two-factor authentication enabled!', 'qrCodeUrl' => $qrCodeUrl, 'secret' => $user->google2fa_secret]);
        } else {
            // Disable 2FA
            $user->google2fa_secret = null;
            $user->uses_two_factor_auth = false;
            $user->save();

            return redirect()->route('admin.setting')
                ->with(['success' => 'Two-factor authentication disabled!']);
        }
    }
    public function Login()
    {
        if (\Auth::check()) {
            return redirect("/manage-admin/view-dashboard");
        }
        return view('admin.login');
    }
    public function Addcounselor(Request $request)
    {
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', // Ensure the email is unique
            'password' => 'required|string|min:6',
        ];

        // Create a validator instance
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // Return a custom JSON response with validation errors
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity status code
        }
        $user = Counselor::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt('Test123'), // Hash the password
        ]);
        $recipient = $request->email;
        $subject = 'Welcome to Mindway EAP – Set Up Your Profile';
        $template = 'emails.counsellor-setup-profile';
        $data = [
            'full_name' => $request->name,
            'id' => $user->id
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);

        return response()->json(['message' => 'Form submitted successfully!']);
    }
    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    function checkLogin(Request $request)
    {
        $this->validate($request, [

            'email' => 'required|email',
            'password' => 'required|min:3'
        ]);

        $user_data = array(

            'email' => $request->get('email'),
            'password' => $request->get('password')
        );
        if (Auth::attempt($user_data)) {
            $user = Auth::user();
            if ($user->uses_two_factor_auth) {
                $google2fa = new Google2FA();

                if ($request->session()->has('2fa_passed')) {
                    $request->session()->forget('2fa_passed');
                }

                $request->session()->put('2fa:user:id', $user->id);
                $request->session()->put('2fa:auth:attempt', true);
                $request->session()->put('2fa:auth:remember', $request->has('remember'));
                $google2fa_secret = $google2fa->generateSecretKey();
                $otp_secret = $user->google2fa_secret;
                $one_time_password = $google2fa->getCurrentOtp($otp_secret);

                return redirect()->route('2fa')->with('one_time_password', $one_time_password);
            }
            return redirect()->route('admin.view-dashboard');
        } else {
            return back()->with('error', 'Wrong Login Details');
        }
    }


    public function viewCustomer(Request $request)
    {
        // Get all customers
        $getCustomer = Customer::get();

        // Get all programs with their associated customers
        $Program = Program::with('customers')->get();



        // Initialize an empty array to store customer records
        $customerData = [];

        // Iterate over each program and its associated customers
        foreach ($Program as $program) {
            // Access associated Customers for each Program
            foreach ($program->customers as $customer) {
                // Access Customer attributes
                $customerId = $customer->id;
                $customerName = $customer->name;
                $customerEmail = $customer->email;

                // Add the customer data to the array
                $customerData[] = [
                    'id' => $customerId,
                    'name' => $customerName,
                    'email' => $customerEmail,
                ];
            }
        }
        $customers = Customer::where('add_manage_staff', 1)->get();
        $totalCount = count($customerData);
        $customerPercentage = $totalCount  / 100;
        $totalCustomers = Customer::count();

        return view('mw-1.admin.user.manage', get_defined_vars());
        // Pass the customer data array to the view
        return view('admin.customer', compact('getCustomer', 'customerData', 'customers', 'totalCustomers', 'customerPercentage'));
        // dd($customerData);
    }


    public function deleteCustomer($id)
    {
        $deleteCustomer = Customer::find($id)->delete();
        return back()->with('message', 'Your customer is deleted successfully!');
    }

    public function addCourse()
    {
        return view('mw-1.admin.courses.add');
        return view('admin.add-course');
    }

    public function viewCourse()
    {
        $viewCourse = SessionUpload::get();
        return view('mw-1.admin.courses.manage', get_defined_vars());
        // return view('admin.view-course',compact('viewCourse'));
    }

    public function viewAudio()
    {
        $getAudio = SessionAudio::get();
        return view('mw-1.admin.course-audio.manage', get_defined_vars());
    }

    public function courseAdd(Request $request)
    {

        $request->validate([
            'course_thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->course_thumbnail;
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $request->course_thumbnail->storeAs('course', $imageName);

        $addSession = new SessionUpload();

        $addSession->course_thumbnail = $imageName;
        $addSession->course_title = $request->course_title;
        $addSession->course_description = $request->course_description;
        $addSession->course_duration = $request->course_duration;
        $addSession->color = $request->favorite_color;

        $addSession->save();
        return back()->with('message', 'Course added successfully!');

        // $session_id = $addSession->id;
        // dd($session_id);

        //book_audio Uploading

        // @ini_set("memory_limit", "100M");
        // @ini_set('post_max_size', '50M');
        // @ini_set('upload_max_filesize', '50M');

        // $input["audio"] = $request->file('audio')->storeAs('audiobooks', request()->file('audio')->extension());
        // $input['session_id'] = $session_id;
        // $input['audio_title'] = $request['audio_title'];


        // $input['session_id'] = $request['session_id'];


        // if ($book = SessionAudio::create($input)) {
        //     return back()->with('message', 'Course added successfully!');
        // }
    }


    public function deleteCourse($id)
    {
        SessionUpload::where('id', $id)->delete();
        return back()->with('message', 'Course deleted successfully!');
    }

    public function deleteAudio($id)
    {
        SessionAudio::where('id', $id)->delete();
        return back()->with('message', 'Audio deleted successfully!');
    }

    public function editCourse($id)
    {
        $editCourse = SessionUpload::find($id);
        return view('mw-1.admin.courses.edit', get_defined_vars());

        return view('admin.edit-course', compact('editCourse'));
    }

    public function updateCourse(Request $request, $id)
    {

        $request->validate([
            'course_thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $addSession =  SessionUpload::find($id);
        $image = $request->course_thumbnail;
        if ($image && $request->has('course_thumbnail')) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->course_thumbnail->storeAs('course', $imageName);
            $addSession->course_thumbnail = $imageName;
        }


        $addSession->course_title = $request->course_title;
        $addSession->course_description = $request->course_description;
        $addSession->course_duration = $request->course_duration;
        $addSession->color = $request->favorite_color;

        $addSession->save();


        return back()->with('message', 'Course updated successfully!');
    }

    // add course audio start here

    public function addAudio()
    {
        $sessionList = DB::table("session_uploads")->where('deleted_at', null)->get();
        return view('mw-1.admin.course-audio.add', get_defined_vars());

        return view('admin.Add-course-audio', compact('sessionList'));
    }

    public function insertAudio(Request $request)
    {
        $audio = new SessionAudio();

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');
        // Retrieve the existing audio record
        // $item = Item::find($id);
        $audio->session_id = $request->input('session_id');
        $audio->duration = $request->input('duration');
        $audio->audio_title = $request->input('audio_title');

        // If a new audio file is uploaded, handle it:
        if ($request->hasFile('audio')) {
            // Store the new audio file
            $audio->audio = $request->file('audio')->storeAs('audiobooks', request()->file('audio')->getClientOriginalName());
        }
        // Update the audio record
        $audio->save();

        return back()->with('message', 'Audio added successfully!');
    }

    public function editAudio(Request $request)
    {
        $sessionList = DB::table("session_uploads")->where('deleted_at', null)->get();
        $getAudio = SessionAudio::where('id', $request->id)->first();
        return view('mw-1.admin.course-audio.edit', get_defined_vars());
        return view('admin.Edit-course-audio', [
            'getAudio' => $getAudio,
            'sessionList' => $sessionList,
        ]);
        // dd($getAudio);
    }
    public function updateAudio(Request $request)
    {
        //book_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');
        // Retrieve the existing audio record
        // $audio = SessionAudio::findOrFail($request->id);
        // $audio  = SessionAudio::where('id', $request->id)->skip($index)->first();
        $audio =  SessionAudio::find($request->audio_id);
        // $item = Item::find($id);
        // dd($request->input('session_id'));
        $audio->session_id = $request->input('session_id');
        $audio->duration = $request->input('duration');
        $audio->audio_title = $request->input('audio_title');

        // If a new audio file is uploaded, handle it:
        if ($request->hasFile('audio')) {
            // Delete the existing audio file from storage
            Storage::delete($audio->audio);

            // Store the new audio file
            $audio->audio = $request->file('audio')->storeAs('audiobooks', request()->file('audio')->getClientOriginalName());
        }
        // Update the audio record
        $audio->save();


        // Provide a success message
        // return redirect()->route('view-audio')->with('message', 'Audio updated successfully!');
        return redirect('manage-admin/view-audio')->with('message', 'Audio updated successfully!');
    }

    public function audioAdd(Request $request)
    {
        //book_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $input["audio"] = $request->file('audio')->storeAs('audiobooks', request()->file('audio')->getClientOriginalName());
        $input['session_id'] = $request['session_id'];
        $input['audio_title'] = $request['audio_title'];
        $input['duration'] = $request['duration'];
        // $input['session_id'] = $request['session_id'];


        if ($book = SessionAudio::create($input)) {
            return back()->with('message', 'Audio added successfully!');
        }
    }

    // Category sleep course start here

    public function viewSleepCourse()
    {
        $viewSleepCourse = CategoryCourse::get();

        return view('mw-1.admin.sleep-courses.manage', get_defined_vars());
        return view('admin.view-sleep-course', compact('viewSleepCourse'));
    }

    public function addSleepCourse()
    {
        $categoryList = \DB::table("categories")->where('deleted_at', null)->get();
        return view('mw-1.admin.sleep-courses.add', get_defined_vars());
        return view('admin.add-sleep-course', compact('categoryList'));
    }

    public function sleepCourseAdd(Request $request)
    {
        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->thumbnail;
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $request->thumbnail->storeAs('course', $imageName);

        $addSession = new CategoryCourse();

        $addSession->category_id = $request->category_id;
        $addSession->thumbnail = $imageName;
        $addSession->title = $request->title;
        $addSession->description = $request->description;

        $addSession->save();
        return back()->with('message', 'Sleep course added successfully!');
        // $course_id = $addSession->id;
        //book_audio Uploading

        // @ini_set("memory_limit", "100M");
        // @ini_set('post_max_size', '50M');
        // @ini_set('upload_max_filesize', '50M');

        // $input["audio"] = $request->file('audio')->storeAs('/sleepaudio', request()->file('audio')->getClientOriginalName());
        // // dd($input);
        // $input['course_id'] = $course_id;
        // $input['duration'] = $request['duration'];
        // $input['title'] = $request['title'];
        // $input['color'] = $request['color'];
        // $input['description'] = $request['description'];
        // $input['duration'] = $request['duration'];
        // $input['image'] = $imageName;

        // if ($book = SleepAudio::create($input)) {
        //     return back()->with('message', 'Sleep course added successfully!');
        // }
    }

    public function deleteSleepCourse($id)
    {
        $deleteSleep = CategoryCourse::find($id)->delete();
        return back()->with('message', 'Sleep course deleted successfully!');
    }

    public function editSleepCourse($id)
    {
        $categoryList = \DB::table("categories")->where('deleted_at', null)->get();
        $getSleepCourse = CategoryCourse::find($id);
        return view('mw-1.admin.sleep-courses.edit', get_defined_vars());
        return view('admin.edit-sleep-course', compact('getSleepCourse'));
    }

    public function updateSleepCourse(Request $request, $id)
    {
        $request->validate([
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->thumbnail;

        $addSession = CategoryCourse::find($id);

        $addSession->category_id = $request->category_id;

        if ($image && $request->has('thumbnail')) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->thumbnail->storeAs('course', $imageName);
            $addSession->thumbnail = $imageName;
        }
        $addSession->title = $request->title;
        $addSession->description = $request->description;


        $addSession->save();

        return redirect('manage-admin/view-sleep-course')->with('message', 'Course updated successfully!');
    }

    public function deleteSleepAudio($id)
    {
        $deleteSleep = SleepAudio::find($id)->delete();

        return back()->with('message', 'Sleep audio deleted successfully!');
    }

    public function viewSleepAudio()
    {
        $getSleepAudio = SleepAudio::get();
        return view('mw-1.admin.sleep-audio.manage', get_defined_vars());

        return view('admin.view-sleep-audio', compact('getSleepAudio'));
    }

    public function addSleepAudio()
    {
        $courseList = DB::table("category_courses")->where('deleted_at', null)->get();
        return view('mw-1.admin.sleep-audio.add', get_defined_vars());
        return view('admin.add-sleep-audio', compact('courseList'));
    }
    public function editSleepAudio($id)
    {
        $editSleepAudio = SleepAudio::find($id);
        $courseList = DB::table("category_courses")->where('deleted_at', null)->get();
        return view('mw-1.admin.sleep-audio.edit', get_defined_vars());
        return view('admin.edit-sleep-audio', compact('editSleepAudio', 'courseList'));
    }

    public function sleepAudioAdd(Request $request)
    {
        //book_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $input["audio"] = $request->file('audio')->storeAs('sleepaudio', request()->file('audio')->getClientOriginalName());
        $input['course_id'] = $request['course_id'];
        $input['duration'] = $request['duration'];


        $input["image"] = $request->file('image')->storeAs('sleepimage', request()->file('image')->getClientOriginalName());
        $input['title'] = $request['title'];
        $input['description'] = $request['description'];
        $input['color'] = $request['favorite_color'];

        // $input['session_id'] = $request['session_id'];


        if ($book = SleepAudio::create($input)) {
            return back()->with('message', 'Sleep Audio added successfully!');
        }
    }

    public function updateSleepAudio(Request $request, $id)
    {
        //book_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $input = SleepAudio::find($id);

        if ($request->has('audio')) {
            $input["audio"] = $request->file('audio')->storeAs('sleepaudio', request()->file('audio')->getClientOriginalName());
        }
        $input['course_id'] = $request['course_id'];
        $input['duration'] = $request['duration'];

        if ($request->has('image')) {
            $input["image"] = $request->file('image')->storeAs('sleepimage', request()->file('image')->getClientOriginalName());
        }
        $input['title'] = $request['title'];
        $input['description'] = $request['description'];
        $input['color'] = $request['color'];

        // $input['session_id'] = $request['session_id'];
        $input->save();
        return back()->with('message', 'Sleep Audio updated successfully!');


        //  if ($book = SleepAudio::create($input)) {
        //      return back()->with('message','Sleep Audio updated successfully!');
        //  }
    }

    public function viewCategory()
    {
        $getCategory = Category::get();
        return view('mw-1.admin.courses-category.manage', get_defined_vars());
        return view('admin.view-category', compact('getCategory'));
    }

    public function addCategory()
    {
        return view('mw-1.admin.courses-category.add');
        return view('admin.add-category');
    }

    public function categoryAdd(Request $request)
    {
        $add = new Category();

        $add->name = $request->name;
        $add->save();

        return back()->with('message', "Category add successfully!");
    }

    public function deleteCategory($id)
    {
        $deleteCategory = Category::find($id)->delete();
        return back()->with('message', 'Category deleted successfully!');
    }

    public function editCategory($id)
    {
        $editCategory = Category::find($id);
        return view('mw-1.admin.courses-category.edit', get_defined_vars());
        return view('admin.edit-category', compact('editCategory'));
    }

    public function updateCategory(Request $request, $id)
    {
        $add = Category::find($id);

        $add->name = $request->name;
        $add->save();

        return redirect('manage-admin/view-category')->with('message', 'Category updated successfully!');
    }

    public function viewLinks()
    {
        $getLinks = Link::get();
        return view('mw-1.admin.account-links.manage', get_defined_vars());

        return view('admin.view-links', compact('getLinks'));
    }
    public function addLinks(Request $request)
    {
        return view('mw-1.admin.account-links.add');
        return view('admin.add-links');
    }

    public function linksAdd(Request $request)
    {
        $request->validate([
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->icon;
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $request->icon->storeAs('links', $imageName);

        $addLink = new Link();

        $addLink->url_name = $request->url_name;
        $addLink->title = $request->title;
        $addLink->sub_title = $request->sub_title;
        $addLink->icon = $imageName;
        $addLink->save();

        return back()->with('message', 'Links add successfully!');
    }
    public function deleteLinks($id)
    {
        $deleteLinks = Link::find($id)->delete();
        return back()->with('message', 'Links deleted successfully!');
    }

    public function editLinks($id)
    {
        $getUpdateLinks = Link::find($id);
        return view('mw-1.admin.account-links.edit', get_defined_vars());

        return view('admin.edit-links', compact('getUpdateLinks'));
    }

    public function updateLinks(Request $request, $id)
    {
        $request->validate([
            'icon' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        $addLink = Link::find($id);

        $addLink->url_name = $request->url_name;
        $addLink->title = $request->title;
        $addLink->sub_title = $request->sub_title;
        $image = $request->icon;
        if ($image && $request->has('icon')) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->icon->storeAs('links', $imageName);
            $addLink->icon = $imageName;
        }
        $addLink->save();

        return redirect('manage-admin/view-links')->with('message', 'Links Updated successfully!');
    }


    public function viewHome()
    {
        $view = HomeScreen::get();
        return view('mw-1.admin.home-screen.manage', get_defined_vars());
        return view('admin.view-home', compact('view'));
    }

    public function addHome()
    {
        return view('mw-1.admin.home-screen.add');
        return view('admin.add-home');
    }

    public function homeAdd(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->image;
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $request->image->storeAs('homescreen', $imageName);

        //home_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $input["home_audio"] = $request->file('home_audio')->storeAs('homeaudio', request()->file('home_audio')->extension());
        $input['title'] = $request['title'];
        $input['subtitle'] = $request['subtitle'];
        $input['duration'] = $request['duration'];
        $input['image'] = $imageName;
        // $input['session_id'] = $request['session_id'];


        if ($home = HomeScreen::create($input)) {
            return back()->with('message', 'Audio added successfully!');
        }
    }

    public function deleteHome($id)
    {
        $deleteHome = HomeScreen::find($id)->delete();

        return back()->with('message', 'Record deleted successfully!');
    }

    public function editHome($id)
    {
        $editHome = HomeScreen::find($id);
        return view('mw-1.admin.home-screen.edit', get_defined_vars());
        return view('admin.edit-home', compact('editHome'));
    }

    public function updateHome(Request $request, $id)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        //home_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $homeUpdate = HomeScreen::find($id);

        if ($request->has('home_audio')) {
            $homeUpdate['home_audio'] = $request->file('home_audio')->storeAs('homeaudio', request()->file('home_audio')->getClientOriginalName());
        }

        $homeUpdate->title = $request->title;
        $homeUpdate->subtitle = $request->subtitle;
        $homeUpdate->duration = $request->duration;

        $image = $request->image;

        if ($image && $request->has('image')) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->image->storeAs('homescreen', $imageName);
            $homeUpdate->image = $imageName;
        }

        // $input['session_id'] = $request['session_id'];
        $homeUpdate->save();
        return redirect()->route('view-home')->with('message', 'Audio updated successfully!');
    }

    public function viewEmoji()
    {
        $getEmoji = EmojiAdd::get();
        return view('mw-1.admin.emoji.manage', get_defined_vars());
        return view('admin.view-emoji', compact('getEmoji'));
    }

    public function addEmoji()
    {
        return view('mw-1.admin.emoji.add');
        return view('admin.add-emoji');
    }

    public function emojiAdd(Request $request)
    {

        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        $addEmoji = new EmojiAdd();

        $addEmoji->name = $request->name;
        $image = $request->emoji;
        if ($image && $request->has('emoji')) {
            $imageName = time() . '.' . $image->Extension();
            $request->emoji->storeAs('emoji', $imageName);
            $addEmoji->emoji = $imageName;
        }

        $addEmoji->save();

        return back()->with('message', "Emoji added successfully!");
    }

    public function deleteEmoji($id)
    {
        $deleteEmoji = EmojiAdd::find($id)->delete();
        return back()->with('message', 'Emoji deleted successfully!');
    }

    public function editEmoji($id)
    {
        $editEmoji = EmojiAdd::find($id);

        return view('mw-1.admin.emoji.edit', get_defined_vars());

        return view('admin.edit-emoji', compact('editEmoji'));
    }

    public function updateEmoji(Request $request, $id)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $addEmoji = EmojiAdd::find($id);

        $addEmoji->name = $request->name;
        $image = $request->emoji;
        if ($image && $request->has('emoji')) {
            $imageName = time() . '.' . $image->Extension();
            $request->emoji->storeAs('emoji', $imageName);
            $addEmoji->emoji = $imageName;
        }


        $addEmoji->save();

        return redirect('manage-admin/view-emoji')->with('message', "Emoji updated successfully!");
    }


    // Music module start here


    public function viewMusic()
    {
        $view = Music::get();
        return view('mw-1.admin.music.manage', get_defined_vars());

        return view('admin.view-music', compact('view'));
    }

    public function addMusic()
    {
        return view('mw-1.admin.music.add');
        return view('admin.add-music');
    }

    public function musicAdd(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->image;
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $request->image->storeAs('music', $imageName);

        //home_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $input["music_audio"] = $request->file('music_audio')->storeAs('musicaudio', request()->file('music_audio')->getClientOriginalExtension());
        $input['title'] = $request['title'];
        $input['subtitle'] = $request['subtitle'];
        $input['duration'] = $request['duration'];
        $input['image'] = $imageName;
        // $input['session_id'] = $request['session_id'];


        if ($home = Music::create($input)) {
            return redirect('manage-admin/view-music')->with('message', 'Music added successfully!');
        }
    }

    public function deleteMusic($id)
    {
        $deleteHome = Music::find($id)->delete();

        return back()->with('message', 'Record deleted successfully!');
    }

    public function editMusic($id)
    {
        $editMusic = Music::find($id);
        return view('mw-1.admin.music.edit', get_defined_vars());

        return view('admin.edit-music', compact('editMusic'));
    }

    public function updateMusic(Request $request, $id)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        //home_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $homeUpdate = Music::find($id);

        if ($request->has('music_audio')) {
            $homeUpdate['music_audio'] = $request->file('music_audio')->storeAs('musicaudio', request()->file('music_audio')->getClientOriginalName());
        }
        $homeUpdate->title = $request->title;
        $homeUpdate->subtitle = $request->subtitle;
        $homeUpdate->duration = $request->duration;
        $image = $request->image;
        if ($image && $request->has('image')) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->image->storeAs('music', $imageName);
            $homeUpdate->image = $imageName;
        }
        // $input['session_id'] = $request['session_id'];
        $homeUpdate->save();
        return redirect('/manage-admin/view-music')->with('message', 'Music updated successfully!');
    }


    // add sos course audio start here

    public function viewSosAudio()
    {
        $getAudio = SosAudio::get();
        return view('mw-1.admin.sos-audio.manage', get_defined_vars());
        return view('admin.view-sos-audio', compact('getAudio'));
    }
    public function addSosAudio()
    {
        $sessionList = DB::table("session_uploads")->where('deleted_at', null)->get();
        return view('mw-1.admin.sos-audio.add', get_defined_vars());
        return view('admin.Add-sos-audio', compact('sessionList'));
    }

    public function audioSosAdd(Request $request)
    {
        //book_audio Uploading
        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $input["sos_audio"] = $request->file('sos_audio')->storeAs('sosaudio', request()->file('sos_audio')->getClientOriginalName());
        $input['session_id'] = $request['session_id'];
        $input['duration'] = $request['duration'];
        $input['audio_title'] = $request['audio_title'];
        // $input['session_id'] = $request['session_id'];


        if ($book = SosAudio::create($input)) {
            return back()->with('message', 'Sos Audio added successfully!');
        }
    }

    public function deleteSosAudio($id)
    {
        $deleteCourse = SosAudio::find($id)->delete();
        return back()->with('message', 'Sos audio deleted successfully!');
    }


    // Home emoji module start here

    public function viewHomeEmoji()
    {
        $getEmoji = HomeEmoji::get();
        return view('mw-1.admin.home-emoji.manage', get_defined_vars());

        return view('admin.view-home-emoji', compact('getEmoji'));
    }

    public function addHomeEmoji()
    {
        return view('mw-1.admin.home-emoji.add');

        return view('admin.add-home-emoji');
    }

    public function homeEmojiAdd(Request $request)
    {
        $request->validate([
            'home_emoji' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);



        $addEmoji = new HomeEmoji();

        $addEmoji->name = $request->name;

        $image = $request->home_emoji;
        if ($image && $request->has('home_emoji')) {
            $imageName = time() . '.' . $image->Extension();
            $request->home_emoji->storeAs('homeEmoji', $imageName);
            $addEmoji->home_emoji = $imageName;
        }

        $addEmoji->save();

        return redirect('manage-admin/view-home-emoji')->with('message', "Home Emoji added successfully!");
    }

    public function deleteHomeEmoji($id)
    {
        $deleteEmoji = HomeEmoji::find($id)->delete();
        return back()->with('message', 'Home Emoji deleted successfully!');
    }

    public function editHomeEmoji($id)
    {
        $editEmoji = HomeEmoji::find($id);
        return view('mw-1.admin.home-emoji.edit', get_defined_vars());

        return view('admin.edit-home-emoji', compact('editEmoji'));
    }

    public function updateHomeEmoji(Request $request, $id)
    {
        $image = $request->home_emoji;
        $imageName = time() . '.' . $image->Extension();
        $request->home_emoji->storeAs('homeEmoji', $imageName);

        $addEmoji = HomeEmoji::find($id);

        $addEmoji->name = $request->name;
        $addEmoji->home_emoji = $imageName;

        $addEmoji->save();

        return redirect('manage-admin/view-home-emoji')->with('message', "Home Emoji updated successfully!");
    }


    // Single course module start here

    public function viewSingleCourse()
    {
        $view = SingleCourse::get();
        return view('mw-1.admin.single-course.manage', get_defined_vars());

        return view('admin.view-single-course', compact('view'));
    }

    public function addSingleCourse()
    {
        return view('mw-1.admin.single-course.add');
        return view('admin.add-single-course');
    }

    public function singleCourseAdd(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->image;
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $request->image->storeAs('SingleCourse', $imageName);

        //single course audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        // $input["sos_audio"] = $request->file('sos_audio')->storeAs('sosaudio', request()->file('sos_audio')->getClientOriginalName());

        $input["single_audio"] = $request->file('single_audio')->storeAs('singleCourse', request()->file('single_audio')->getClientOriginalName());
        $input['title'] = $request['title'];
        $input['subtitle'] = $request['subtitle'];
        $input['duration'] = $request['duration'];
        $input['image'] = $imageName;
        // $input['session_id'] = $request['session_id'];
        $input['color'] = $request['favorite_color'];


        if ($home = SingleCourse::create($input)) {
            return redirect('/manage-admin/view-single-course')->with('message', 'Single course added successfully!');
        }
    }

    public function deleteSingleCOurse($id)
    {
        $deleteHome = SingleCourse::find($id)->delete();

        return back()->with('message', 'Record deleted successfully!');
    }

    public function editSingleCourse($id)
    {
        $editHome = SingleCourse::find($id);

        return view('mw-1.admin.single-course.edit', get_defined_vars());

        return view('admin.edit-signle-course', compact('editHome'));
    }

    public function updateSingleCourse(Request $request, $id)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);



        //home_audio Uploading

        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $homeUpdate = SingleCourse::find($id);

        if ($request->has('single_audio')) {
            $homeUpdate['single_audio'] = $request->file('single_audio')->storeAs('singleCourse', request()->file('single_audio')->getClientOriginalName());
        }
        $homeUpdate->title = $request->title;
        $homeUpdate->subtitle = $request->subtitle;
        $homeUpdate->duration = $request->duration;
        $homeUpdate->color = $request->favorite_color;

        $image = $request->image;

        if ($image && $request->has('image')) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->image->storeAs('singleCourse', $imageName);
            $homeUpdate->image = $imageName;
        }

        // $input['session_id'] = $request['session_id'];
        $homeUpdate->save();
        return redirect('/manage-admin/view-single-course')->with('message', 'Single course updated successfully!');
    }


    public function viewSleepScreen()
    {
        $getSleepScreen = SleepScreen::get();
        return view('mw-1.admin.sleep-screen.manage', get_defined_vars());

        return view('admin.view-sleep-screen', compact('getSleepScreen'));
    }
    public function addSleepScreen()
    {
        return view('mw-1.admin.sleep-screen.add');
        return view('admin.add-sleep-screen');
    }

    public function SleepScreenAdd(Request $request)
    {
        //book_audio Uploading

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->image;
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $request->image->storeAs('SleepScreen', $imageName);


        @ini_set("memory_limit", "100M");
        @ini_set('post_max_size', '50M');
        @ini_set('upload_max_filesize', '50M');

        $input["sleep_audio"] = $request->file('sleep_audio')->storeAs('SleepScreen', request()->file('sleep_audio')->getClientOriginalName());

        $input['audio_title'] = $request['audio_title'];
        $input['duration'] = $request['duration'];
        $input['image'] = $imageName;
        // $input['session_id'] = $request['session_id'];


        if ($book = SleepScreen::create($input)) {
            return redirect('manage-admin/view-sleep-screen')->with('message', 'Sleep Screen Audio added successfully!');
        }
    }

    public function deleteSleepScreen($id)
    {
        $deleteCourse = SleepScreen::find($id)->delete();
        return back()->with('message', 'Sos audio deleted successfully!');
    }


    //  sleep screen second

    public function viewQuote()
    {
        $view = Quote::get();
        return view('mw-1.admin.quote.manage', get_defined_vars());

        return view('admin.view-quote', compact('view'));
    }

    public function editQuote($id)
    {
        $editQuote = Quote::find($id);

        return view('mw-1.admin.quote.edit', get_defined_vars());

        return view('admin.edit-quote', compact('editQuote'));
    }
    public function updateQuote(Request $request, $id)
    {


        $quoteUpdate = Quote::find($id);

        $quoteUpdate->name = $request->name;

        $quoteUpdate->save();
        return redirect('manage-admin/view-quote')->with('message', 'Quote updated successfully!');
    }

    public function quoteAdd(Request $request)
    {

        $input['name'] = $request['name'];



        if ($quote = Quote::create($input)) {
            return back()->with('message', 'Quote added successfully!');
        }
    }

    public function deleteQuote($id)
    {
        $deleteQuote = Quote::find($id)->delete();

        return back()->with('message', 'Record deleted successfully!');
    }
    public function addQuote()
    {
        return view('mw-1.admin.quote.add');

        return view('admin.add-quote');
    }

    public function viewPrograms(Request $request)
    {
        $status = 1;
        if ($request->has('status') && $request->status !== 'all' && in_array($status, [0, 1, 2])) {
            $status = $request->status;
        }
        $Programs = Program::query();
        if ($status != null) {
            $Programs = $Programs->where('program_type', $status);
        }
        $Programs = $Programs->get();
        return view('mw-1.admin.programs.manage', get_defined_vars());

        return view('admin.view-programs', compact('Programs'));
    }
    public function viewsession()
    {
        $Programs = Session::with('counselor')->get();
        return view('admin.view-sessions', compact('Programs'));
    }
    public function resetSession(Request $request)
    {
        CustomreBrevoData::where('program_id', $request->id)->update(['max_session' => $request->max_session]);
        return back()->with('success', 'Session Reset Successfully.');
    }
    public function addProgram($type = 1)
    {
        return view('mw-1.admin.programs.add')->with(['type' => $type]);

        return view('admin.add-program');
    }

    public function storeProgram(Request $request)
    {
        try {
            $brevo = CustomreBrevoData::where('email', $request->admin_email)->first();
            $customer = Customer::where('email', $request->admin_email)->first();
            if ($brevo || $customer) {
                return back()->with('error', 'Email has already been taken.');
            }
            if ($program = Program::create($request->all())) {
                $admin_level_employee = new CustomreBrevoData();
                $admin_level_employee->name = $request->full_name;
                $admin_level_employee->email = $request->admin_email;
                $admin_level_employee->level = 'admin';
                $admin_level_employee->company_name = $request->company_name;
                $admin_level_employee->max_session = $request->max_session;
                $admin_level_employee->program_id = $program->id;
                $admin_level_employee->save();

                $brevoService = new BrevoService();
                $brevoService->addUserToList($request->admin_email, $request->full_name, $program->code, $request->company_name, $request->max_session, 11);
                if($program && $admin_level_employee){
                    $multiLogin = new ProgramMultiLogin();
                    $multiLogin->customre_brevo_data_id = $admin_level_employee->id;
                    $multiLogin->program_id = $program->id;
                    $multiLogin->save();
                }
                if ($request->program_type == 1) {
                    $recipient = $request->admin_email;
                    $subject = 'Welcome to Mindway EAP';
                    $template = 'emails.active-program';
                    $data = [
                        'full_name' => $request->full_name,
                        'company_name' => $request->company_name,
                        'access_code' => $request->code,
                    ];
                    sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
                }
                if ($request->program_type == 2) {
                    $recipient = $request->admin_email;
                    $subject = 'Start Your 14-Day Mindway EAP Trial';
                    $template = 'emails.trial-program';
                    $data = [
                        'full_name' => $request->full_name,
                        'company_name' => $request->company_name,
                        'access_code' => $request->code,
                    ];
                    sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
                }
                $imageName = '';
                if ($request->hasFile('logo')) {
                    $image = $request->logo;
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $request->logo->storeAs('logo', $imageName);
                    $request->logo = $imageName;
                }
                $program->logo = $imageName;
                $random_password = 'Test123';
                $program->password = $random_password;
                $program->email = $request->admin_email;
                $program->program_type = $request->program_type ?? 0;

                if($request->input('allow_employees') == 'yes'){
                    $program->allow_employees = 1;
                    }else{
                    $program->allow_employees = 0;
                    }

                if ($request->program_type == 2) {
                    $program->trial_expire = Carbon::parse($request->trial_expire)->format('Y-m-d H:i:s');
                }
                $program->save();

                if ($program && $request->program_type == 1) {
                    $programPlans = new ProgramPlan();
                    $programPlans->program_id = $program->id;
                    $programPlans->plan_type = $request->plan_type;
                    $programPlans->annual_fee = $request->annual_fee;
                    $programPlans->cost_per_session = $request->cost_per_session;
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $request->renewal_date . '/' . date('Y'))->startOfDay();
                    $programPlans->renewal_date =  $formattedDate;
                    if($request->input('gst_registered') == 'yes'){
                    $programPlans->gst_registered = 1;
                    }else{
                    $programPlans->gst_registered = 0;
                    }
                    $programPlans->save();
                    
                     if ($program) {
                        foreach (json_decode($request->departments) as $key => $department) {
                            $departs = new ProgramDepartment();
                            $departs->name = $department;
                            $departs->program_id = $program->id;
                            $departs->save();
                        }
                    }
                    
                }
                return redirect()->to('/manage-admin/program/'.$program->id)->with('message', 'Program added successfully!');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
            return back()->with('message', 'Something went wrong while adding program!');
    }

    public function updateProgram(Request $request, $id)
    {
        // dd($request->all());
        try {
        $Program = Program::find($id);
        $imageName = '';

        if ($request->hasFile('logo')) {
            $image = $request->logo;
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->logo->storeAs('logo', $imageName);
            $Program->logo = $imageName;
        }

        $Program->company_name = $request->company_name;
        $Program->email = $request->email;
        $Program->max_lic = $request->max_lic;
        $Program->code = $request->code;
        $Program->link = $request->link ?? 'https://mindwayeap.com.au/booking';
        $Program->max_session = $request->max_session;
        $Program->allow_employees = $request->allow_employees;

        if ($request->program_type == 2) {
            $Program->trial_expire = Carbon::parse($request->trial_expire)->format('Y-m-d H:i:s');
        }

        // Check if password field is present in the request
        if ($request->filled('password')) {
            // Hash the password before saving
            //             $random_password = $request->password; // Set a default password
            // $hashed_password = \Hash::make($random_password);
            $random_password = 'Test123';
            $Program->password = $request->password;
        }
        $Program->program_type = $request->program_type;
        // $Program->save();

        // $employee = User::where(['email' => $request->admin_email, 'user_type' => 'employee'])->first();
        // if (!$employee) {
        //     $employee = new User();
        //     $employee->password = \Hash::make($request->full_name);
        // }
        // $employee->name = $request->full_name;
        // $employee->email = $request->admin_email;
        // $employee->user_type = 'employee';
        // $employee->save();
        // $Program->admin_id = $employee->id;

        $Program->save();
        
            $alreadyPrograms = ProgramDepartment::where('program_id', $id)->pluck('name')->toArray();
            $newPrograms = json_decode($request->departments);
            $departmentsToDelete = array_diff($alreadyPrograms, $newPrograms);
            ProgramDepartment::whereIn('name', $departmentsToDelete)->where('program_id', $id)->delete();
            $departmentsToAdd = array_diff($newPrograms, $alreadyPrograms);
            foreach ($departmentsToAdd as $department) {
                if (!ProgramDepartment::where(['name' => $department, 'program_id' => $id])->first()) {
                    $departs = new ProgramDepartment();
                    $departs->name = $department;
                    $departs->program_id = $id;
                    $departs->save();
                }
            }
        $progPlans = ProgramPlan::where('program_id', $id)->first();
        if ($request->program_type == 1) {
            if (!$progPlans) {
                $progPlans = new ProgramPlan();
            }
            $progPlans->program_id = $id;
            $progPlans->plan_type = $request->plan_type;
            $progPlans->annual_fee = $request->annual_fee;
            $progPlans->cost_per_session = $request->cost_per_session;
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->renewal_date . '/' . date('Y'))->startOfDay();
            $progPlans->renewal_date = $formattedDate;
            $progPlans->gst_registered = $request->gst_registered;
            // $progPlans->gst_registered = $request->has('gst_registered') ? 1 : 0;
            $progPlans->save();
        } else {
            if ($progPlans) {
                $progPlans->delete();
            }
        }
        return back()->with('message', 'Program updated successfully!');
        } catch (\Throwable $th) {

        }
            return back()->with('message', 'Something went wrong!');
    }
    public function updateCustomerLevel(Request $request)
    {
        $customer = CustomreBrevoData::where('id', $request->member_id)->first();
        $oldLevel = $customer->level;
        if ($customer) {
            $customer->level = $request->admin_level;
            $customer->save();
        }
            if($customer){
                $programId = $customer->program_id;
                $customerId = $customer->id;
                $multiLogin = ProgramMultiLogin::where(['program_id' => $programId, 'customre_brevo_data_id' => $customerId]
            )->first();

                if(!$multiLogin){
                    $multiLogin = new ProgramMultiLogin();
                    $multiLogin->program_id =$programId;
                    $multiLogin->customre_brevo_data_id = $customerId;
                    $multiLogin->save();
                }
            }
        if ($oldLevel == 'member' && $request->admin_level == 'admin') {
            $recipient = $customer->email;
            $subject = 'You’ve Been Made an Admin for Mindway EAP';
            $template = 'emails.become-admin-member';
            $data = [
                'full_name' => $customer->name,
                'company_name' => $customer?->program?->company_name ?? '',
                'access_code' => $customer?->program?->code ?? ''
            ];
            sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
        }
        return response()->json(['success' => true]);
    }
    public function DeactiveProgram($id, $convertTo)
    {
        $Program = Program::find($id);
        if ($convertTo == 'deactivate') {
            $Program->program_type = '0';
            $message = 'Program Deactivated';
        }
        if ($convertTo == 'active') {

            $Program->program_type = '1';
            $Program->trial_expire = null;
            $message = 'Program Activated';
            $allAdminUser = CustomreBrevoData::where('program_id',$Program->id)->where('level','admin')->get();
            foreach($allAdminUser as $user)
            {
                $recipient = $user->email;
                $subject = 'Welcome to Mindway EAP';
                $template = 'emails.active-program';
                $data = [
                    'full_name' => $user->name,
                    'company_name' => $Program->company_name,
                    'access_code' => $Program->code,
                ];
                sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
          
            }
            }
        if ($convertTo == 'delete') {
            $Program->delete();
            $message = 'Program Deleted';
        }
        if ($convertTo == 'extend_trial') {

            $Program->trial_expire = Carbon::now()->addDays(14)->format('Y-m-d H:i:s');
            $Program->program_type = '2';
            $message = 'Program Trial Extended by 14 days';
            $allAdminUser = CustomreBrevoData::where('program_id',$Program->id)->where('level','admin')->get();
            foreach($allAdminUser as $user)
            {
            $recipient = $user->email;
            $subject = 'Start Your 14-Day Mindway EAP Trial';
            $template = 'emails.trial-program';
            $data = [
                'full_name' => $user->name,
                'company_name' => $Program->company_name,
                'access_code' => $Program->code,
            ];
            sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
        }
        }
        if ($convertTo !== 'delete') {
            $Program->save();
        }
        return redirect('/manage-admin/view-programs?status=1')->with('message', $message);
    }


    public function SingleProgram($id)
    {
        $Program = Program::find($id);

        $ProgramPlan = ProgramPlan::where('program_id', $id)->first();
        // Get all customers
        $getCustomer = Customer::get();

        // Get all programs with their associated customers
        $Programs = Program::with('customers')->get();

        $employee = User::where('id', $Program->admin_id)->first();

        // Initialize an empty array to store customer records
        $customerData = [];
        $programDepart = ProgramDepartment::where('program_id', $id)->pluck('name')->toArray();
        // dd($programDepart);
        $totalCount = count($customerData);
        $customerPercentage = $totalCount  / 100;
        $customers = CustomreBrevoData::where('program_id', $id)->get();
        $totalCustomers = $customers->count();
        return view('mw-1.admin.programs.edit', get_defined_vars());

        //dd($Program->customers);
        return view('admin.edit-program', compact('Program', 'customerPercentage', 'totalCustomers', 'customers'));
    }

    public function RemoveReddemCode($customerId, $programId)
    {
        $customer = CustomreBrevoData::where('id', $customerId)->where('program_id', $programId)->first();
        if ($customer) {
            $brevo = new BrevoService();
            $brevo->removeUserFromList($customer->email);
            $customer->delete();
            $customer3 = CustomerRelatedProgram::where('customer_id', $customerId)->first();
            if($customer3)
            {
                $brevo = new BrevoService();
                $brevo->removeUserFromList($customer3->email);
                $customer3->delete();
            }
            if($customer->app_customer_id)
            {
                Customer::where('id',$customer->app_customer_id)->delete();
            }
        }

        return back()->with('message', 'Record deleted successfully!');
    }
    public function MinusSession($customerId, $programId)
    {

        $customer = CustomreBrevoData::find($customerId);

        if ($customer) {
            $customer->max_session = $customer->max_session - 1;
            $customer->save();
        }

        return back()->with('message', 'Session updated successfully!');
    }
    public function PlusSession($customerId, $programId)
    {
        $customer = CustomreBrevoData::find($customerId);
        if ($customer) {
            // Detach the program from the customer
            $customer->max_session = (int)$customer->max_session + 1;
            $customer->save();
            if(isset($customer->app_customer_id))
            {
                $customer3 = Customer::where('id',$customer->app_customer_id)->first();
                if($customer3){
                     $customer3->max_session = (int)$customer->max_session;
                    $customer3->save();
                }
            }
        }

        return back()->with('message', 'Session updated successfully!');
    }
    public function update(Request $request)
    {

        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));

        $apiInstance = new ContactsApi(
            new Client(),
            $config
        );

        // Check if the identifier is provided and not empty
        if (!empty($request->identifier)) {
            $identifier = $request->identifier;
            $updateContact = new UpdateContact();
            $updateContact['attributes'] = array('EMAIL' => $request->email, 'FIRSTNAME' => $request->name);

            try {
                $apiInstance->updateContact($identifier, $updateContact);

                // Assuming Customer model is being used to update the customer information in your application
                $customer = Customer::findOrFail($request->id);
                $customer->update($request->all());

                return response()->json(['success' => true, 'message' => 'Customer information updated successfully']);
            } catch (Exception $e) {
                // Handle any exceptions that occur during the API call
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            // Return an error response if the identifier is missing or empty
            return response()->json(['success' => false, 'message' => 'Missing identifier parameter']);
        }
    }

    public function store(Request $request)
    {
       
        // Initialize the reason array
        $reasons = [];

        // Check if checkboxes are present and append their values to the reason array
        if ($request->has('work_related')) $reasons[] = $request->input('work_related');
        if ($request->has('work_stress')) $reasons[] = $request->input('work_stress');
        if ($request->has('workplace_conflicts')) $reasons[] = $request->input('workplace_conflicts');
        if ($request->has('harassment_bullying')) $reasons[] = $request->input('harassment_bullying');
        if ($request->has('performance_issues')) $reasons[] = $request->input('performance_issues');
        if ($request->has('organisational_change')) $reasons[] = $request->input('organisational_change');
        if ($request->has('burnout')) $reasons[] = $request->input('burnout');
        if ($request->has('other')) $reasons[] = $request->input('other');
        if ($request->has('other_reason')) $reasons[] = $request->input('other_reason');
        if ($request->has('person_related')) $reasons[] = $request->input('person_related');

        // dd($reasons);
        // Concatenate reasons into a single string
        $reasonString = implode(', ', $reasons);
        $reasonStrings = rtrim($reasonString, ', ');

        // Get new_user value
        $newUser = $request->input('new_user', 'No');
        try
        {
             $upcomingBookings = Booking::where('counselor_id', $request->counselor_id )
            ->where('slot_id', $request->slot_id)
            ->first();
           
            if($upcomingBookings)
            {
                $upcomingBookings->status = 'completed';
                $upcomingBookings->save();
            }
        }
        catch (\Throwable $th) {
            
        }
        // Fetch data from Session model using program_id
        try
        {
            $customer3 = Customer::where('id',$request->customerId)->first();
            $customer3->max_session = $customer3->max_session - 1;
            $customer3->save();
        }
        catch (\Throwable $th) {
            //throw $th;
        }
        if ($request->type == 'upcomingSession') {
            $sessionData = CustomreBrevoData::where('app_customer_id', $request->customerId)->first();
            
        } else {
            $sessionData = CustomreBrevoData::where('id', $request->customerId)->first();
        }
        if ($sessionData) {
            Session::create([
            'session_date' => $request->sessionDate,
            'session_type' => $request->sessionType,
            'reason' => $reasonStrings,
            'new_user' => $newUser,
            'program_id' => $request->programId??$sessionData->program_id,
            'department_id' => $sessionData?->department_id??null,
            'counselor_id' => $request->counselor_id ?? null
        ]);
            $sessionData->max_session = $sessionData->max_session - 1;
            $sessionData->is_counselling_user = true;
            $sessionData->save();
            CounsellingSession::create([
                'session_date' => $request->sessionDate,
                'session_type' => $request->sessionType,
                'reason' => $reasonStrings,
                ' ' => $newUser,
                'program_id' => $request->programId??$sessionData->program_id,
                'company_name' => $sessionData->company_name,
                'name' => $sessionData->name,
                'email' => $sessionData->email,
                'counselor_id' => $request->counselor_id ?? null,
                'department_id' => $sessionData?->department_id??null,
                'max_session' => $sessionData->max_session, // Assuming you want to store this as well
            ]);
            return redirect()->back()->with('success', 'Session data saved successfully.');
        } else {
            return redirect()->back()->with('error', 'Something wrong with the customer connection.');
        }
    }

    public function counsellorDisp()
    {

        $counsellors = Counselor::all();
        $path = public_path('mw-1' . DIRECTORY_SEPARATOR . 'timezones.json');
        $json = File::get($path);
        $timezones = json_decode($json, true);

        return view('mw-1.admin.counsellor.manage', get_defined_vars());
    }

    public function counsellorManage($id)
    {
        $Counselor = Counselor::where('id', $id)->first();
        $user_id = $Counselor->id;
        $customers = CustomreBrevoData::all();
        $upcomingBookings = Booking::with(['user', 'counselor', 'slot'])
            ->where('counselor_id', $Counselor?->id)
            ->where('status', 'confirmed')
            ->whereHas('slot', function ($query) {
                // $query->where('start_time', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
        $CounselorSession = CounsellingSession::with('counselor')->get();
        $timezone = $Counselor->timezone??'UTC';
        return view('mw-1.admin.counsellor.counsellor-manage', get_defined_vars());
    }

    public function counsellerCancelSession(Request $request)
    {
        $booking = Booking::findOrFail($request['booking_id']);
        if ($booking->status == 'cancelled') {
            return redirect()->back()->with('error', 'Booking already cancelled successfully');
        }
        $user_id = $request['customer_id'];
        if ((int)$booking->user_id !== (int)$user_id) {
            return redirect()->back()->with('error', 'Not allowed to cancel the session');
        }

        $booking->update(['status' => 'cancelled']);
        $booking->slot->update(['is_booked' => false]);
        $customer = Customer::find($user_id);
        return redirect()->back()->with('success', 'Booking cancelled successfully');
    }

    public function timezones()
    {
        $path = public_path('mw-1' . DIRECTORY_SEPARATOR . 'timezones.json');
        $json = File::get($path);
        $timezones = json_decode($json, true);
        return $timezones;
    }
    public function counsellorProfile($id)
    {
        $Counselor = Counselor::where('id', $id)->first();
        $path = public_path('mw-1' . DIRECTORY_SEPARATOR . 'timezones.json');
        $json = File::get($path);
        $timezones = json_decode($json, true);

        return view('mw-1.admin.counsellor.counsellor-profile', get_defined_vars());
    }
    public function counsellorAvailability($id)
    {
        $Counselor = Counselor::where('id', $id)->first();
        $currentTimezone = $Counselor->timezone;
        $availability = $Counselor->availabilities()->get();
        $availabilityData = [];
        $timezones = $this->timezones();
        
        foreach ($availability as $schedule) {
            $startTimeInCounselorTimezone = Carbon::parse($schedule->start_time)->setTimezone($currentTimezone);
            $endTimeInCounselorTimezone = Carbon::parse($schedule->end_time)->setTimezone($currentTimezone);

            $availabilityData[$schedule->day] = [
                'available' => $schedule->available,
                'start_time' => $startTimeInCounselorTimezone->format('H:i'),
                'end_time' =>   $endTimeInCounselorTimezone->format('H:i'),
            ];
        }
       
        return view('mw-1.admin.counsellor.counsellor-availability', get_defined_vars());
    }

    public function availabilitySave(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'availability_data' => 'required|array', // Make sure availability data is in JSON format
            // 'timezone' => 'nullable|string', // Make sure timezone is a nullable string (optional)
            'counselorId' => 'required'
        ]);

        
        $availabilityData = $validated['availability_data'];

      
        $user_id = $request->counselorId;
        $counselor = Counselor::findOrFail($user_id);
        // if ($validated['timezone']) {
        //     $counselor->timezone = $validated['timezone'];
        //     $counselor->save();
        // }
        // Start a database transaction to ensure data integrity
        \DB::beginTransaction();

        try {
            // Clear existing availability
            $counselor->availabilities()->delete();

          
            foreach ($availabilityData as $dayAvailability) {
                
                $startTime = Carbon::parse($dayAvailability['start_time'], $counselor->timezone);
                $endTime = Carbon::parse($dayAvailability['end_time'], $counselor->timezone);

                $startTimeUtc = $startTime->setTimezone('UTC');
                $endTimeUtc = $endTime->setTimezone('UTC');

                $startTimeForSave = $startTimeUtc->format('H:i:s');
                $endTimeForSave = $endTimeUtc->format('H:i:s');

                if ($dayAvailability['start_time'] && $dayAvailability['end_time']) {
                    Availability::create([
                        'counselor_id' => $counselor->id,
                        'day' => $dayAvailability['day_of_week'],
                        'start_time' => $startTimeForSave,
                        'end_time' => $endTimeForSave,
                        'available' => true,
                    ]);
                }
            }
            \DB::commit();

            // Update the counselor's timezone (if provided)
            // if ($validated['timezone']) {
            //     $counselor->timezone = $validated['timezone'];
            //     $counselor->save();
            // }

            // Generate slots based on new availability
             $counselor->slots()->where('is_booked', false)->delete();
            app(SlotGenerationService::class)->generateSlotsForCounselor($counselor);

            return back()->with('message', 'Data saved successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            \DB::rollBack();

            // Log the error (optional)
            \Log::error('Error updating availability', ['error' => $e->getMessage()]);
            return back()->with('message', 'Data saved successfully.');
        }
    }

    public function profileSave(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $counsellorId = $request->counsellorId;
        $Counselor = Counselor::where('id', $counsellorId)->first();
        $specilization = explode(",", $request->tags); // $request->specialisations;
        // dd($specilization);

        // $Counselor->timezone = $request->timezone;
        $Counselor->description = $request->description;
        $Counselor->gender = $request->gender;
        $Counselor->intake_link = $request->intake_link;
        $Counselor->notice_period = $request->notice_period;

        $imageName = '';
        if ($request->hasFile('logo')) {
            $image = $request->file('logo'); // Use `file()` for clarity
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('logo', $imageName); // Saves to storage/logo
            $Counselor->avatar = $imageName;
        }
        $Counselor->communication_method = json_encode($request->communication_methods);
        $Counselor->specialization = json_encode($specilization);
        $Counselor->save();
        if(isset($request->notice_period))
        {
            $Counselor->slots()->where('is_booked', false)->delete();
            $month = now()->addMonth()->month;
            app(SlotGenerationService::class)->generateSlotsForCounselor($Counselor);
            app(SlotGenerationService::class)->generateSlotsForCounselor($Counselor,$month);
            
        }
        return back()->with(['message' => "Information Saved Successfully"]);
    }


    public function SaveCounselorLogo(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $Counselor = Counselor::where('id', $request->counsellorId)->first();
        $imageName = '';
        if ($request->hasFile('logo')) {
            $image = $request->file('logo'); // Use `file()` for clarity
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('logo', $imageName); // Saves to storage/logo
            $Counselor->avatar = $imageName;
        }
        $Counselor->save();
        // return back()->with(['message' => "Information Saved Successfully"]);

        return response()->json(['status' => 'success', 'message' => 'Image Saved Successfully']);
    }


    public function storeCounsellor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:counselors,email',
            'gender' => 'required',
        ]);
        $tags = $request->input('tags', '');
        $specializationArray = $tags ? explode(',', $tags) : [];
        $user = Counselor::create([
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description,
            'password' => bcrypt('Test123'),
            'gender' => $request->gender,
            'timezone' => $request->timezone,
            'specialization' => json_encode($specializationArray),
            'communication_method' => json_encode($request->communication_method)
        ]);

        $recipient = $request->email;
        $subject = 'Welcome to Mindway EAP – Set Up Your Profile';
        $template = 'emails.counsellor-setup-profile';
        $token = encrypt($user->id);
        $resetLink = url("/counsellor/set-password-view/?token={$token}&email={$request->email}&type=counsellorSetPassword&name{$request->name}");
        $data = [
            'full_name' => $request->name,
            'resetLink' => $resetLink,
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
        return redirect()->back()->with('message', 'Counsellor added successfully!');
    }

    public function saveDataEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customre_brevo_data', // Add unique validation for email
        ]);
        $userId = $request->programId;
        $program = Program::where('id', $userId)->first();
        $customer = new CustomreBrevoData();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->program_id =  $userId;
        $customer->company_name = $program->company_name;
        $customer->max_session = $program->max_session;
        $customer->save();
        // Retrieve the newly created customer's ID
        $customerId = $customer->id;
        // Create a record in the customer_related_program table
        $customerRelatedProgram = new CustomerRelatedProgram();
        $customerRelatedProgram->customer_id = $customerId;
        $customerRelatedProgram->program_id = $userId; // Use the program ID obtained earlier
        $customerRelatedProgram->save();
        // Set up the SendinBlue API configuration
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));

        // Create an instance of the ContactsApi
        $apiInstance = new ContactsApi(new Client(), $config); // Use the correct Client class

        // Prepare the data for creating the contact
        $createContact = new CreateContact([
            'email' => $request->email,
            'attributes' => (object) [
                'EMAIL' => $request->email,
                'FIRSTNAME' => $request->name,
                'CODEACCESS' => $program->code,
                'COMPANY' => $program->company_name,
                'MS' => $program->max_session,
                'LASTNAME' => ""
            ],
            'listIds' => [9], // Assuming you want to add the contact to list ID 1
        ]);


        try {
            // Make the request to create the contact
            $result = $apiInstance->createContact($createContact);

            // Process the response as needed
            // return response()->json($result);
            return back()->with('message', 'Record added successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            // Catch the specific exception for duplicate entry constraint
            if ($e->getCode() == 23000) {
                return back()->with('error', 'User is already registered. Duplicate emails are not allowed.');
            } else {
                // Handle other query exceptions
                return back()->with('error', $e->getMessage());
            }
        } catch (Exception $e) {
            // Handle other exceptions
            return back()->with('error', $e->getMessage());
        }
    }
    public function saveDataEmployeeInBulk(Request $request)
    {
        set_time_limit(0);
        $userId = $request->programId;
        $finalData = json_decode($request->input('finalData'), true);
        if ($finalData) {
            $Program = Program::where('id', $userId)->first();
            foreach ($finalData as $employee) {

                if ($employee['email'] && $employee['email'] !== null) {

                    $customer = CustomreBrevoData::where('email', $employee['email'])->first();
                    if (!$customer) {
                        $customer = new CustomreBrevoData();
                        $customer->email = $employee['email'];
                        $customer->name = $employee['name'];
                        $customer->company_name = $Program->company_name;
                        $customer->max_session = $Program->max_session;
                        $customer->program_id = $userId;
                        $customer->level = 'member';
                        $customer->save();
                    }
                }
            }
        }
        return back()->with('message', 'Record added successfully');
        
    }
}
