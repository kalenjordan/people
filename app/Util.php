<?php

namespace App;

class Util
{
    public static function json($object)
    {
        return json_encode($object, JSON_HEX_APOS);
    }

    public static function isProduction()
    {
        return env('APP_ENV') == 'production';
    }

    public static function appUrl()
    {
        return env('APP_URL');
    }

    public static function appName()
    {
        return env('APP_NAME');
    }

    public static function svgLogo()
    {
        return env('LOGO_SVG');
    }

    public static function googleGeocodingApiKey()
    {
        return env('GOOGLE_GEOCODING_API');
    }

    /**
     * @param $user User
     *
     * @return bool
     */
    public static function isAdmin($user)
    {
        if (! $user) {
            return false;
        }

        $adminEmails = explode(',', env('ADMIN_EMAILS'));
        return (in_array($user->email(), $adminEmails));
    }

    public static function airtableUrl()
    {
        return "https://airtable.com/" . env('AIRTABLE_BASE_ID');
    }

    public static function algoliaAppId()
    {
        return env('ALGOLIA_APP_ID');
    }

    public static function algoliaPublicKey()
    {
        return env('ALGOLIA_PUBLIC_KEY');
    }

    public static function algoliaPublicKeyForAdmin()
    {
        return env('ALGOLIA_PUBLIC_KEY_ADMIN');
    }

    /**
     * @param $user User
     *
     * @return mixed
     */
    public static function algoliaPublicKeyFor($user = null)
    {
        if (isset($user) && $user && $user->isAdmin()) {
            return self::algoliaPublicKeyForAdmin();
        }

        return env('ALGOLIA_PUBLIC_KEY');
    }

    public static function algoliaPrivateKey()
    {
        return env('ALGOLIA_PRIVATE_KEY');
    }
}
