<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Auth;
use Exception;
use App\Models\User;

class GoogleSocialiteController extends Controller
{
    //
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleCallback()
    {
        try {
     
            $user = Socialite::driver('google')->user();
            // dd($user);
            $finduser = User::where('social_id', $user->id)->first();
            $finduser = User::where('social_type','google')->where('social_id', $user->id)->first();
      
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
                    'social_type'=> 'google',
                    'password' => encrypt('my-google')
                ]);
     
                Auth::login($newUser);
      
                return redirect('/home');
            }
     
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
