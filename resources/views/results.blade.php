<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Webcrawler</title>
    </head>
    <body>
        Number Of Pictures: {{ $numberOfPictures }}<br><br>

        Average Page Load: {{ $averageLoadTime }}s<br>
        Average Title Length: {{ $averageTitleLength }}<br>
        Average Word Count: Fill Me In<br>
        Number Of Pages Crawled: {{ count($pagesCrawled) }}<br>
        Number Of Unique Images: Fill Me In<br>
        Number Of Unique Internal Links: {{ $numberUniqueInternalLinks }}<br>
        Number Of Unique External Links: {{ $numberUniqueExternalLinks }}<br>
        <table>
            <tr>
                <th>Page</th>
                <th>Status Code</th>
            </tr>
            @foreach($pagesCrawled as $page)
                <tr>
                    <td>{{ $page->getUrl() }}</td>
                    <td>{{ $page->getStatusCode() }}</td>
                </tr>
            @endforeach
        </table>
    </body>
</html>
