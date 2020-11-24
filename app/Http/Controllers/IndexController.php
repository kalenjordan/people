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

        if ($user) {
            $filter = "OR(
                Private = 0,
                AND(Private = 1, {User Record ID} = '{$user->id()}')
            )";
        } else {
            $filter = "Private = 0";
        }

        $savedSearches = (new SavedSearch())->recordsWithFilter($filter);

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

        /** @var Person $person */
        $person = (new Person())->lookupWithFilter("Slug = '$slugOrId'");
        if (!$person) {
            abort(404);
        }

        $personData = $user ? $person->toDataFor($user) : $person->toData();

        return view('person', [
            'error'      => $request->input('error'),
            'success'    => $request->input('success'),
            'person'     => $person,
            'user'       => $user,
            'personData' => $personData,
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
        /** @var User $user */
        $user = $request->session()->get('user');

        /** @var SavedSearch $savedSearch */
        $savedSearch = (new SavedSearch())->lookupWithFilter("Slug = '$slugOrId'");
        if (!$savedSearch) {
            abort(404);
        }

        if (!$savedSearch->userHasAccess($user)) {
            abort(403);
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
