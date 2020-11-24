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

    public static function linkedinUsername()
    {
        return env('LINKEDIN_USERNAME');
    }

    public static function linkedinPassword()
    {
        return env('LINKEDIN_PASSWORD');
    }

    public static function parseLinkedInField($linkedIn)
    {
        if (strpos($linkedIn, "http") === 0) {
            return $linkedIn;
        }

        if (strpos($linkedIn, '/in/') !== false) {
            $parts = explode('/in/', $linkedIn);
            return "https://linkedin.com/in/" . $parts[1];
        }

        return "Bad linkedin format: $linkedIn";
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

    public static function adminUserId()
    {
        return env('ADMIN_USER_ID');
    }

    /**
     * @param $user User
     *
     * @return mixed
     */
    public static function algoliaPublicKeyFor($user = null)
    {
        if (isset($user) && $user) {
            return $user->algoliaApiKey();
        }

        return Util::algoliaPublicKey();
    }

    public static function algoliaPrivateKey()
    {
        return env('ALGOLIA_PRIVATE_KEY');
    }

    public static function slugify($string)
    {
        $string = str_replace(" ", "-", $string);
        $string = strtolower($string);

        return $string;
    }
}
