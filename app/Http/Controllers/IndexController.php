<?php

namespace App\Http\Controllers;

use Algolia\AlgoliaSearch\SearchClient;
use App\Blog;
use App\Person;
use App\SavedSearch;
use App\User;
use App\Util;
use Illuminate\Http\Request;
use Exception;

class IndexController extends Controller
{

    /**
     * @param Request $request
     *
     * @throws \Exception
     */
    public function welcome(Request $request)
    {
        try {
            $user = User::loggedInUser($request);
        } catch (Exception $e) {
            return redirect('/logout');
        }

        $savedSearches = (new SavedSearch())->getRecords();

        return view('welcome', [
            'error'         => $request->input('error'),
            'success'       => $request->input('success'),
            'savedSearches' => $savedSearches,
            'user'          => $user,
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     */
    public function person(Request $request, $slugOrId)
    {
        try {
            $user = User::loggedInUser($request);
        } catch (Exception $e) {
            return redirect('/logout');
        }

        $person = (new Person())->lookupWithFilter("Slug = '$slugOrId'");
        if (!$person) {
            abort(404);
        }

        return view('person', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'person'  => $person,
            'user'    => $user,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function savedSearch(Request $request, $slugOrId)
    {
        $user = $request->session()->get('user');

        $savedSearch = (new SavedSearch())->lookupWithFilter("Slug = '$slugOrId'");
        if (!$savedSearch) {
            abort(404);
        }

        $people = $savedSearch->people();
        foreach ($people as $person) {
            /* @var \App\Person $person */
            $peopleIds[] = $person->id();
        }

        $savedSearch->save([
            'People' => $peopleIds
        ]);

        return view('saved-search', [
            'error'       => $request->input('error'),
            'success'     => $request->input('success'),
            'user'        => $user,
            'savedSearch' => $savedSearch,
            'people'      => $people,
        ]);
    }
}
