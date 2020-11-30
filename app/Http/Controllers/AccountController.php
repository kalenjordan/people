<?php

namespace App\Http\Controllers;

use Algolia\AlgoliaSearch\SearchClient;
use App\Person;
use App\Twitter;
use App\User;
use App\Util;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    /**
     * @param Request $request
     * @param         $friendId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function settings(Request $request)
    {
        /* @var User $user */
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/auth?redirect=" . $request->path());
        }

//        $key = SearchClient::generateSecuredApiKey(Util::algoliaPublicKeyForAdmin(), [
//            'filters' => "user:all"
//        ]);
//        die($key);

        $key = SearchClient::generateSecuredApiKey(Util::algoliaPublicKeyForAdmin(), [
            'filters' => "user:all OR user:{$user->id()}"
        ]);

        $user->save([
            'Algolia API Key' => $key
        ]);

        $user = (new User())->load($user->id());

        return view('account.settings', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user
        ]);
    }

    /**
     * @param Request $request
     * @param         $friendId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function settingsPost(Request $request)
    {
        /** @var User $user */
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/?error=not_logged_in");
        }

        $data = [
            'Name'  => $request->input('name'),
            'About' => $request->input('about'),
        ];

        if ($request->input('favorite_persons')) {
            $personsTheyUseIds = explode(',', $request->input('favorite_persons'));
            $personsTheyUseIds = $this->_saveFavoritePersons($personsTheyUseIds);
            $data['Favorite Persons'] = $personsTheyUseIds;
        } else {
            $data['Favorite Persons'] = [];
        }

        $user->save($data);

        return redirect('/account/settings?success=Saved');
    }

    /**
     * @param Request $request
     * @param         $friendId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function personList(Request $request)
    {
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/auth?redirect=" . $request->path());
        }

        $params = array(
            "filterByFormula" => "{User ID} = '{$user->id()}'",
            "sort"            => [['field' => 'Created', 'direction' => "desc"]],
        );

        $persons = (new Person())->getRecords($params);

        return view('account.person-list', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user,
            'persons' => $persons,
        ]);
    }

    /**
     * @param Request $request
     * @param         $friendId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function personNew(Request $request)
    {
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/auth?redirect=" . $request->path());
        }

        return view('account.person-new', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user,
        ]);
    }

    /**
     * @param Request $request
     * @param         $friendId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function personNewPost(Request $request)
    {
        /** @var User $user */
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/?error=not_logged_in");
        }

        $name = $request->input('name');
        $profile = Twitter::getProfile($name);

        $data = [
            'Name'        => $profile['name'],
            'Twitter'     => $name,
            'Avatar'      => [
                [
                    'url' => $profile['avatar'],
                ]
            ],
            'Description' => $profile['description'],
            'Slug'        => $name,
        ];

        $person = (new Person())->create($data);
        $person->saveToSearchIndex();

        return redirect($person->url());
    }

    public function personNewFromTwitter(Request $request, $twitterUsername)
    {
        /** @var User $user */
        $user = $request->session()->get('user');
        if (!$user) {
            $url = '/account/people/new-from-twitter/' . $twitterUsername;
            return redirect("/auth?redirect=" . urlencode($url));
        }

        $existing = (new Person())->lookupWithFilter("Twitter = '$twitterUsername'");
        if ($existing) {
            return redirect($existing->url() . '?existing');
        }

        $profile = Twitter::getProfile($twitterUsername);

        $data = [
            'Name'        => $profile['name'],
            'Twitter'     => $twitterUsername,
            'Avatar'      => [
                [
                    'url' => $profile['avatar'],
                ]
            ],
            'Description' => $profile['description'],
            'Slug'        => $twitterUsername,
        ];

        $person = (new Person())->create($data);
        $person->saveToSearchIndex();
        return redirect($person->url());
    }

    public function personNewFromLinkedIn(Request $request)
    {
        /** @var User $user */
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/auth?redirect=" . urlencode($request->url()));
        }

        $name = $request->input('name');
        $linkedInUrl = $request->input('url');
        $avatar = $request->input('avatar');

        $slug = Util::slugify($name);

        $data = [
            'Name'        => $name,
            'LinkedIn'     => $linkedInUrl,
            'Avatar'      => [
                [
                    'url' => $avatar,
                ]
            ],
            'Slug'        => $slug,
        ];

        $person = (new Person())->create($data);
        $person->saveToSearchIndex();
        return redirect($person->url());
    }

    /**
     * @param Request $request
     * @param         $friendId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function personEdit(Request $request, $personId)
    {
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/auth?redirect=" . $request->path());
        }

        $person = (new Person())->load($personId);
        if (!$person) {
            throw new \Exception("Couldn't load person: $personId");
        }

        return view('account.person-edit', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user,
            'person'  => $person,
        ]);
    }

    /**
     * @param Request $request
     * @param         $friendId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function personEditPost(Request $request, $personId)
    {
        /** @var User $user */
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/?error=not_logged_in");
        }

        $person = (new Person())->load($personId);
        if (!$person) {
            throw new \Exception("Couldn't load person: $personId");
        }

        $data = [
            'Name'        => $request->input('name'),
            'Description' => $request->input('description'),
            'Price'       => (float)$request->input('price'),
        ];

        $person->save($data);

        return redirect($person->editUrl() . '?success=Saved');
    }

    /**
     * @param User $user
     * @param      $personsTheyUseIds
     *
     * @return mixed
     * @throws \Exception
     */
    protected function _saveFavoritePersons($personIds)
    {
        for ($i = 0; $i < count($personIds); $i++) {
            $personId = $personIds[$i];
            if (substr($personId, 0, 4) == 'new_') {
                $newPersonName = substr($personId, 4);
                $personTheyUse = (new Person())->create(['Name' => $newPersonName]);
                $personIds[$i] = $personTheyUse->id();
            }
        }

        return $personIds;
    }

    /**
     * @param Request $request
     * @param         $friendId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function personDelete(Request $request, $personId)
    {
        /** @var User $user */
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect("/?error=not_logged_in");
        }

        $person = (new Person())->load($personId);
        if (!$person) {
            throw new \Exception("Couldn't load person: $personId");
        }

        $deletedName = $person->name();
        $person->delete();

        return redirect("/account/persons/?success=Deleted $deletedName");
    }
}
