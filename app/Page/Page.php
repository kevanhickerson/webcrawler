<?php

namespace App\Page;

use DOMDocument;

class Page {
    private DOMDocument $document;
    private float $loadTime;    // In microseconds
    private array $links;
    private string $statusCode;
    private string $title;
    private array $uniqueExternalLinks;
    private array $uniqueInternalLinks;
    private string $url;

    public function __construct(DOMDocument $domDocument = new DOMDocument()) {
        $this->document = $domDocument;
        $this->uniqueExternalLinks = [];
        $this->uniqueInternalLinks = [];
    }

    public function fetch(string $url): bool {
        $data = file_get_contents($url);
        $statusCode = $http_response_header[0];

        $startTime = microtime(true);

        // The LIBXML_NOERROR flag is so that this doesn't throw errors on valid HTML5 elements
        if (!$this->document->loadHtml($data, LIBXML_NOERROR)) {
            return false;
        }

        $this->loadTime = microtime(true) - $startTime;
        $this->statusCode = $statusCode;
        $this->url = $url;

        $this->title = $this->document->getElementsByTagName('title')->item(0)?->nodeValue;

        $links = $this->document->getElementsByTagName('a');

        foreach($links as $link) {
            $linkValue = $link?->attributes?->getNamedItem('href')?->nodeValue;
            if ($linkValue) {
                $host = parse_url($linkValue, PHP_URL_HOST);
                $path = parse_url($linkValue, PHP_URL_PATH);

                if ($host || $path) {
                    $this->links[] = $linkValue;

                    if (!$host) {
                        $this->uniqueInternalLinks[$linkValue] ??= count($this->uniqueInternalLinks);
                    } else {
                        $this->uniqueExternalLinks[$linkValue] ??= count($this->uniqueExternalLinks);
                    }
                }
            }
        }

        // Flip the keys and values so that the urls are values
        $this->uniqueInternalLinks = array_flip($this->uniqueInternalLinks);
        $this->uniqueExternalLinks = array_flip($this->uniqueExternalLinks);

        return true;
    }

    public function getDocument(): DOMDocument {
        return $this->document;
    }

    public function getLoadTime(): float {
        return $this->loadTime;
    }

    public function getLinks(): array {
        return $this->links;
    }

    public function getStatusCode(): string {
        return $this->statusCode;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getUniqueExternalLinks(): array {
        return $this->uniqueExternalLinks;
    }

    public function getUniqueInternalLinks(): array {
        return $this->uniqueInternalLinks;
    }

    public function getUrl(): string {
        return $this->url;
    }
}
