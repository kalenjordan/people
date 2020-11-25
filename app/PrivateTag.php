<?php

namespace App;

class PrivateTag extends Airtable
{
    protected $table = "Private Tags";

    public function name()
    {
        return isset($this->fields->{'Name'}) ? $this->fields->{'Name'} : null;
    }

    public function fullName()
    {
        return isset($this->fields->{'Full Name'}) ? $this->fields->{'Full Name'} : null;
    }

    public function userId()
    {
        return isset($this->fields->{'User'}[0]) ? $this->fields->{'User'}[0] : null;
    }

    public function created()
    {
        return isset($this->fields->{'Created'}) ? $this->fields->{'Created'} : null;
    }

    public function createdDate()
    {
        $date = $this->created();
        if (!$date) {
            return null;
        }

        return new Date($date);
    }

    public function peopleIds()
    {
        return isset($this->fields->{'People'}) ? $this->fields->{'People'} : [];
    }

    public function url()
    {
        return '/private-tag/' . $this->id();
    }

    public function peopleForUser($user)
    {
        $tagName = $this->name();
        $userName = $user->name();

        $params = array(
            "filterByFormula" => "FIND('$tagName tagged by $userName', {Private Tags})",
            "sort"            => [['field' => 'Created', 'direction' => "desc"]],
        );

        $people = (new Person())->getRecords($params);
        return $people;
    }

    public function searchTitle()
    {
        return "Private Tag: " . $this->name();
    }

    public function toSearchIndexArray()
    {
        return [
            'type'         => 'private-tag',
            'object_id'    => $this->searchIndexId(),
            'search_title' => $this->searchTitle(),
            'url'          => $this->url(),
            'name'         => $this->name(),
            'user'         => $this->userId(),
        ];
    }

    public function toData()
    {
        return [
            'id'   => $this->id(),
            'name' => $this->name(),
        ];
    }
}
