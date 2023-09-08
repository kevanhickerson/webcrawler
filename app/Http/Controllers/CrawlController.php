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

        if (!stristr($pageUrl, 'http://') && !stristr($pageUrl, 'https://')) {
            $pageUrl = 'http://' . $pageUrl;
        }

        $pages = $this->fetch($pageUrl);

        $averageLoadTime = $pages[0]->getLoadTime() / 1;
        $uniqueInternalLink = [];
        $uniqueExternalLink = [];
        $links = [];

        foreach($pages as $page) {
            $averageLoadTime += $page->getLoadTime();
            $links = array_merge($links, $page->getLinks());
        }

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
            'numberOfPictures' => $pages[0]->getDocument()->getElementsByTagName('img')->count(),
            'page' => $pages[0]->getUrl(),
            'pageStatus' => $pages[0]->getStatusCode(),
        ]);
    }

    private function fetch(string $url) {
        $pageData = new Page();
        $pageData->fetch($url);

        return [$pageData];
    }
}
