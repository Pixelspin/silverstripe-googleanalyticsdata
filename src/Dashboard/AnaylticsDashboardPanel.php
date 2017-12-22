<?php

namespace Pixelspin\GoogleAnalyticsData\Dashboard;

use Pixelspin\CMSDashboard\Admin\DashboardPanel;

if (!class_exists(DashboardPanel::class)) {
    return;
}

class AnaylticsDashboardPanel extends DashboardPanel {

    private static $icon = 'font-icon-graph-bar';

    public function getTitle()
    {
        return 'Website visitors';
    }

    public function getContent()
    {
        return $this->renderWith('Pixelspin\GoogleAnalyticsData\Dashboard\AnaylticsDashboardPanel');
    }

}
