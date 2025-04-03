<?php

use App\Http\Controllers\api\Admin\SessionController;
use App\Http\Controllers\api\BookingController;
use App\Http\Controllers\api\CounselorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CounsellerController;
use App\Http\Controllers\UserPreferenceController;
use App\Models\Customer;
use App\Models\CustomreBrevoData;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/reddemprogramcode', [CustomerController::class, 'ReddemProgramCode']);
Route::post('/get-code-information', [CustomerController::class, 'getCodeInformation']);

Route::get('/getuserprograms', [CustomerController::class, 'getuserprograms']);
Route::post('/updatedeviceid', [CustomerController::class, 'updatedeviceid']);



Route::group(["prefix" => "customer", "middleware" => ["auth:sanctum"]], function () {
  Route::post('/login', [CustomerController::class, 'login'])->withoutMiddleware('auth:sanctum');
  Route::post('/send-password-reset-otp', [CustomerController::class, 'passwordReset'])->withoutMiddleware('auth:sanctum');
  Route::post('/find-customer-by-email', [CustomerController::class, 'findMe'])->withoutMiddleware('auth:sanctum');
  Route::post('verify-otp', [CustomerController::class, 'verifyOTP'])->withoutMiddleware('auth:sanctum');
  Route::post('register-by-email', [CustomerController::class, 'registerByEmail'])->withoutMiddleware('auth:sanctum');
  Route::post('/signup', [CustomerController::class, 'register'])->withoutMiddleware('auth:sanctum');
  Route::post('/verify-signup', [CustomerController::class, 'verifySignup'])->withoutMiddleware('auth:sanctum');
  
  Route::post('/forget-password', [CustomerController::class, 'forgetPassword'])->withoutMiddleware('auth:sanctum');
  Route::post('/reset-password', [CustomerController::class, 'resetPassword'])->withoutMiddleware('auth:sanctum');

  Route::get('/get-counselor-calendar/{id}', [CounsellerController::class, 'getCounselorCalendar']);
  Route::delete('/remove-session/{sessionId}', [CounsellerController::class, 'removeSession'])->name('remove-session');
  Route::post('/book-session', [CounsellerController::class, 'bookSession'])->name('book-session');

  Route::post('/update/profile', [CustomerController::class, 'updateProfile']);
  Route::post('/logout', [CustomerController::class, 'logout']);


  Route::get('/preferences', [UserPreferenceController::class, 'index']);
  Route::post('/preferences', [UserPreferenceController::class, 'store']);
  Route::delete('/preferences', [UserPreferenceController::class, 'destroy']);


  Route::post('/add-sessions', [SessionController::class, 'addSession']);
  Route::post('/session-audio', [SessionController::class, 'uploadSessionAudio']);
  Route::post('/get-session', [SessionController::class, 'getSession']);

  // journal routes
  Route::post('/add-journal', [SessionController::class, 'addJournal']);
  Route::post('/edit-journal', [SessionController::class, 'editJournal']);
  Route::get('/delete-journal', [SessionController::class, 'deleteJournal']);
  Route::post('/get-journal', [SessionController::class, 'getJournal']);

  // get notify route
  Route::post('/get-notify', [CustomerController::class, 'getNotify']);

  // Add category
  Route::post('/add-category', [SessionController::class, 'addCategory']);
  Route::get('/get-category', [SessionController::class, 'getCategory']);

  // Category course api

  Route::get('/get-sleep-course', [SessionController::class, 'getSleepCourse']);


  // Links category
  Route::get('/get-links', [SessionController::class, 'getLinks']);

  // home screen
  Route::get('/get-home', [SessionController::class, 'getHome']);
  Route::get('/get-music', [SessionController::class, 'getMusic']);
  // add emoji api
  Route::post('/add-emoji', [SessionController::class, 'addEmoji']);
  Route::get('/get-emoji', [SessionController::class, 'getEmoji']);

  Route::get('/get-random-course', [SessionController::class, 'getRandomCourse']);
  // sleep screen api
  Route::get('/get-sleep-screen', [SessionController::class, 'getSleepScreen']);

  // home emoji api
  Route::get('/get-home-emoji', [SessionController::class, 'getHomeEmoji']);

  // Quote screen api
  Route::get('/get-quote', [SessionController::class, 'getQuote']);

  // Quote screen api
  Route::get('/get-quote/{id}/{date}', [SessionController::class, 'getQuote']);

  // single course api
  Route::get('/get-single-course', [SessionController::class, 'getSingleCourse']);

  Route::get('/get-course/{id}/{course_order_by}', [SessionController::class, 'getCourse']);
  Route::get('/get-user/{id}', [CustomerController::class, 'getUser']);
  Route::get('/get-home-sleep-audio/{id}/{date}', [SessionController::class, 'getHomeSleepAudio']);


  // Counselor routes
  Route::get('/counselors', [CounselorController::class, 'getCounselors']);
  Route::get('/get-paginated-counselors-data', [CounselorController::class, 'getCounselorsPagination']);
  Route::get('/get-preference-info', [CounselorController::class, 'getPreferenceInfo']);
  Route::post('/counselor/availability', [CounselorController::class, 'setAvailability']);
  Route::get('/counselor/calendar', [CounselorController::class, 'getCalendarAvailability']);
  Route::get('/counselor/upcoming-sessions', [CounselorController::class, 'getUpcomingSessions']);
  Route::get('/upcoming-sessions', [CounselorController::class, 'getCustomerUpcomingSessions']);

  // Booking routes
  Route::get('/available-slots', [BookingController::class, 'getAvailableSlots']);
  Route::post('/book-slot', [BookingController::class, 'bookSlot']);
  Route::post('/reschedule-slot', [BookingController::class, 'rescheduleSlot']);
  Route::post('/reserved-slot', [BookingController::class, 'reservedSlot']);
  Route::post('/cancel-booking', [BookingController::class, 'cancelBooking']);
});
Route::post('/goalupdate/{email}/goal/{goal_id}', [CustomerController::class, 'updateGoalIdByEmail']);
Route::get('/get-test', function () {
  return "api is ok";
});

