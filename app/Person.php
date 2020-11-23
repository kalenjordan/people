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

    public function linkedInUrl()
    {
        return isset($this->fields->{'LinkedIn'}) ? $this->fields->{'LinkedIn'} : null;
    }

    public function publicTagIds()
    {
        return isset($this->fields->{'Public Tags'}) ? $this->fields->{'Public Tags'} : [];
    }

    public function publicTagNames()
    {
        return isset($this->fields->{'Public Tag Names'}) ? $this->fields->{'Public Tag Names'} : [];
    }

    public function privateTagsFor(User $user)
    {
        $tags = $this->_privateTags();

        $count = count($tags);
        for ($i = 0; $i < $count; $i++) {
            /** @var PrivateTag $tag */
            $tag = $tags[$i];
            if ($tag->userId() != $user->id()) {
                unset($tags[$i]);
            }
        }

        $tags = array_values($tags);

        return $tags;
    }

    public function privateTagNames()
    {
        return isset($this->fields->{'Private Tag Names'}) ? $this->fields->{'Private Tag Names'} : [];
    }

    public function privateTagUserIds()
    {
        return isset($this->fields->{'Private Tag Users'}) ? $this->fields->{'Private Tag Users'} : [];
    }

    protected function _privateTags()
    {
        $names = $this->privateTagNames();
        $userIds = $this->privateTagUserIds();
        $tags = [];

        for ($i = 0; $i < count($names); $i++) {
            $tags[] = new PrivateTag([
                'Name' => $names[$i],
                'User' => [$userIds[$i]],
            ]);
        }

//        usort($tags, function (PrivateTag $a, PrivateTag $b) {
//            return $a->clientSortOrder() > $b->clientSortOrder();
//        });

        return $tags;
    }


    public function description()
    {
        return isset($this->fields->{'Description'}) ? $this->fields->{'Description'} : null;
    }

    public function url()
    {
        return '/' . $this->slug();
    }

    public function avatar()
    {
        return isset($this->fields->{'Avatar'}[0]->url) ? $this->fields->{'Avatar'}[0]->url : null;
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
            'type'         => 'person',
            'object_id'    => $this->searchIndexId(),
            'search_title' => $this->searchTitle(),
            'url'          => $this->url(),
            'avatar'       => $this->avatar(),
        ];
    }

    public function toData()
    {
        return [
            'id'          => $this->id(),
            'url'         => $this->url(),
            'slug'        => $this->slug(),
            'name'        => $this->name(),
            'avatar'      => $this->avatar(),
            'public_tags' => $this->publicTagNames(),
        ];
    }

    public function toDataFor(User $user)
    {
        $data = $this->toData();
        $privateTags = $this->privateTagsFor($user);

        $privateTagsData = [];
        foreach ($privateTags as $privateTag) {
            /** @var PrivateTag $privateTag */
            $privateTagsData[] = $privateTag->toData();
        }

        $data['private_tags'] = $privateTagsData;

        return $data;
    }
}
