<?php

namespace Pixelspin\GoogleAnalyticsData\Tasks;

use DateTime;
use Pixelspin\GoogleAnalyticsData\Helpers\GoogleAnalyticsHelper;
use Pixelspin\GoogleAnalyticsData\Models\GoogleAnalyticsData;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\BuildTask;
use SilverStripe\SiteConfig\SiteConfig;

class GoogleAnalyticsDataSync extends BuildTask
{

    private static $segment = 'GoogleAnalyticsDataSync';
    protected $title = 'Google analytics data sync';
    protected $description = 'Sync google analytics data';

    private $_pages = false;

    public function run($request)
    {
        //Date
        $startDate = $request->getVar('startDate');
        $startDate = $startDate ? $startDate : '1daysAgo';

        //Vars
        $pages = $this->getPages();
        $step = 1;
        $size = 10000;
        $total = false;
        $next = true;

        //Per siteconfig
        try {
            $data = GoogleAnalyticsHelper::getPageViewsPerWebsite($startDate);
        } catch (\Exception $exception) {
            var_dump($exception);
            die;
        }
        $siteConfig = SiteConfig::current_site_config();
        foreach ($data as $row) {
            //Date
            $date = new DateTime($row[0]);
            $formatedDate = $date->format('Y-m-d');

            //Day data
            $day = GoogleAnalyticsData::get()->filter(array(
                'SiteConfigID' => $siteConfig->ID,
                'IsMonth' => false,
                'Date' => $formatedDate
            ))->first();
            $isNewDay = false;
            if (!$day) {
                $day = new GoogleAnalyticsData();
                $isNewDay = true;
            }
            $day->SiteConfigID = $siteConfig->ID;
            $day->Date = $formatedDate;
            $day->PageViews = $row[1];
            $day->UniquePageViews = $row[2];
            $day->write();

            //Month data
            $month = GoogleAnalyticsData::get()->filter(array(
                'SiteConfigID' => $siteConfig->ID,
                'IsMonth' => true,
                'Month' => $date->format('m')
            ))->first();
            $isNewMonth = false;
            if (!$month) {
                $month = new GoogleAnalyticsData();
                $month->PageViews = 0;
                $month->UniquePageViews = 0;
                $isNewMonth = true;
            }
            if ($isNewDay || $isNewMonth) {
                $month->SiteConfigID = $siteConfig->ID;
                $month->IsMonth = true;
                $month->Month = $date->format('m');
                $month->PageViews = $month->PageViews + $row[1];
                $month->UniquePageViews = $month->UniquePageViews + $row[2];
                $month->write();
            }

            echo "Date: $formatedDate\tViews: $row[2]\t$siteConfig->Title\n";
        }

        //Per page
        while ($next) {
            //Get results
            $start = $size * ($step - 1) + 1;
            try {
                $data = GoogleAnalyticsHelper::getPageViewsPerPage($startDate, $start, $size);
            } catch (\Exception $exception) {
                var_dump($exception);
                die;
            }

            //Total results
            if (!$total) {
                $total = $data->getTotalResults();
            }

            //Per page
            foreach ($data as $row) {
                //Find page
                $page = false;
                if (array_key_exists($row[0], $pages)) {
                    $page = $pages[$row[0]];
                } elseif (array_key_exists("$row[0]/", $pages)) {
                    $page = $pages["$row[0]/"];
                }
                if (!$page) {
                    continue;
                }

                //Date
                $date = new DateTime($row[1]);
                $formatedDate = $date->format('Y-m-d');

                //Day data
                $day = GoogleAnalyticsData::get()->filter(array(
                    'SiteTreeID' => $page->ID,
                    'IsMonth' => false,
                    'Date' => $formatedDate
                ))->first();
                $isNewDay = false;
                if (!$day) {
                    $day = new GoogleAnalyticsData();
                    $isNewDay = true;
                }
                $day->SiteTreeID = $page->ID;
                $day->Date = $formatedDate;
                $day->PageViews = $row[2];
                $day->UniquePageViews = $row[3];
                $day->write();

                //Month data
                $month = GoogleAnalyticsData::get()->filter(array(
                    'SiteTreeID' => $page->ID,
                    'IsMonth' => true,
                    'Month' => $date->format('m')
                ))->first();
                $isNewMonth = false;
                if (!$month) {
                    $month = new GoogleAnalyticsData();
                    $month->PageViews = 0;
                    $month->UniquePageViews = 0;
                    $isNewMonth = true;
                }
                if ($isNewDay || $isNewMonth) {
                    $month->SiteTreeID = $page->ID;
                    $month->IsMonth = true;
                    $month->Month = $date->format('m');
                    $month->PageViews = $month->PageViews + $row[2];
                    $month->UniquePageViews = $month->UniquePageViews + $row[3];
                    $month->write();
                }

                echo "Date: $formatedDate\tViews: $row[2]\t$page->Title\n";
            }

            //Check for last
            if ($start + $size > $total) {
                $next = false;
            }
            $step++;
        }
    }

    /**
     * Get page relative url's
     * @return array
     */
    public function getPages()
    {
        if ($this->_pages) {
            return $this->_pages;
        }
        $pages = [];
        foreach (SiteTree::get() as $page) {
            $pages[$page->Link()] = $page;
        }
        $this->_pages = $pages;
        return $pages;
    }

}
