<?php

namespace App\Http\Controllers;

use DOMDocument;
use App\Page\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrawlController extends Controller
{
    public function search(): View {
        return view('search');
    }

    public function results(Request $request): View {
        $pageUrl = $request->input('page');

        $pageData = new Page();
        $pageData->fetch($pageUrl);

        $averageLoadTime = $pageData->getLoadTime() / 1;
        $uniqueInternalLink = [];
        $uniqueExternalLink = [];

        $links = $pageData->getLinks();

        array_walk($links, function ($link) use (&$uniqueInternalLink, &$uniqueExternalLink) {
            if (!stristr($link, 'http://') && !stristr($link, 'https://')) {
                $uniqueInternalLink[$link] ??= count($uniqueInternalLink);
            } else {
                $uniqueExternalLink[$link] ??= count($uniqueExternalLink);
            }
        });

        return view('results', [
            'averageLoadTime' => $averageLoadTime,
            'numberUniqueInternalLinks' => count($uniqueInternalLink),
            'numberUniqueExternalLinks' => count($uniqueExternalLink),

            'links' => implode(', ', $links),
            'numberOfLinks' => count($links),
            'numberOfPictures' => $pageData->getDocument()->getElementsByTagName('img')->count(),
            'page' => $pageData->getUrl(),
            'pageStatus' => $pageData->getStatusCode(),
        ]);
    }
}
