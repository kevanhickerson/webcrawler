<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Webcrawler</title>
    </head>
    <body>
        Number Of Links: {{ $numberOfLinks }}<br>
        Number Of Pictures: {{ $numberOfPictures }}<br><br>

        Average Page Load: {{ $averageLoadTime }}s<br>
        Average Title Length: Fill Me In<br>
        Average Word Count: Fill Me In<br>
        Number Of Pages Crawled: Fill Me In<br>
        Number Of Unique Images: Fill Me In<br>
        Number Of Unique Internal Links: Fill Me In<br>
        Number Of Unique External Links: Fill Me In<br>
        <table>
            <tr>
                <th>Page</th>
                <th>Status Code</th>
            </tr>
            <tr>
                <td>{{ $page }}</td>
                <td>{{ $pageStatus }}</td>
            </tr>
        </table>
    </body>
</html>
