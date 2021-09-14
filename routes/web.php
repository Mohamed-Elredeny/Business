<?php

use App\Models\Country;
use App\Models\Inspiration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::get('/', function () {
        return redirect()->route('login');
    })->name('welcome');

    Auth::routes(['verify' => true]);


    Route::group(['middleware' => ['auth','mention']],function() {

        Route::get('home', 'User\MainController@index')->name('home');
        Route::post('joingroup', 'User\GroupsController@enterGroup')->name('join_group');
        Route::post('likepage', 'User\PagesController@likePage')->name('like_page');
        Route::post('addfriend', 'User\FriendshipController@friendship')->name('addfriend');
        Route::post('savepost', 'User\PostController@savePost')->name('savepost');
        Route::get('loadmore/{take?}/{start?}','User\MainController@index');
        Route::get('loadstories/{take?}/{start?}','User\MainController@loadStories')->name('load_stories');
        Route::get('loadcomments/{post_id}/{limit?}/{start?}','User\MainController@loadComments');
        Route::resource('posts', 'User\PostController');
        Route::post('sponsor', 'User\PostController@sponsor')->name('sponsor');
        Route::post('sponsor/payment', 'User\PostController@payment')->name('sponsor.payment');
        Route::post('userreport', 'User\MainController@report')->name('userreports.store');
        Route::resource('comments', 'User\CommentController');
        Route::get('comments', 'User\CommentController@store');
        Route::resource('likes', 'User\LikeController');
        Route::resource('shares', 'User\ShareController');
        Route::resource('stories', 'User\StoryController');
        Route::post('viewstory', 'User\StoryController@viewStory')->name('story.view');
        Route::resource('groups', 'User\GroupsController');
        Route::resource('pages', 'User\PagesController');
        Route::resource('companies', 'User\CompaniesController');
        Route::get('saved_posts', 'User\PostController@savedPosts')->name('savedposts');
        Route::get('/notifications','User\NotificationsController@index')->name('notifications');
        Route::get('services/categories', 'User\ServiceController@getCategories')->name('service_categories');
        Route::get('services/{category_id?}', 'User\ServiceController@index')->name('services');
        Route::resource('services','User\ServiceController');
        Route::get('/mark-all-read/{user}', function (User $user) {
            $user->unreadNotifications->markAsRead();
            return response(['message'=>'done', 'notifications'=>$user->notifications]);
        })->name('read');
        Route::get('profile/{user_id}', 'User\ProfileController@edit')->name('profile');
        Route::put('profile', 'User\ProfileController@update')->name('profileupdate');
        Route::put('profile/password', 'User\ProfileController@password')->name('profilepassword');


        //Martina
        #region groups
        Route::resource('groups', 'User\GroupsController');
        Route::any('join-group','User\GroupsController@joinGroup')->name('join-group');
        Route::get('about-group/{id}','User\GroupsController@aboutGroup')->name('about-group');
        Route::get('images-group/{id}','User\GroupsController@imagesGroup')->name('images-group');
        Route::get('videos-group/{id}','User\GroupsController@videosGroup')->name('videos-group');
        Route::get('requests-group/{id}','User\GroupsController@requestsGroup')->name('requests-group');
        Route::any('changeRequest-group','User\GroupsController@changeRequest')->name('changeRequest-group');
        Route::any('adminLeft-group/{id}','User\GroupsController@adminLeft')->name('adminLeft');
        Route::get('members-group/{id}','User\GroupsController@membersGroup')->name('members-group');
        Route::any('frientshep-group','User\GroupsController@frirndshipGroup')->name('frientshep-group');
        Route::any('following-group','User\GroupsController@followingGroup')->name('following-group');
        Route::any('asignAdmin-group','User\GroupsController@asignAdmin')->name('asignAdmin-group');
        Route::any('all-group','User\GroupsController@allGroup')->name('all-group');
        Route::any('my-group','User\GroupsController@myGroup')->name('my-group')->middleware(['auth','verified']);
        #endregion
        #region pages
        Route::resource('pages', 'User\PagesController');
        Route::any('join-page','User\PagesController@joinPage')->name('join-page');
        Route::get('about-page/{id}','User\PagesController@aboutPage')->name('about-page');
        Route::get('images-page/{id}','User\PagesController@imagesPage')->name('images-page');
        Route::get('videos-page/{id}','User\PagesController@videosPage')->name('videos-page');
        Route::get('requests-page/{id}','User\PagesController@requestsPage')->name('requests-page');
        Route::any('changeRequest-page','User\PagesController@changePage')->name('changeRequest-page');
        Route::any('adminLeft-page/{id}','User\PagesController@adminLeft')->name('adminLeft');
        Route::get('members-page/{id}','User\PagesController@membersPage')->name('members-page');
        Route::any('frientshep-page','User\PagesController@frirndshipPage')->name('frientshep-page');
        Route::any('following-page','User\PagesController@followingPage')->name('following-page');
        Route::any('asignAdmin-page','User\PagesController@asignAdmin')->name('asignAdmin-page');
        Route::any('all-page','User\PagesController@allPage')->name('all-page');
        Route::any('my-page','User\PagesController@myPage')->name('my-page')->middleware(['auth','verified']);
        #endregion
        //Redeny
         Route::get('user/profile/{user_id}', 'User\ProfileController@index')->name('user.view.profile');
         Route::post('user/add/friend','User\ProfileController@addFriend')->name('user.add.friend');
         Route::post('user/profile/add/friend', 'User\ProfileController@addFriendRedeny')->name('redeny.user.add.friend');
         Route::post('user/profile/follow/friend', 'User\ProfileController@followFriendRedeny')->name('redeny.user.follow.friend');
        Route::post('user/profile/unfollow/friend', 'User\ProfileController@unfollowFriendRedeny')->name('redeny.user.unfollow.friend');


        Route::post('user/profile/refuse/friend', 'User\ProfileController@RefuseFriendRedeny')->name('redeny.user.refuse.friend');
        Route::post('user/profile/accept/friend', 'User\ProfileController@AcceptFriendRedeny')->name('redeny.user.accept.friend');
        Route::group(['prefix'=>'profile','namespace'=>'User'],function () {
            Route::post('user/edit/profile', 'ProfileController@editProfile')->name('redeny.user.edit.profile');
            Route::post('user/add/friend', 'ProfileController@addFriendProfile')->name('redeny.user.add.friend.profile');
            Route::post('user/refuse/friend', 'ProfileController@RefuseFriendProfile')->name('redeny.user.refuse.friend.profile');
            Route::post('user/accept/friend', 'ProfileController@AcceptFriendProfile')->name('redeny.user.accept.friend.profile');
            Route::post('user/follow/friend', 'ProfileController@followFriendProfile')->name('redeny.user.follow.friend.profile');
            Route::post('user/unfollow/friend', 'ProfileController@unfollowFriendProfile')->name('redeny.user.unfollow.friend.profile');
            Route::post('user/add/inspiration', 'ProfileController@addInspirationProfile')->name('redeny.user.add.inspiration.profile');
            Route::post('user/remove/inspiration', 'ProfileController@removeInspirationProfile')->name('redeny.user.remove.inspiration.profile');
            Route::post('user/add/music', 'ProfileController@addMusic')->name('redeny.user.add.music');
            Route::post('user/list/music', 'ProfileController@MusicList')->name('redeny.user.list.music');
            Route::post('user/list/sport', 'ProfileController@SportList')->name('redeny.user.list.sport');
            Route::post('user/list/hoppy', 'ProfileController@HobbyList')->name('redeny.user.list.hoppy');
            Route::post('user/list/inspiration', 'ProfileController@inspirationList')->name('redeny.user.list.inspiration');

            Route::get('user/profile/view/{user_id}/{component}', 'ProfileController@viewComponent')->name('redeny.view.component');


        });
        Route::Group(['namespace'=>'User'],function(){
            Route::get('chat','ChatController@index');
            Route::get('chats','ChatController@chats');//View all Chats
          Route::get('chats/messages','ChatController@messages');//View all Chat messages
          Route::post('chats/messages/add','ChatController@addMessage');//Add New Message
        });

    });
    Route::get('/directions', function () {
        $url = 'https://countriesnow.space/api/v0.1/countries';
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        $records = $newsData->data;
        //0 20
        for($i=0;$i<20;$i++){
            $country = $records[$i]->country;
            $cities = $records[$i]->cities;
            $oneCountry= Country::create([
                'name' => $country,
            ]);
            foreach ($cities as $city){
                DB::table('cities')->insert([
                    'name' => $city,
                    'country_id'=>$oneCountry->id
                ]);
            }
        }
        return 1;
    });
    Route::get('hh',function (){
        Inspiration::create([
            'user_id' => 1,
            'inspirerende_id' => 3
        ]);
    });

});
