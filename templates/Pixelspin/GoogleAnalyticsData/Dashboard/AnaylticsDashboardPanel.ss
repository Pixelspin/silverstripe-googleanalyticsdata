<table>
    <tr>
        <th>Date</th>
        <th>Page views</th>
        <th>Unique page views</th>
    </tr>
<% loop $SiteConfig.GoogleAnalyticsDataLastDays %>
    <tr>
        <th>$Date</th>
        <td>$PageViews</td>
        <td>$UniquePageViews</td>
    </tr>
<% end_loop %>
</table>
