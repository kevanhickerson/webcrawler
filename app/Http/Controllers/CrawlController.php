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
//         var_dump($linksToCrawl);die();

        // Crawl some of the internal urls that we've found on the first page
        for ($i = 0; $i < count($linksToCrawl); $i++) {
            // Stop after a maximum of 6 pages have been crawled
            if (count($pages) > 6) {
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
        $uniqueInternalLink = 0;
        $uniqueExternalLink = 0;

        foreach($pages as $page) {
            $averageLoadTime += $page->getLoadTime();
            $averageTitleLength += strlen($page->getTitle());
            $uniqueExternalLink += count($page->getUniqueExternalLinks());
            $uniqueInternalLink += count($page->getUniqueInternalLinks());
        }

        $averageLoadTime /= count($pages);
        $averageTitleLength /= count($pages);

        return view('results', [
            'averageLoadTime' => $averageLoadTime,
            'averageTitleLength' => $averageTitleLength,
            'numberUniqueInternalLinks' => $uniqueInternalLink,
            'numberUniqueExternalLinks' => $uniqueExternalLink,
            'pagesCrawled' => $pages,

            'numberOfPictures' => $pages[0]->getDocument()->getElementsByTagName('img')->count(),
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
