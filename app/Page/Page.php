<?php

namespace App\Page;

use DOMDocument;

class Page {
    private DOMDocument $document;
    private string $url;
    private string $statusCode;
    private int $loadTime;

    public function __construct(DOMDocument $domDocument = new DOMDocument()) {
        $this->document = $domDocument;
    }

    public function fetch(string $url): bool {
        $data = file_get_contents($url);
        $statusCode = $http_response_header[0];

        $startTime = time();

        if (!$this->document->loadHtml($data, LIBXML_NOERROR)) {
            return false;
        }

        $this->loadTime = time() - $startTime;
        $this->statusCode = $statusCode;
        $this->url = $url;

        return true;
    }

    public function getDocument(): DOMDocument {
        return $this->document;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getStatusCode(): string {
        return $this->statusCode;
    }

    public function getLoadTime(): int {
        return $this->loadTime;
    }
}
