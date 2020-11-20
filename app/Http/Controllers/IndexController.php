<?php

namespace App\Http\Controllers;

use Algolia\AlgoliaSearch\SearchClient;
use App\Blog;
use App\Person;
use App\User;
use App\Util;
use Illuminate\Http\Request;

class IndexController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function welcome(Request $request)
    {
        $user = $request->session()->get('user');

        return view('welcome', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function person(Request $request, $slugOrId)
    {
        $user = $request->session()->get('user');

        $person = (new Person())->lookupWithFilter("Slug = '$slugOrId'");
        if (!$person) {
            abort(404);
        }

        return view('person', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user,
            'person'  => $person,
        ]);
    }
}
