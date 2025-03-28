<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CounsellerController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RegistrationController;

Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/oauth2/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('calendar/event', [GoogleController::class, 'createEvent'])->name('calendar.event.create');
Route::get('/calendar/events', [GoogleController::class, 'listEvents'])->name('calendar.event.list');
Route::get('calendar/create', function () {
    return view('create-event');
})->name('calendar.create');


Route::post('/counsellor/set-password', [PasswordResetController::class, 'counsellorPasswordSet'])->name('counsellor.password.post');

Route::get('/counsellor/set-password-view', [PasswordResetController::class, 'showCounsellorSetPassword']);


Route::get('/forgot-password/{type}', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
Route::get('app-reset-password/{token}', [PasswordResetController::class, 'showResetPasswordAppForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
Route::post('/reset-app-password', [PasswordResetController::class, 'resetAppPassword'])->name('password.app.update');


Route::get('/program-signup', [RegistrationController::class, 'programSignup'])->name('program.signup');
Route::post('/manage-program/signup', [RegistrationController::class, 'submitSignUp']);
Route::get('/manage-program/otp', [RegistrationController::class, 'programOtp'])->name('program.otp');

Route::get('/manage-program/setpassword', [RegistrationController::class, 'setPassword'])->name('program.setpassword');

Route::get('/manage-program/resendotp', [RegistrationController::class, 'resendOTP']);


Route::post('/manage-program/verify-otp', [RegistrationController::class, 'verifyOtp']);
Route::post('/set-password-and-set-acc', [RegistrationController::class, 'setPassAndAccs']);

Route::get('/2fa', [TwoFactorController::class,'show'])->name('2fa');
Route::get('/counselor/2fa', [TwoFactorController::class,'counselorShow'])->name('counselor.2fa');
Route::get('/program/2fa', [TwoFactorController::class,'programShow'])->name('program.2fa');
Route::post('/2fa', [TwoFactorController::class,'verify'])->name('2fa.verify');
Route::post('/counselor/2fa', [TwoFactorController::class,'counselorVerify'])->name('counselor.2fa.verify');
Route::post('/program/2fa', [TwoFactorController::class,'programVerify'])->name('program.2fa.verify');

Route::get('/dashboard-setup', function () {
  return view('mw-1.layout.app');
});

  Route::get('/clear', function() {
   Artisan::call('cache:clear');
   Artisan::call('config:clear');
   Artisan::call('config:cache');
   Artisan::call('view:clear');
   Artisan::call('route:clear');
   Artisan::call('route:cache');

   return "Cleared!";

});

Route::get('/counsellinglogin',[CounsellerController::class,'seecounselling'])->name('counseller.login');
Route::post('/counsellersesion', [CounsellerController::class,'checkLoginCounseler'])->name('counsellersesion');
Route::get('/counseller/dashboard', [CounsellerController::class,'counselorDashboard'])->name('counseller.dashboard');


//Middleware for the counsellor and routes are defined

Route::group(['middleware' => ['counsellor']], function () {

Route::get('/counsellor-logout', [CounsellerController::class,'logout'])->name('counsellor.logout');


Route::post('/sessions/store', [CounsellerController::class, 'store'])->name('session.store');
Route::get('/counsellersesions', [CounsellerController::class,'index'])->name('counsellersesion.index');

Route::get('/counsellersesions/data', [CounsellerController::class, 'getCounsellerSesions'])->name('admin.counsellersesions-data');
Route::get('/counsellerhome', [CounsellerController::class,'counsellerhome'])->name('counseller.home');

Route::get('/counselleravailability', [CounsellerController::class,'counsellerAvailability'])->name('counseller.availability');

Route::get('/counsellerprofile', [CounsellerController::class,'counsellerProfile'])->name('counseller.profile');
Route::get('/counseller/setting', [CounsellerController::class,'setting'])->name('counseller.setting');
Route::post('/counseller-setting-save', [CounsellerController::class,'counsellerSettingSave'])->name('counseller.setting-save');
Route::get('/counseller-session-cancel', [CounsellerController::class,'counsellerCancelSession'])->name('counselor.session.cancel');
Route::post('/counseller-session-rebook', [CounsellerController::class,'counsellerRebbokSession'])->name('counselor.session.rebook');
Route::post('/profile-save', [CounsellerController::class, 'profileSave'])->name('counseller.profileSave');
});

Route::post('/save-timezone', [CounsellerController::class, 'saveTimezone'])->name('timezone.store');

Route::post('/save-counsellor-logo',[CounsellerController::class,'saveCounsellorLogo']);
Route::post('/save-counsellor-intro-video',[CounsellerController::class,'SaveCounselorIntroVideo']);

// Route::post('/save-timezone', [CounsellerController::class, 'saveTimezone'])->name('timezone.store');
Route::post('/availability-save', [CounsellerController::class, 'setAvailability'])->name('counseller.availabilitySave');
Route::get('/fetch-counsellor-availability', [CounsellerController::class, 'fetchCounsellorAvailability'])->name('counseller.availabilitySave');

Route::get('/', function () {
  return redirect ("/manage-admin/login");
});
Route::get('/login', function () {
    return redirect ("/manage-admin/login");
});
Route::get('/programlogin', function () {
  return redirect ("/manage-program/login");
});

Route::group(['prefix' => 'manage-admin'], function () {
  Route::post('/admin_login', [AdminController::class,'checkLogin']);
  Route::get('/login',[AdminController::class,'Login'])->name('login');
});
Route::post('/update-customer-level',[AdminController::class,'updateCustomerLevel']);

Route::group(['prefix' => 'manage-admin', 'middleware' => ['auth']], function () {
  Route::get('/setting',[AdminController::class,'setting'])->name('admin.setting');
  Route::post('/setting',[AdminController::class,'saveSetting'])->name('admin.save-setting');
  Route::post('/add-counselor',[AdminController::class,'Addcounselor']);
  Route::post('/save-counsellor-logo',[AdminController::class,'SaveCounselorLogo']);
  Route::post('/save-counsellor-intro-video',[AdminController::class,'SaveCounselorIntroVideo']);
    Route::get('/view-dashboard',[AdminController::class,'viewCustomer'])->name('admin.view-dashboard');
    Route::get('/users/data', [AdminController::class, 'getUsers'])->name('admin.users-data');
    Route::get('/delete-customer/{id}',[AdminController::class,'deleteCustomer']);
    Route::put('/customer/update', [AdminController::class,'update'])->name('customer.update');

   Route::get('/clear', function() {

   Artisan::call('cache:clear');
   Artisan::call('config:clear');
   Artisan::call('config:cache');
   Artisan::call('view:clear');
   Artisan::call('route:clear');
   Artisan::call('route:cache');

   return "Cleared!";

});
// session routes
Route::post('/sessions/store', [AdminController::class, 'store'])->name('admin.sessions.store');

    // Add course routes
    Route::get('/view-course',[AdminController::class,'viewCourse']);
    Route::get('/viewCourse/data', [AdminController::class, 'getViewCourse'])->name('admin.viewcourse-data');
      Route::get('/add-audio',[AdminController::class,'addAudio']);
      Route::post('/insert-audio',[AdminController::class,'insertAudio']);
      Route::get('/view-audio',[AdminController::class,'viewAudio'])->name('view-audio');
      Route::get('/view-audio/data', [AdminController::class, 'getViewAudio'])->name('admin.viewaudio-data');
    Route::get('/view-audio',[AdminController::class,'viewAudio']);
    Route::get('/add-course',[AdminController::class,'addCourse']);
    Route::post('/course-add',[AdminController::class,'courseAdd']);
    Route::get('/edit-course/{id}',[AdminController::class,'editCourse']);
    Route::post('/update-course/{id}',[AdminController::class,'updateCourse']);
    Route::get('/delete-course/{id}',[AdminController::class,'deleteCourse']);
    Route::get('/delete-audio/{id}',[AdminController::class,'deleteAudio']);
    Route::get('/edit-audio/{id}',[AdminController::class,'editAudio']);
        Route::post('/update-audio',[AdminController::class,'updateAudio']);


    //Counsellor in the admin side

    Route::get('/counsellor',[AdminController::class,'counsellorDisp']);
    Route::get('/counsellor-manage/{id}',[AdminController::class,'counsellorManage']);
    Route::get('/counsellor-availability/{id}',[AdminController::class,'counsellorAvailability']);

    Route::get('/counsellor-session/data', [AdminController::class, 'counsellorSession'])->name('admin.counsellor-session');
    Route::get('/counsellor/data', [AdminController::class, 'getCounsellor'])->name('admin.counsellor-data');
    Route::get('/counseller-session-cancel', [AdminController::class,'counsellerCancelSession'])->name('session.cancel');
    Route::post('/counseller-session-rebook', [AdminController::class,'counsellerRebbokSession'])->name('session.rebook');
    Route::get('/counsellor-profile/{id}',[AdminController::class,'counsellorProfile'])->name('admin.counsellor.profile');
    Route::post('/availability-save',[AdminController::class,'availabilitySave'])->name('manage-admin.availabilitySave');
    Route::post('/profile-save',[AdminController::class,'profileSave'])->name('manage-admin.profileSave');
    Route::post('/add-counsellor',[AdminController::class,'storeCounsellor'])->name('manage-admin.addCounsellor');
    Route::get('/add-sos-audio',[AdminController::class,'addSosAudio']);
    Route::post('/sos-audio-add',[AdminController::class,'audioSosAdd']);
    Route::get('/view-sos-audio',[AdminController::class,'viewSosAudio']);
    Route::get('/view-sos-audio/data', [AdminController::class, 'getViewSosAudio'])->name('admin.viewsosaudio-data');

    Route::get('/delete-sos-audio/{id}',[AdminController::class,'deleteSosAudio']);
    Route::get('/view-sleep-course',[AdminController::class,'viewSleepCourse']);
    Route::get('/view-sleep-course/data', [AdminController::class, 'getViewSleepCourse'])->name('admin.viewsleepcourse-data');
    Route::get('/view-sleep-audio',[AdminController::class,'viewSleepAudio']);
    Route::get('/view-sleep-audio/data', [AdminController::class, 'getViewSleepAudio'])->name('admin.viewsleepaudio-data');
    Route::get('/edit-sleep-audio/{id}',[AdminController::class,'editSleepAudio']);
    Route::get('/add-sleep-course',[AdminController::class,'addSleepCourse']);
    Route::post('/sleep-course-add',[AdminController::class,'sleepCourseAdd']);
    Route::get('/edit-sleep-course/{id}',[AdminController::class,'editSleepCourse']);
    Route::post('/update-sleep-course/{id}',[AdminController::class,'updateSleepCourse']);
    Route::get('/delete-sleep-course/{id}',[AdminController::class,'deleteSleepCourse']);
    Route::get('/delete-sleep-audio/{id}',[AdminController::class,'deleteSleepAudio']);
      Route::post('/sleep-audio-add',[AdminController::class,'sleepAudioAdd']);
      Route::post('/update-sleep_audio/{id}',[AdminController::class,'updateSleepAudio']);
    Route::get('/add-sleep-audio',[AdminController::class,'addSleepAudio']);
    // Logout route
    Route::post('/logout',[AdminController::class,'logout'])->name('logoutadmin');
    // Account links
    Route::get('/view-links',[AdminController::class,'viewLinks']);

    Route::get('/view-links/data', [AdminController::class, 'getViewLinks'])->name('admin.viewlinks-data');

    Route::get('/add-links',[AdminController::class,'addLinks']);
    Route::post('/links-add',[AdminController::class,'linksAdd']);
      Route::get('/edit-links/{id}',[AdminController::class,'editLinks']);
    Route::post('/update-links/{id}',[AdminController::class,'updateLinks']);
    Route::get('/delete-links/{id}',[AdminController::class,'deleteLinks']);
    // category route
    Route::get('/view-category',[AdminController::class,'viewCategory']);

    Route::get('/view-category/data', [AdminController::class, 'getViewCategory'])->name('admin.viewcategory-data');

    Route::get('/add-category',[AdminController::class,'addCategory']);
    Route::post('/category-add',[AdminController::class,'categoryAdd']);
    Route::get('/edit-category/{id}',[AdminController::class,'editCategory']);
    Route::post('/update-category/{id}',[AdminController::class,'updateCategory']);
    Route::get('/delete-category/{id}',[AdminController::class,'deleteCategory']);

      // home screen route
    Route::get('/view-home',[AdminController::class,'viewHome'])->name('view-home');
    Route::get('/viewhome/data', [AdminController::class, 'getViewHome'])->name('admin.viewhome-data');

    Route::get('/add-home',[AdminController::class,'addHome']);
    Route::post('/home-add',[AdminController::class,'homeAdd']);
    Route::get('/edit-home/{id}',[AdminController::class,'editHome']);
    Route::post('/update-home/{id}',[AdminController::class,'updateHome']);
    Route::get('/delete-home/{id}',[AdminController::class,'deleteHome']);

          // emoji route

    Route::get('/view-emoji',[AdminController::class,'viewEmoji']);
    Route::get('/view-emoji/data', [AdminController::class, 'getViewEmoji'])->name('admin.viewemoji-data');

    Route::get('/add-emoji',[AdminController::class,'addEmoji']);
    Route::post('/emoji-add',[AdminController::class,'emojiAdd']);
    Route::get('/delete-emoji/{id}',[AdminController::class,'deleteEmoji']);
            Route::get('/edit-emoji/{id}',[AdminController::class,'editEmoji']);
    Route::post('/update-emoji/{id}',[AdminController::class,'updateEmoji']);

      // music routes
    Route::get('/view-music',[AdminController::class,'viewMusic']);
    Route::get('/view-music/data', [AdminController::class, 'getViewMusic'])->name('admin.viewmusic-data');
    Route::get('/add-music',[AdminController::class,'addMusic']);
    Route::post('/music-add',[AdminController::class,'musicAdd']);
    Route::get('/edit-music/{id}',[AdminController::class,'editMusic']);
    Route::post('/update-music/{id}',[AdminController::class,'updateMusic']);
    Route::get('/delete-music/{id}',[AdminController::class,'deleteMusic']);


      //sleep screen routes

    Route::get('/add-sleep-screen',[AdminController::class,'addSleepScreen']);
    Route::post('/sleep-screen-add',[AdminController::class,'SleepScreenAdd']);
    Route::get('/view-sleep-screen',[AdminController::class,'viewSleepScreen']);
    Route::get('/view-sleep-screen/data', [AdminController::class, 'getViewSleepScreen'])->name('admin.viewsleepscreen-data');
    Route::get('/delete-sleep-screen/{id}',[AdminController::class,'deleteSleepScreen']);


      //Home emoji route

      Route::get('/view-home-emoji',[AdminController::class,'viewHomeEmoji']);
      Route::get('/view-home-emoji/data', [AdminController::class, 'getViewHomeEmoji'])->name('admin.viewhomeemoji-data');
      Route::get('/add-home-emoji',[AdminController::class,'addHomeEmoji']);
      Route::post('/home-emoji-add',[AdminController::class,'HomeEmojiAdd']);
      Route::get('/delete-home-emoji/{id}',[AdminController::class,'deleteHomeEmoji']);
      Route::get('/edit-home-emoji/{id}',[AdminController::class,'editHomeEmoji']);
      Route::post('/update-home-emoji/{id}',[AdminController::class,'updateHomeEmoji']);

      // single course route
    Route::get('/view-single-course',[AdminController::class,'viewSingleCourse']);

    Route::get('/view-single-course/data', [AdminController::class, 'getViewSingleCourse'])->name('admin.viewsinglecourse-data');

    Route::get('/add-single-course',[AdminController::class,'addSingleCourse']);
    Route::post('/single-course-add',[AdminController::class,'singleCourseAdd']);
    Route::get('/edit-single-course/{id}',[AdminController::class,'editSingleCourse']);
    Route::post('/update-single-course/{id}',[AdminController::class,'updateSingleCourse']);
    Route::get('/delete-single-course/{id}',[AdminController::class,'deleteSingleCourse']);

    Route::post('/save-data',[AdminController::class,'saveDataEmployee'])->name('saveDataEmployee');
    Route::post('/save-data-in-bulk',[AdminController::class,'saveDataEmployeeInBulk'])->name('saveDataEmployeeInBulk');

    Route::get('/view-quote',[AdminController::class,'viewQuote']);

    Route::get('/view-quote/data', [AdminController::class, 'getViewQuote'])->name('admin.viewquote-data');

    Route::get('/add-quote',[AdminController::class,'addQuote']);
    Route::post('/quote-add',[AdminController::class,'quoteAdd']);
    Route::get('/edit-quote/{id}',[AdminController::class,'editQuote']);
    Route::post('/update-quote/{id}',[AdminController::class,'updateQuote']);
    Route::get('/delete-quote/{id}',[AdminController::class,'deleteQuote']);

    Route::get('/view-programs',[AdminController::class,'viewPrograms']);
    Route::get('/view-session',[AdminController::class,'viewsession']);
    Route::get('/reset-session',[AdminController::class,'resetSession'])->name('admin.program-reset-max-session');
    Route::get('/programs/data', [AdminController::class, 'getPrograms'])->name('admin.programs-data');
    Route::get('/add-program/{type?}',[AdminController::class,'addProgram'])->name('admin.program.add');
    Route::post('/store-program',[AdminController::class,'storeProgram']);
    Route::get('/programs-employees/data', [AdminController::class, 'programEmployees'])->name('admin.programs-employees-data');
    Route::get('/program/{id}',[AdminController::class,'SingleProgram']);
    Route::get('/deactive-program/{id}/{convertTo}',[AdminController::class,'DeactiveProgram']);
    Route::post('/update-program/{id}',[AdminController::class,'updateProgram']);
      Route::get('/remove-program/{customerId}/{programId}', [AdminController::class,'RemoveReddemCode'])->name('remove-cusomer-program');
    Route::get('/minus-session/{customerId}/{programId}', [AdminController::class,'MinusSession'])->name('minus-session');
    Route::get('/plus-session/{customerId}/{programId}', [AdminController::class,'PlusSession'])->name('plus-session');

});

Route::group(['prefix' => 'manage-program'], function () {
    Route::get('/clear', function() {

   Artisan::call('cache:clear');
   Artisan::call('config:clear');
   Artisan::call('config:cache');
   Artisan::call('view:clear');
   Artisan::call('route:clear');
   Artisan::call('route:cache');

   return "Cleared!";

});
  Route::post('/program_login', [ProgramController::class,'checkLogin'])->name('program.login');
  Route::get('/login',[ProgramController::class,'Login'])->name('program.login');

});
//Employeer Portal Routes here
Route::group(['prefix' => 'manage-program', 'middleware' => ['program_auth']], function () {

  Route::get('/logout',[ProgramController::class,'logout'])->name('program.logout');

  Route::get('/view-dashboard',[ProgramController::class,'dashboard'])->name('program.dashboard');
  Route::get('/view-employees',[ProgramController::class,'viewEmployees']);
  Route::get('/view-analytics',[ProgramController::class,'viewAnalytics']);

  Route::get('/add-employee',[ProgramController::class,'addEmployees']);

    // Route::get('/remove-employee/{id}',[ProgramController::class,'removeEmployee']);


  Route::post('/save-data',[ProgramController::class,'save'])->name('saveData');
  Route::post('/upload-users', [ProgramController::class, 'uploadUsers'])->name('uploadUsers');
  Route::post('/processExcel', [ProgramController::class, 'processExcel'])->name('processExcel');
  Route::post('/remove-customer', [ProgramController::class, 'removeCustomer'])->name('remove-customer');

  Route::get('/remove-session/{sessionId}', [ProgramController::class, 'remove'])->name('remove-session');


  Route::get('/remove/{programId}', [ProgramController::class, 'removeprogram'])->name('remove-program');
  Route::get('/setting', [ProgramController::class, 'setting'])->name('setting');
  Route::post('/save-setting', [ProgramController::class, 'saveSetting'])->name('program.save-setting');

    Route::post('/update-program/{id}',[ProgramController::class,'updateProgram']);
    Route::post('/save-name',[ProgramController::class,'saveName']);
    Route::post('/save-program-logo/{id}',[ProgramController::class,'saveProgramLogo']);



});