Route::get('/sync-department-data',function()
{
  $customer = Customer::whereNotNull('department_id')->get();
  foreach($customer as $cust)
  {
    $brevoData = CustomreBrevoData::where('app_customer_id', $cust->id)->first();
    if($brevoData){
        $departId = $cust->department_id;
        $brevoData->department_id = $departId;
        $brevoData->save();
    }
  }
  
});
Route::get('/sync-old-data', function () {
// $customer = App\Models\Customer::whereHas('program')->with('program')->get();
// foreach($customer as $key => $user)
// {
//     $brevoTable = App\Models\CustomreBrevoData::where('email',$user->email)->first();  
    
//     if($brevoTable)
//     {
       
//         $brevoTable->app_customer_id = $user->id;
//         $brevoTable->is_app_user = true;
//         $brevoTable->max_session = $user?->single_program?->max_session;
//         $brevoTable->save();
//         $user->max_session = $user?->single_program?->max_session;
//         $user->save();
//     }
//     else
//     {
//         $brevo = new App\Models\CustomreBrevoData();
//         $brevo->name = $user->name;
//         $brevo->email = 'dummyemail' . time() .$key. '@example.com';
//         $brevo->program_id = $user?->single_program?->id;
//         $brevo->company_name = $user?->single_program?->company_name;
//         $brevo->max_session = $user?->single_program?->max_session;
//         $brevo->is_app_user = true;
//         $brevo->app_customer_id = $user->id;
//         $brevo->save();
//     }

//  }

    $counsellingSession = App\Models\CounsellingSession::whereHas('brevoUser')->get();
    foreach($counsellingSession as $session)    
    {
        $brevoUser = App\Models\CustomreBrevoData::where('email',$session->email)->first();
        if($brevoUser)
        {
            $brevoUser->is_counselling_user = 1;
            $brevoUser->save();
        }
        else
        {
            dd($session);
        }
    }
    return "api is ok";
});

Route::post('/google/calendar/webhook', [GoogleController::class, 'handleWebhook'])->name('google.calendar.webhook');
