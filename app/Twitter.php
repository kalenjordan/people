<?php

namespace App;

class Twitter
{

    /**
     * @param $screenname
     *
     * @return array
     * @throws \Exception
     */
    public static function getProfile($screenname)
    {
        $settings = array(
            'oauth_access_token'        => env('TWITTER_ACCESS_TOKEN'),
            'oauth_access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
            'consumer_key'              => env('TWITTER_CONSUMER_KEY'),
            'consumer_secret'           => env('TWITTER_CONSUMER_SECRET'),
        );

        $url = 'https://api.twitter.com/1.1/users/show.json';
        $getfield = "?screen_name=$screenname";
        $requestMethod = 'GET';

        $twitter = new \TwitterAPIExchange($settings);
        $response = $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();
        $response = json_decode($response);

        $avatar = $response->profile_image_url_https;
        $avatar = str_replace('_normal', '', $avatar);
        $url = isset($response->entities->url->urls[0]->expanded_url) ? $response->entities->url->urls[0]->expanded_url : null;
        $data = [
            'name'        => $response->name,
            'username'     => $screenname,
            'avatar'      => $avatar,
            'description' => isset($response->description) ? $response->description : null,
            'url'         => $url,
        ];

        return $data;
    }

    /**
     * @param $screenname
     *
     * @return array
     * @throws \Exception
     */
    public static function searchProfiles($query)
    {
        $settings = array(
            'oauth_access_token'        => env('TWITTER_ACCESS_TOKEN'),
            'oauth_access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
            'consumer_key'              => env('TWITTER_CONSUMER_KEY'),
            'consumer_secret'           => env('TWITTER_CONSUMER_SECRET'),
        );

        $url = 'https://api.twitter.com/1.1/users/search.json';
        $getfield = "?q=$query&count=10";
        $requestMethod = 'GET';

        $twitter = new \TwitterAPIExchange($settings);
        $response = $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        return json_decode($response);
    }
}
