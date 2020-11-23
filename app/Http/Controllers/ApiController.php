<?php

namespace App\Http\Controllers;

use App\Person;
use App\PrivateTag;
use App\PublicTag;
use App\Thing;
use App\User;
use App\Util;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function newsletterSubscribe(Request $request)
    {
        try {
            return $this->_newsletterSubscribe($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    protected function _newsletterSubscribe(Request $request)
    {
        $email = $request->input('email');

        (new User())->create([
            'Email'                   => $email,
            'Subscribe to Newsletter' => true,
        ]);

        return [
            'success' => true,
        ];
    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function geocode(Request $request)
    {
        try {
            return $this->_geocode($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    protected function _geocode(Request $request)
    {
        $user = $this->_loadFromApiKey($request);

        $latlon = $request->input('latlon');

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latlon&sensor=true&key=" . Util::googleGeocodingApiKey();
        $result = json_decode(file_get_contents($url), true);

        $firstResult = $result['results'][0];
        $city = "";
        foreach ($firstResult['address_components'] as $component) {
            if ($component['types'][0] == 'locality') {
                $city = $component['long_name'];
            } elseif ($component['types'][0] == 'administrative_area_level_1') {
                $state = $component['long_name'];
            } elseif ($component['types'][0] == 'country') {
                $country = $component['long_name'];
            }
        }

        $location = "$city, $state, $country";
        $user->save([
            'Location' => $location,
        ]);

        return ['location' => $location];
    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function geocodeDelete(Request $request)
    {
        try {
            return $this->_geocodeDelete($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    protected function _geocodeDelete(Request $request)
    {
        $user = $this->_loadFromApiKey($request);
        $user->save([
            'Location' => null,
        ]);

        return ['success' => true];
    }

    /**
     * @param Request $request
     *
     * @return User
     * @throws \Exception
     */
    protected function _loadFromApiKey(Request $request)
    {
        $apiKey = $request->input('api_key');
        if (!$apiKey) {
            throw new \Exception("Missing API key");
        }

        $user = (new User())->lookupWithFilter("{Api Key} = '$apiKey'");
        if (!$user) {
            throw new \Exception("Couldn't find user with api key: $apiKey");
        }

        return $user;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    public function publicTags(Request $request)
    {
        $query = $request->input('query');

        $params = array(
            "sort"            => array(array('field' => 'Name', 'direction' => "asc")),
            "maxRecords"      => 10,
            "filterByFormula" => "FIND(LOWER('$query'), LOWER(Name)) > 0",
        );
        $tags = (new PublicTag())->getRecords($params);

        $data = [];
        foreach ($tags as $tag) {
            /** @var PublicTag $tag */
            $data[] = $tag->toData();
        }

        return $data;
    }

    public function personPublicTag(Request $request, $slug)
    {
        /** @var Person $person */
        $person = (new Person())->lookupWithFilter("Slug = '$slug'");
        if (!$person) {
            abort(404);
        }

        $tagId = $request->input('tag');
        $newTag = $request->input('new_tag');
        if ($tagId) {
            $tag = (new PublicTag())->load($tagId);
            if (!$tag) {
                throw new \Exception("Couldn't find tag by ID: $tagId");
            }
        } elseif ($newTag) {
            $tag = (new PublicTag())->create([
                'Name' => $newTag,
            ]);
        }

        $tags = $person->publicTagIds();
        if (($key = array_search($tag->id(), $tags)) !== false) {
            unset($tags[$key]);
            $tags = array_values($tags);
            $message = "Removed tag";
        } else {
            $tags[] = $tag->id();
            $message = "Added tag";
        }
        $person->save([
            'Public Tags' => $tags,
        ]);

        return [
            'success' => true,
            'message' => $message,
            'person'  => $person->toData(),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    public function privateTags(Request $request)
    {
        $user = $this->_loadFromApiKey($request);
        if (!$user) {
            throw new \Exception("User not found");
        }

        $query = $request->input('query');

        $params = array(
            "sort"            => array(array('field' => 'Name', 'direction' => "asc")),
            "maxRecords"      => 10,
            "filterByFormula" => "AND(
                FIND(LOWER('$query'), LOWER(Name)) > 0,
                {User Record ID} = '{$user->id()}'
            )",
        );
        $tags = (new PrivateTag())->getRecords($params);

        $data = [];
        foreach ($tags as $tag) {
            /** @var PublicTag $tag */
            $data[] = $tag->toData();
        }

        return $data;
    }

    public function personPrivateTag(Request $request, $slug)
    {
        $user = $this->_loadFromApiKey($request);
        if (!$user) {
            throw new \Exception("User not found");
        }

        /** @var Person $person */
        $person = (new Person())->lookupWithFilter("Slug = '$slug'");
        if (!$person) {
            abort(404);
        }

        $tagId = $request->input('tag');
        $newTag = $request->input('new_tag');
        if ($newTag) {
            $tag = (new PrivateTag())->create([
                'Name'   => $newTag,
                'User'   => [$user->id()],
                'Person' => [$person->id()],
            ]);
            return [
                'success' => true,
                'person'  => $person->toDataFor($user),
            ];
        }


        if (!$tagId) {
            throw new \Exception("Missing tag id");
        }

        $tag = (new PrivateTag())->load($tagId);
        if (!$tag) {
            throw new \Exception("Couldn't find tag by ID: $tagId");
        }

        $people = $tag->peopleIds();
        if (($key = array_search($person->id(), $people)) !== false) {
            unset($people[$key]);
            $people = array_values($people);
        } else {
            $people[] = $person->id();
        }
        $tag->save([
            'People' => $people,
        ]);

        // Refresh
        $person = $person->load($person->id());

        return [
            'success' => true,
            'person'  => $person->toDataFor($user),
        ];
    }
}
