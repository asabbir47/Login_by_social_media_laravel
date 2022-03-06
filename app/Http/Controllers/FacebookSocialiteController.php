<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Auth;
use Exception;
use App\Models\User;

class FacebookSocialiteController extends Controller
{
    //
    public function redirectToFB()
    {
        return Socialite::driver('facebook')->redirect();
    }
       
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleCallback()
    {
        try {
     
            $user = Socialite::driver('facebook')->user();
            // dd($user);
            $finduser = User::where('social_type','facebook')->where('social_id', $user->id)->first();
      
            if($finduser){
      
                Auth::login($finduser);
     
                return redirect('/home');
      
            }else{

                $user_exist = User::where('email',$user->email);
                if($user_exist->count()>0)
                {
                    return redirect('/login')->withErrors(['msg' => 'This email is already used']);
                }

                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'social_id'=> $user->id,
                    'social_type'=> 'facebook',
                    'password' => encrypt('my-facebook')
                ]);
     
                Auth::login($newUser);
      
                return redirect('/home');
            }
     
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
