<?php

namespace App;

class SavedSearch extends Airtable
{
    protected $table = "Saved Searches";

    public function name()
    {
        return isset($this->fields->{'Name'}) ? $this->fields->{'Name'} : null;
    }

    public function isPrivate()
    {
        return isset($this->fields->{'Private'}) ? $this->fields->{'Private'} : null;
    }

    public function slug()
    {
        return isset($this->fields->{'Slug'}) ? $this->fields->{'Slug'} : null;
    }

    public function avatars()
    {
        $avatars = isset($this->fields->{'People Avatars'}) ? $this->fields->{'People Avatars'} : [];
        $urls = [];
        foreach ($avatars as $avatar) {
            $urls[] = $avatar->url;
        }

        return $urls;
    }

    public function description()
    {
        return isset($this->fields->{'Description'}) ? $this->fields->{'Description'} : null;
    }

    public function filter()
    {
        return isset($this->fields->{'Filter'}) ? $this->fields->{'Filter'} : null;
    }

    public function userId()
    {
        return isset($this->fields->{'User'}[0]) ? $this->fields->{'User'}[0] : null;
    }

    public function people()
    {
        $params = array(
            "filterByFormula" => $this->filter(),
            "sort"            => [['field' => 'Created', 'direction' => "desc"]],
        );

        $people = (new Person())->getRecords($params);
        return $people;
    }

    public function isEnabled()
    {
        return isset($this->fields->{'Enabled'}) ? true : false;
    }

    public function url()
    {
        return '/s/' . $this->slug();
    }

    public function avatar()
    {
        return isset($this->fields->{'Avatar'}[0]->url) ? $this->fields->{'Avatar'}[0]->url : null;
    }

    public function userFacetForSearch()
    {
        if ($this->isPrivate()) {
            return $this->userId();
        }

        return "all";
    }

    public function userHasAccess($user)
    {
        if (! $this->isPrivate()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return $this->userId() == $user->id();
    }

    public function searchTitle()
    {
        return "Saved Search: " . $this->name();
    }

    public function toSearchIndexArray()
    {
        return [
            'type'         => 'saved-search',
            'object_id'    => $this->searchIndexId(),
            'search_title' => $this->searchTitle(),
            'url'          => $this->url(),
            'avatar'       => $this->avatar(),
            'user'         => $this->userFacetForSearch(),
        ];
    }
}
