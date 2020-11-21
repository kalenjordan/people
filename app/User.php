<?php

namespace App;

use Illuminate\Http\Request;
use Cookie;

class User extends Airtable
{

    protected $table = "Users";

    public function email()
    {
        return isset($this->fields->{'Email'}) ? $this->fields->{'Email'} : null;
    }

    public function name()
    {
        return isset($this->fields->{'Name'}) ? $this->fields->{'Name'} : null;
    }

    public function about()
    {
        return isset($this->fields->{'About'}) ? $this->fields->{'About'} : null;
    }

    public function phone()
    {
        return isset($this->fields->{'Phone'}) ? $this->fields->{'Phone'} : null;
    }

    public function avatar()
    {
        return isset($this->fields->{'Avatar'}[0]->url) ? $this->fields->{'Avatar'}[0]->url : null;
    }

    public function location()
    {
        return isset($this->fields->{'Location'}) ? $this->fields->{'Location'} : null;
    }

    public function apiKey()
    {
        return isset($this->fields->{'API Key'}) ? $this->fields->{'API Key'} : null;
    }

    public function isAdmin()
    {
        return Util::isAdmin($this);
    }

    public function url()
    {
        return '/users/' . $this->id();
    }

    public function searchTitle()
    {
        return $this->name();
    }

    public function searchIndexId()
    {
        return $this->url();
    }

    public function toSearchIndexArray()
    {
        return [
            'type'         => 'user',
            'url'          => $this->url(),
            'object_id'    => $this->searchIndexId(),
            'search_title' => $this->searchTitle(),
            'name'         => $this->name(),
            'about'        => $this->about(),
            'location'     => $this->location(),
            'public'       => false,
        ];
    }

    public function status()
    {
        return isset($this->fields->{'Status'}) ? $this->fields->{'Status'} : null;
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function favoriteThingsIds()
    {
        return isset($this->fields->{'Favorite Things'}) ? $this->fields->{'Favorite Things'} : [];
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function favoriteThingsNames()
    {
        return isset($this->fields->{'Favorite Things Names'}) ? $this->fields->{'Favorite Things Names'} : [];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function favoriteThingsVue()
    {
        $ids = $this->favoriteThingsIds();
        $names = $this->favoriteThingsNames();

        $results = [];
        for ($i = 0; $i < count($ids); $i++) {
            $results[] = [
                'id'     => $ids[$i],
                'name'   => $names[$i],
            ];
        }

        return $results;
    }

    public function lastActive()
    {
        return isset($this->fields->{'Last Active'}) ? $this->fields->{'Last Active'} : null;
    }

    public function lastActiveDate()
    {
        if (!$this->lastActive()) {
            return null;
        }

        return new Date($this->lastActive());
    }

    public function lastActiveStale()
    {
        if (!$this->lastActive()) {
            return true;
        }

        if ($this->lastActiveDate()->isDaysAgo(1)) {
            return true;
        }

        return false;
    }

    public static function loggedInUser(Request $request)
    {
        $user = self::_loggedInUser();
        if (!$user) {
            return null;
        }

        if ($user->lastActiveStale()) {
            $user = (new User())->load($user->id());
            $now = Date::now()->toDateTimeString();
            $user->save([
                'Last Active' => $now,
            ]);
            $user = $user->refreshSession($request);
        }

        return $user;
    }

    public static function _loggedInUser()
    {
        $cookieUserId = Cookie::get('user_id');

        $sessionUser = session()->get('user');
        if ($sessionUser) {
            return $sessionUser;
        }

        if ($cookieUserId) {
            $user = (new User())->load($cookieUserId);
            if ($user) {
                session()->put('user', $user);
                return $user;
            }
        }

        return null;
    }

    public function refreshSession(Request $request)
    {
        $user = (new User())->load($this->id());
        $request->session()->put('user', $user);

        return $user;
    }
}
