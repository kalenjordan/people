<?php

namespace App\Http\Controllers;

use Algolia\AlgoliaSearch\SearchClient;
use App\Blog;
use App\Person;
use App\PrivateTag;
use App\PublicTag;
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

        $params = array(
            "filterByFormula" => $filter,
            "sort"            => [['field' => 'Modified', 'direction' => "desc"]],
        );

        $savedSearches = (new SavedSearch())->getRecords($params);

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

    public function publicTag(Request $request, $slugOrId)
    {
        /** @var User $user */
        $user = $request->session()->get('user');

        /** @var PublicTag $tag */
        $tag = (new PublicTag())->lookupWithFilter("Slug = '$slugOrId'");
        if (!$tag) {
            abort(404);
        }

        $people = $tag->people();

        return view('tag', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user,
            'tag'     => $tag,
            'people'  => $people,
        ]);
    }

    public function privateTag(Request $request, $id)
    {
        /** @var User $user */
        $user = $request->session()->get('user');

        if (! $user) {
            abort(403);
        }

        /** @var PrivateTag $tag */
        $tag = (new PrivateTag())->load($id);
        if (!$tag) {
            abort(404);
        }

        $people = $tag->peopleForUser($user);

        return view('tag', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user,
            'tag'     => $tag,
            'people'  => $people,
        ]);
    }

}
