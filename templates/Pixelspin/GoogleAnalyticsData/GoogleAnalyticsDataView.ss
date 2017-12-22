<table class="table">
    <tr>
        <th>Date</th>
        <th>Page views</th>
        <th>Unique page views</th>
    </tr>
    <% loop $Target.GoogleAnalyticsDataLastDays %>
        <tr>
            <th>$Date</th>
            <td>$PageViews</td>
            <td>$UniquePageViews</td>
        </tr>
    <% end_loop %>
</table>
