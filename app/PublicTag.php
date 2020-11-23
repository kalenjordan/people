<?php

namespace App;

class PublicTag extends Airtable
{
    protected $table = "Public Tags";

    public function name()
    {
        return isset($this->fields->{'Name'}) ? $this->fields->{'Name'} : null;
    }

    public function slug()
    {
        return isset($this->fields->{'Slug'}) ? $this->fields->{'Slug'} : null;
    }

    public function url()
    {
        return '/' . $this->slug();
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

    public function toSearchIndexArray()
    {
        return [
            'type'         => 'tag',
            'object_id'    => $this->searchIndexId(),
            'search_title' => $this->searchTitle(),
            'url'          => $this->url(),
        ];
    }

    public function toData()
    {
        return [
            'id'    => $this->id(),
            'url'   => $this->url(),
            'name'  => $this->name(),
        ];
    }
}
