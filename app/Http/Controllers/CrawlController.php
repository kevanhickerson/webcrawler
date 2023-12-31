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

        $pages[] = $this->fetch($pageUrl);

        $linksToCrawl = $pages[0]->getUniqueInternalLinks();

        // Crawl some of the internal urls that we've found on the first page
        for ($i = 0; $i < count($linksToCrawl); $i++) {
            // Stop after a maximum of 6 pages have been crawled
            if (count($pages) >= 6) {
                break;
            }

            $link = $linksToCrawl[$i];

            if ($link === '/') {
                continue;
            }

            $pages[] = $this->fetch($domain . $link);
        }

        $averageLoadTime = 0;
        $averageTitleLength = 0;
        $averageWordCount = 0;
        $uniqueInternalLink = [];
        $uniqueExternalLink = [];
        $uniqueImages = [];

        foreach($pages as $page) {
            $averageLoadTime += $page->getLoadTime();
            $averageTitleLength += strlen($page->getTitle());
            $averageWordCount += $page->getWordCount();
            $uniqueExternalLink += $page->getUniqueExternalLinks();
            $uniqueInternalLink += $page->getUniqueInternalLinks();
            $uniqueImages += $page->getUniqueImages();
        }

        $averageLoadTime /= count($pages);
        // Change microseconds to seconds
        $averageLoadTime /= 1000000;
        $averageTitleLength /= count($pages);
        $averageWordCount /= count($pages);

        return view('results', [
            'averageLoadTime' => $averageLoadTime,
            'averageTitleLength' => $averageTitleLength,
            'averageWordCount' => $averageWordCount,
            'numberUniqueInternalLinks' => count($uniqueInternalLink),
            'numberUniqueExternalLinks' => count($uniqueExternalLink),
            'numberUniqueImages' => count($uniqueImages),
            'pagesCrawled' => $pages,
        ]);
    }

    private function fetch(string $url) {
        $pageData = new Page();
        $pageData->fetch($url);

        return $pageData;
    }

    private function getHost(string $url) {
        return parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
    }
}
