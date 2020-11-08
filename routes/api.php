<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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

Route::any('testAuth', 'Api\ForgotPassword_Controller@testAuth');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Cron 
Route::get('/cron/deleteMessages', 'Cron\Message_Controller@deleteMessages');

Route::post('validateEmail', 'Api\Register_Controller@validateEmail');
Route::post('resendOTP', 'Api\Register_Controller@resendOTP');
Route::post('verifyOTP', 'Api\Register_Controller@verifyOTP');
Route::post('registration', 'Api\Register_Controller@registration');

Route::post('login', 'Api\Login_Controller@login');

//Forgot Password
Route::post('forgotPassword', 'Api\ForgotPassword_Controller@forgotPassword');
Route::post('resetPassword', 'Api\ForgotPassword_Controller@resetPassword');

//Category Api
Route::get('getCategories', 'Api\Category_Controller@getCategories');

Route::group([

    'middleware' => 'auth:api',

], function () {
    //login
    Route::post('logout', 'Api\Login_Controller@logout');
    Route::post('testAuth', 'Api\Login_Controller@testAuth');

    //Profile
    Route::get('getProfile', 'Api\User_Profile@getProfile');
    Route::post('updateProfile', 'Api\User_Profile@updateProfile');
    Route::post('uploadProfileImage', 'Api\User_Profile@uploadProfileImage');
    Route::get('downloadProfileImage', 'Api\User_Profile@downloadProfileImage');
    Route::post('updateSetting', 'Api\User_Profile@updateSetting');

    //I need/can help with
    Route::post('canHelpWith', 'Api\Help_Controller@canHelpWith');
    Route::post('needHelpWith', 'Api\Help_Controller@needHelpWith');

    //Friend suggestion 
    Route::post('friendSuggestion', 'Api\FriendSuggestion_Controller@friendSuggestion');
    Route::post('viewProfile', 'Api\FriendSuggestion_Controller@viewProfile');
    Route::post('sendFriendRequest', 'Api\FriendSuggestion_Controller@sendFriendRequest');
    Route::get('getFriendList', 'Api\FriendSuggestion_Controller@getFriendList');
    Route::get('getPendingFriendList', 'Api\FriendSuggestion_Controller@getPendingFriendList');
    Route::post('acceptFriendRequest', 'Api\FriendSuggestion_Controller@acceptFriendRequest');
    Route::post('rejectFriendRequest', 'Api\FriendSuggestion_Controller@rejectFriendRequest');
    Route::post('searchFriends', 'Api\FriendSuggestion_Controller@searchFriends');
    Route::get('receviedFriendRequestList', 'Api\FriendSuggestion_Controller@receviedFriendRequestList');
    Route::post('cancelFriendRequest', 'Api\FriendSuggestion_Controller@cancelFriendRequest');
    Route::post('unfriendFriendRequest', 'Api\FriendSuggestion_Controller@unfriendFriendRequest');


    //Feed api
    Route::post('addFeed', 'Api\Feed_Controller@addFeed');
    Route::post('removeFeed', 'Api\Feed_Controller@removeFeed');
    Route::post('likeFeed', 'Api\Feed_Controller@likeFeed');
    Route::post('unlikeFeed', 'Api\Feed_Controller@unlikeFeed');
    Route::post('addFeedComment', 'Api\Feed_Controller@addFeedComment');
    Route::post('deleteFeedComment', 'Api\Feed_Controller@deleteFeedComment');
    Route::post('getFriendFeeds', 'Api\Feed_Controller@getFriendFeeds');
    Route::post('getFeedComments', 'Api\Feed_Controller@getFeedComments');
    Route::post('getHomeFeeds', 'Api\Feed_Controller@getHomeFeeds');
    Route::post('feedShare', 'Api\Feed_Controller@feedShare');
    Route::post('valueAdd', 'Api\Feed_Controller@valueAdd');
    Route::post('valueRemove', 'Api\Feed_Controller@valueRemove');

    //Message api
    Route::post('sendMessage', 'Api\Message_Controller@sendMessage');
    Route::post('getUserMessages', 'Api\Message_Controller@getUserMessages');
    Route::post('getAllMessages', 'Api\Message_Controller@getAllMessages');
    
    //Test profile
    Route::any('testProfile', 'Api\Login_Controller@testProfile');

    // Fcm token
    Route::post('fcmToken', 'Api\Fcm_Controller@fcmToken');

    //photo competition
    Route::post('submitPhoto', 'Api\PhotoCompetition_Controller@submitPhoto');
    Route::post('getPhotos', 'Api\PhotoCompetition_Controller@getPhotos');
    Route::post('addVote', 'Api\PhotoCompetition_Controller@addVote');
    Route::post('getCompetitionResult', 'Api\PhotoCompetition_Controller@getCompetitionResult');
    Route::post('getUserPreviousEntries', 'Api\PhotoCompetition_Controller@getUserPreviousEntries');    

    //reporting issue 
    Route::post('reportingIssue', 'Api\Reporting_Controller@reportingIssue');

    // add city and state
    Route::post('addState', 'Api\Address_Controller@addState');
    Route::post('addCountry', 'Api\Address_Controller@addCountry');

});

Route::any('/downloadProfileImage/{filepath}', function ($filepath) {
        //$fullpath = asset('storage/app/public/profile_images/'.$filepath);
        // $path = asset('storage/app/public/profile_images/' . $filepath);
        // dd($path);
    //$fullpath = asset('/public/storage/profile_images/'.$filepath);
    return response()->download('/public/storage/profile_images/'.$filepath);
});