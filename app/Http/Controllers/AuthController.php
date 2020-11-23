<?php

namespace App\Http\Controllers;

use App\User;
use App\Util;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Cookie;
use Exception;

class AuthController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function auth(Request $request)
    {
        if ($request->input('redirect')) {
            if (strpos($request->input('redirect'), ".")) {
                throw new Exception("Bad redirect format");
            }
            $request->session()->put('redirect', $request->input('redirect'));
        }

        return Socialite::driver('google')->redirect();
    }

    public function logout(Request $request)
    {
        $request->session()->remove('user');
        if ($request->input('r')) {
            redirect($request->input('r'))->withCookie(cookie()->forget('user_id'));
        }
        return redirect()->back()->withCookie(cookie()->forget('user_id'));
    }

    /**
     * Obtain the user information from Google.
     *
     * @throws Exception
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $socialiteUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect('/login');
        }

        $email = isset($socialiteUser->email) ? $socialiteUser->email : null;
        $name = isset($socialiteUser->name) ? $socialiteUser->name : null;
        $user = (new User())->lookupWithFilter("Email = '$email'");

        if (!$user) {
            $user = (new User)->create([
                'Name'   => $name,
                'Email'  => $email,
                'Avatar' => [
                    [
                        'url' => isset($socialiteUser->avatar) ? $socialiteUser->avatar : null,
                    ]
                ],
                'API Key' => md5(now() . Util::appName() . $email),
            ]);
        }

        $request->session()->put('user', $user);
        $cookie = Cookie::forever('user_id', $user->id());
        Cookie::queue($cookie);

        if ($request->session()->get('redirect')) {
            return redirect($request->session()->get('redirect'));
        }

        return redirect('/');
    }
}
