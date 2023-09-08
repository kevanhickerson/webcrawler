<?php

namespace App\Page;

use DOMDocument;

class Page {
    private DOMDocument $document;
    private int $loadTime;
    private array $links;
    private string $statusCode;
    private string $url;

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

        $links = $this->document->getElementsByTagName('a');

        foreach($links as $link) {
            $linkValue = $link?->attributes?->getNamedItem('href')?->nodeValue;
            if ($linkValue) {
                $this->links[] = $linkValue;
            }
        }

        return true;
    }

    public function getDocument(): DOMDocument {
        return $this->document;
    }

    public function getLoadTime(): int {
        return $this->loadTime;
    }

    public function getLinks(): array {
        return $this->links;
    }

    public function getStatusCode(): string {
        return $this->statusCode;
    }

    public function getUrl(): string {
        return $this->url;
    }
}
