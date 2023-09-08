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

        $domain = $this->getHost($pageUrl);

        $pages = $this->fetch($pageUrl);

        $averageLoadTime = $pages[0]->getLoadTime() / count($pages);
        $averageTitleLength = strlen($pages[0]->getTitle()) / count($pages);
        $uniqueInternalLink = 0;
        $uniqueExternalLink = 0;
        $links = [];

        foreach($pages as $page) {
            $averageLoadTime += $page->getLoadTime();
            $links = array_merge($links, $page->getLinks());
            $uniqueExternalLink += count($page->getUniqueExternalLinks());
            $uniqueInternalLink += count($page->getUniqueInternalLinks());
        }

        return view('results', [
            'averageLoadTime' => $averageLoadTime,
            'averageTitleLength' => $averageTitleLength,
            'numberUniqueInternalLinks' => $uniqueInternalLink,
            'numberUniqueExternalLinks' => $uniqueExternalLink,

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

    private function getHost(string $url) {
        return parse_url($url, PHP_URL_HOST);
    }
}
