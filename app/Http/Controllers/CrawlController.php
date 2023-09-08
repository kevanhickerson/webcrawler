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

        return view('results', [
            'averageLoadTime' => $averageLoadTime,
            'links' => implode(', ', $pageData->getLinks()),
            'numberOfLinks' => count($pageData->getLinks()),
            'numberOfPictures' => $pageData->getDocument()->getElementsByTagName('img')->count(),
            'page' => $pageData->getUrl(),
            'pageStatus' => $pageData->getStatusCode(),
        ]);
    }
}
