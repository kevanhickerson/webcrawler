<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Webcrawler</title>
    </head>
    <body>
        <form action="results">
            @csrf
            Page: <input type="text" id="page" value="agencyanalytics.com"><br><br>
            <input type="submit">
        </form>
    </body>
</html>
