<?php

namespace App\Http\Controllers;

use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrawlController extends Controller
{
    public function search(): View {
        return view('search');
    }

    public function results(Request $request): View {
        $page = $request->input('page');

        $pageData = $this->getPage($page);

        return view('results', [
            'numberOfLinks' => $pageData->getElementsByTagName('a')->count(),
            'numberOfPictures' => $pageData->getElementsByTagName('img')->count(),
            'page' => $page,
        ]);
    }

    private function getPage(string $url): DOMDocument {
        $data = file_get_contents($url);
//        print_r($data);die();

        $doc = new DOMDocument();
        $doc->loadHtml($data, LIBXML_NOERROR);

        return $doc;
    }
}
