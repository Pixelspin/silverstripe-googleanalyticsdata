<?php

namespace Pixelspin\GoogleAnalyticsData\Helpers;

use Exception;
use Google_Client;
use Google_Service_Analytics;
use SilverStripe\Core\Config\Config;

class GoogleAnalyticsHelper {

    private static $analytics = false;

    public static function getPageViewsPerPage($startDate, $start = 1, $results = 10000)
    {
        $data = self::_getAnalytics()->data_ga->get(
            'ga:' . self::_getFirstProfileId(),
            $startDate,
            'yesterday',
            'ga:pageviews,ga:uniquePageviews',
            array(
                'dimensions' => 'ga:pagePath,ga:date',
                'max-results' => $results,
                'start-index' => $start
            )
        );

        return $data;
    }

    public static function getPageViewsPerWebsite($startDate, $start = 1, $results = 10000)
    {
        $data = self::_getAnalytics()->data_ga->get(
            'ga:' . self::_getFirstProfileId(),
            $startDate,
            'yesterday',
            'ga:pageviews,ga:uniquePageviews',
            array(
                'dimensions' => 'ga:date',
                'max-results' => $results,
                'start-index' => $start
            )
        );

        return $data;
    }

    private static function _getAnalytics() {
        if (!self::$analytics) {
            $authConfig = Config::inst()->get('GoogleAnalyticsData', 'auth_config');
            if (!$authConfig) {
                throw new Exception('No auth config file found');
            }
            $client = new Google_Client();
            $client->setAuthConfig($authConfig);
            $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
            self::$analytics = new Google_Service_Analytics($client);
        }
        return self::$analytics;
    }

    private static function _getFirstProfileId() {
        $accounts = self::_getAnalytics()->management_accounts->listManagementAccounts();
        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            $firstAccountId = $items[0]->getId();
            $properties = self::_getAnalytics()->management_webproperties
                ->listManagementWebproperties($firstAccountId);
            if (count($properties->getItems()) > 0) {
                $items = $properties->getItems();
                $firstPropertyId = $items[0]->getId();
                $profiles = self::_getAnalytics()->management_profiles
                    ->listManagementProfiles($firstAccountId, $firstPropertyId);
                if (count($profiles->getItems()) > 0) {
                    $items = $profiles->getItems();
                    return $items[0]->getId();
                } else {
                    throw new Exception('No views (profiles) found for this user.');
                }
            } else {
                throw new Exception('No properties found for this user.');
            }
        } else {
            throw new Exception('No accounts found for this user.');
        }
    }

}
