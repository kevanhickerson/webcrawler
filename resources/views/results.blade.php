<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Webcrawler</title>
    </head>
    <body>
        Crawling {{ $page }}<br>
        Number Of Links: {{ $numberOfLinks }}<br>
        Number Of Pictures: {{ $numberOfPictures }}<br>
        <table>
            <tr>
                <th>Page</th>
                <th>Status Code</th>
            </tr>
        </table>
    </body>
</html>
