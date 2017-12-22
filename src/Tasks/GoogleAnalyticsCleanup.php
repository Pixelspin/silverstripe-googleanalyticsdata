<?php

namespace Pixelspin\GoogleAnalyticsData\Tasks;

use DateTime;
use Pixelspin\GoogleAnalyticsData\Models\GoogleAnalyticsData;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\BuildTask;

class GoogleAnalyticsCleanup extends BuildTask {

    private static $segment = 'GoogleAnalyticsCleanup';
    protected $title = 'Google analytics data cleanup';
    protected $description = 'Cleanup google analytics data';

    public function run($request)
    {
        $daysLimit = Config::inst()->get('GoogleAnalyticsData', 'days_cleanup');
        if(!$daysLimit){
            $daysLimit = 30;
        }
        $monthsLimit = Config::inst()->get('GoogleAnalyticsData', 'months_cleanup');
        if(!$monthsLimit){
            $monthsLimit = 12;
        }

        $dayDate = (new DateTime())->modify('-'.$daysLimit.' day');
        $daysToDelete = GoogleAnalyticsData::get()->filter(array(
            'IsMonth' => false,
            'Date:LessThanOrEqual' => $dayDate->format('Y-m-d')
        ));
        foreach($daysToDelete as $day){
            $day->delete();
        }

        $monthDate = (new DateTime())->modify('-'.$monthsLimit.' month');
        $monthsToDelete = GoogleAnalyticsData::get()->filter(array(
            'IsMonth' => true,
            'Date:LessThanOrEqual' => $monthDate->format('Y-m-d')
        ));
        foreach($monthsToDelete as $month){
            $month->delete();
        }

    }

}
