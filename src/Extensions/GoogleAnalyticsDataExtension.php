<?php

namespace Pixelspin\GoogleAnalyticsData\Extensions;

use DateTime;
use Pixelspin\GoogleAnalyticsData\Models\GoogleAnalyticsData;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;

class GoogleAnalyticsDataExtension extends DataExtension
{

    private static $has_many = array(
        'GoogleAnalyticsData' => GoogleAnalyticsData::class
    );

    public function updateCMSFields(FieldList $fields)
    {
        $hide = Config::inst()->get('GoogleAnalyticsData', 'hide_data_on_page');
        if (!$hide) {
            $viewer = ViewableData::create();
            $viewer->Target = $this->owner;
            $fields->addFieldToTab('Root.Visitors', new LiteralField('GoogleAnalyticsDataView', $viewer->renderWith('Pixelspin\GoogleAnalyticsData\GoogleAnalyticsDataView')));
        }
    }

    public function getGoogleAnalyticsDataLastDays($numDays = 7)
    {
        $out = new ArrayList();
        $today = (new DateTime())->modify('-1 day');
        $minDay = (new DateTime())->modify('-' . ($numDays + 1) . ' day');
        $type = $this->owner->ClassName == 'SilverStripe\SiteConfig\SiteConfig' ? 'SiteConfigID' : 'SiteTreeID';
        $datas = GoogleAnalyticsData::get()->filter(array(
            'IsMonth' => false,
            $type => $this->owner->ID,
            'Date:GreaterThanOrEqual' => $minDay->format('Y-m-d'),
            'Date:LessThanOrEqual' => $today->format('Y-m-d')
        ));
        $dates = array();
        foreach ($datas as $data) {
            $dates[$data->Date] = $data;
        }
        for ($i = 0; $i < $numDays; $i++) {
            $currData = array_key_exists($today->format('Y-m-d'), $dates) ? $dates[$today->format('Y-m-d')] : false;
            $out->push(new ArrayData(array(
                'Date' => DBField::create_field('Date', $today->format('Y-m-d')),
                'PageViews' => $currData ? $currData->PageViews : 0,
                'UniquePageViews' => $currData ? $currData->UniquePageViews : 0
            )));
            $today = $today->modify('-1 day');
        }
        return $out;
    }

}
