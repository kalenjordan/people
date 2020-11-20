<?php

namespace App;

class Person extends Airtable
{
    protected $table = "People";

    public function name()
    {
        return isset($this->fields->{'Name'}) ? $this->fields->{'Name'} : null;
    }

    public function slug()
    {
        return isset($this->fields->{'Slug'}) ? $this->fields->{'Slug'} : null;
    }

    public function description()
    {
        return isset($this->fields->{'Description'}) ? $this->fields->{'Description'} : null;
    }

    public function price()
    {
        return isset($this->fields->{'Price'}) ? $this->fields->{'Price'} : 0;
    }

    public function priceFormatted()
    {
        return number_format($this->price(), 2);
    }

    public function isEnabled()
    {
        return isset($this->fields->{'Enabled'}) ? true : false;
    }

    public function url()
    {
        return '/' . $this->slug();
    }

    public function avatar()
    {
        return isset($this->fields->{'Avatar'}[0]->url) ? $this->fields->{'Avatar'}[0]->url : null;
    }

    public function toSearchIndexArray()
    {
        return [
            'type'         => 'person',
            'object_id'    => $this->searchIndexId(),
            'search_title' => $this->searchTitle(),
            'url'          => $this->url(),
            'avatar'       => $this->avatar(),
        ];
    }

}
