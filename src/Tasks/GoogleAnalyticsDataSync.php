<?php

namespace Pixelspin\GoogleAnalyticsData\Tasks;

use Pixelspin\GoogleAnalyticsData\Helpers\GoogleAnalyticsHelper;
use SilverStripe\Dev\BuildTask;

class GoogleAnalyticsDataSync extends BuildTask {

    private static $segment = 'GoogleAnalyticsDataSync';
    protected $title = 'Google analytics data sync';
    protected $description = 'Sync google analytics data';

    public function run($request)
    {
        try {
            $data = GoogleAnalyticsHelper::getPageViewsPerPage('2017-12-10');
            var_dump( $data );
        } catch (\Exception $exception) {
            var_dump($exception);
        }

        //Totaal aantal bezoekers
        //Totaal aantal pageviews
        //Per pagina bezoekers en pageviews

        //Opslaan per pagina/datum en per siteconfig/datum
        //Cleanup na x aantal dagen?

        //Hoe opgeven welk account/property/view etc
        //Kunnen filteren op bv domein

        //Dashboard panel als module bestaat
        //Per pagina een grafiek tonen
        //een raport?


//        $startDate = $request->getVar('startDate');
//        $startDate = $startDate ? $startDate : '1daysAgo';
//
//        $pages = $this->getPages();
//
//        $service = AnalyticsService::singleton();
//
//        $step = 1;
//        $size = 10000;
//        $total = false;
//        $next = true;
//
//        while ($next) {
//            $start = $size * ($step - 1) + 1;
//            $data = $service->getPageViewsPerPage($startDate, $start, $size);
//
//            if (!$total) {
//                $total = $data->getTotalResults();
//            }
//
//            foreach ($data as $row) {
//                $page = false;
//                if (array_key_exists($row[0], $pages)) {
//                    $page = $pages[$row[0]];
//                } elseif (array_key_exists("$row[0]/", $pages)) {
//                    $page = $pages["$row[0]/"];
//                }
//
//                if (!$page) {
//                    continue;
//                }
//
//                $date = (new DateTime($row[1]))->format('Y-m-d');
//
//                $object = AnalyticsSiteTreeData::get()->filter(array(
//                    'SiteTreeID' => $page->ID,
//                    'Date' => $date
//                ))->first();
//
//                if (!$object) {
//                    $object = new AnalyticsSiteTreeData();
//                }
//
//                $object->SiteTreeID = $page->ID;
//                $object->Date = $date;
//                $object->Views = $row[2];
//                $object->write();
//
//                echo "Date: $date\tViews: $row[2]\t$page->Title\n";
//            }
//
//            if ($start + $size > $total) {
//                $next = false;
//            }
//
//            $step++;
//        }

    }

//    public function getPages()
//    {
//        $pages = [];
//
//        foreach (SiteTree::get() as $page) {
//            $pages[$page->Link()] = $page;
//        }
//
//        return $pages;
//    }

}
