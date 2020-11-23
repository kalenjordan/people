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

    public function toSearchIndexArray()
    {
        return [
            'type'      => 'private_tag',
            'object_id' => $this->searchIndexId(),
        ];
    }

    public function toData()
    {
        return [
            // 'id'   => $this->id(),
            'name' => $this->name(),
        ];
    }
}
