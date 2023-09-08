<?php

namespace App\Page;

use DOMDocument;
use DOMXPath;

class Page {
    private DOMDocument $document;
    private float $loadTime;    // In microseconds
    private string $statusCode;
    private string $title;
    private array $uniqueExternalLinks;
    private array $uniqueInternalLinks;
    private array $uniqueImages;
    private string $url;
    private int $wordCount;

    public function __construct(DOMDocument $domDocument = new DOMDocument()) {
        $this->document = $domDocument;
        $this->loadTime = 0;
        $this->statusCode = '';
        $this->title = '';
        $this->uniqueExternalLinks = [];
        $this->uniqueInternalLinks = [];
        $this->uniqueImages = [];
        $this->url = '';
        $this->wordCount = 0;
    }

    public function fetch(string $url): bool {
        $data = file_get_contents($url);
        $statusCode = $http_response_header[0];

        $startTime = microtime(true);

        // The LIBXML_NOERROR flag is so that this doesn't throw errors on valid HTML5 elements
        if (!$this->document->loadHtml($data, LIBXML_NOERROR | LIBXML_NOBLANKS)) {
            return false;
        }

        $this->loadTime = microtime(true) - $startTime;
        $this->statusCode = $statusCode;
        $this->url = $url;

        $xpath = new DOMXPath($this->document);
        // Get all text nodes that are not in a script tag and not an empty string
        $textNodes = $xpath->query('/html/body//text()[
            not(ancestor::script) and
            not(normalize-space(.) = "")
        ]');

        foreach ($textNodes as $textNode) {
            $this->wordCount += count(explode(' ', trim($textNode->textContent)));
        }

        $this->title = $this->document->getElementsByTagName('title')->item(0)?->nodeValue ?? '';

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

        $images = $this->document->getElementsByTagName('img');

        foreach($images as $image) {
            $imageValue = $image?->attributes?->getNamedItem('src')?->nodeValue;
            if ($imageValue) {
                $this->uniqueImages[$imageValue] ??= count($this->uniqueImages);
            }
        }

        // Flip the keys and values so that the urls are values
        $this->uniqueInternalLinks = array_flip($this->uniqueInternalLinks);
        $this->uniqueExternalLinks = array_flip($this->uniqueExternalLinks);
        $this->uniqueImages = array_flip($this->uniqueImages);

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

    public function getUniqueImages(): array {
        return $this->uniqueImages;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getWordCount(): int {
        return $this->wordCount;
    }
}
