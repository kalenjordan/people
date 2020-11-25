<?php

namespace App;

use Exception;

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

    public function count()
    {
        return isset($this->fields->{'Count'}) ? $this->fields->{'Count'} : null;
    }

    public function url()
    {
        return '/t/' . $this->slug();
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

    public function people()
    {
        $tagName = $this->name();
        $params = array(
            "filterByFormula" => "FIND('$tagName', {Public Tags})",
            "sort"            => [['field' => 'Created', 'direction' => "desc"]],
        );

        $people = (new Person())->getRecords($params);
        return $people;
    }

    public function generateAndSaveSlug()
    {
        $slug = Util::slugify($this->name());
        $existing = (new PublicTag())->lookupWithFilter("Slug = '$slug'");
        if ($existing) {
            throw new Exception("Already exists: $slug");
        }
        $this->save([
            'Slug' => $slug,
        ]);

        return $slug;
    }

    public function toSearchIndexArray()
    {
        return [
            'type'         => 'public-tag',
            'object_id'    => $this->searchIndexId(),
            'search_title' => $this->searchTitle(),
            'url'          => $this->url(),
            'name'         => $this->name(),
            'user'         => 'all',
        ];
    }

    public function toData()
    {
        return [
            'id'   => $this->id(),
            'url'  => $this->url(),
            'name' => $this->name(),
        ];
    }
}
