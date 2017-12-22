<?php

namespace Pixelspin\GoogleAnalyticsData\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\SiteConfig\SiteConfig;

class GoogleAnalyticsData extends DataObject {

    private static $db = array(
        'IsMonth' => 'Boolean',
        'Month' => 'Int',
        'Date' => 'Date',
        'PageViews' => 'Int',
        'UniquePageViews' => 'Int'
    );

    private static $has_one = array(
        'SiteTree' => SiteTree::class,
        'SiteConfig' => SiteConfig::class
    );

}
