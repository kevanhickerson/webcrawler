<?php

namespace Tests\Feature\App\Page;

use App\Page\Page;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PageTest extends TestCase
{
    public function testFetchWithMostlyEmptyDom(): void
    {
        Http::fake([
            '*' => Http::response('
                <html>
                    <head></head>
                    <body></body>
                </html>
            ', 200),
        ]);

        $page = new Page();
        $page->fetch('http://example.com');

        $this->assertEquals('http://example.com', $page->getUrl());
        $this->assertEquals('200', $page->getStatusCode());
        $this->assertEquals('', $page->getTitle());
        $this->assertEquals([], $page->getUniqueExternalLinks());
        $this->assertEquals([], $page->getUniqueInternalLinks());
        $this->assertEquals([], $page->getUniqueImages());
        $this->assertEquals(0, $page->getWordCount());
    }

    public function testFetchWithUniqueInternalLinks(): void
    {
        Http::fake([
            '*' => Http::response('
                <html>
                    <head></head>
                    <body>
                        <a href="/internal1">I1</a>
                        <a href="/internal1">I1</a>
                        <a href="/internal2">I2</a>
                    </body>
                </html>
            ', 200),
        ]);

        $page = new Page();
        $page->fetch('http://example.com');

        $this->assertEquals('http://example.com', $page->getUrl());
        $this->assertEquals('200', $page->getStatusCode());
        $this->assertEquals('', $page->getTitle());
        $this->assertEquals([], $page->getUniqueExternalLinks());
        $this->assertEquals(['/internal1', '/internal2'], $page->getUniqueInternalLinks());
        $this->assertEquals([], $page->getUniqueImages());
        $this->assertEquals(3, $page->getWordCount());
    }

    public function testFetchWithUniqueExternalLinks(): void
    {
        Http::fake([
            '*' => Http::response('
                <html>
                    <head></head>
                    <body>
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external2">E2</a>
                    </body>
                </html>
            ', 200),
        ]);

        $page = new Page();
        $page->fetch('http://example.com');

        $this->assertEquals('http://example.com', $page->getUrl());
        $this->assertEquals('200', $page->getStatusCode());
        $this->assertEquals('', $page->getTitle());
        $this->assertEquals(['http://example.com/external1', 'http://example.com/external2'], $page->getUniqueExternalLinks());
        $this->assertEquals([], $page->getUniqueInternalLinks());
        $this->assertEquals([], $page->getUniqueImages());
        $this->assertEquals(3, $page->getWordCount());
    }

    public function testFetchWithUniqueInternalAndExternalLinks(): void
    {
        Http::fake([
            '*' => Http::response('
                <html>
                    <head></head>
                    <body>
                        <a href="/internal1">I1</a>
                        <a href="/internal1">I1</a>
                        <a href="/internal2">I2</a>
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external2">E2</a>
                    </body>
                </html>
            ', 200),
        ]);

        $page = new Page();
        $page->fetch('http://example.com');

        $this->assertEquals('http://example.com', $page->getUrl());
        $this->assertEquals('200', $page->getStatusCode());
        $this->assertEquals('', $page->getTitle());
        $this->assertEquals(['http://example.com/external1', 'http://example.com/external2'], $page->getUniqueExternalLinks());
        $this->assertEquals(['/internal1', '/internal2'], $page->getUniqueInternalLinks());
        $this->assertEquals([], $page->getUniqueImages());
        $this->assertEquals(6, $page->getWordCount());
    }

    public function testFetchWithUniqueImages(): void
    {
        Http::fake([
            '*' => Http::response('
                <html>
                    <head></head>
                    <body>
                        <img src="http://example.com/external1.png">
                        <img src="http://example.com/external1.png">
                        <img src="http://example.com/external2.png">
                    </body>
                </html>
            ', 200),
        ]);

        $page = new Page();
        $page->fetch('http://example.com');

        $this->assertEquals('http://example.com', $page->getUrl());
        $this->assertEquals('200', $page->getStatusCode());
        $this->assertEquals('', $page->getTitle());
        $this->assertEquals([], $page->getUniqueExternalLinks());
        $this->assertEquals([], $page->getUniqueInternalLinks());
        $this->assertEquals(['http://example.com/external1.png', 'http://example.com/external2.png'], $page->getUniqueImages());
        $this->assertEquals(0, $page->getWordCount());
    }

    public function testFetchWordCountExcludesScript(): void
    {
        Http::fake([
            '*' => Http::response('
                <html>
                    <head>
                        <script>One</script>
                    </head>
                    <body>
                        <div>
                            <div>Two</div>
                            Three
                        </div>
                        Four
                        <script>Five</script>
                    </body>
                </html>
            ', 200),
        ]);

        $page = new Page();
        $page->fetch('http://example.com');

        $this->assertEquals('http://example.com', $page->getUrl());
        $this->assertEquals('200', $page->getStatusCode());
        $this->assertEquals('', $page->getTitle());
        $this->assertEquals([], $page->getUniqueExternalLinks());
        $this->assertEquals([], $page->getUniqueInternalLinks());
        $this->assertEquals([], $page->getUniqueImages());
        $this->assertEquals(3, $page->getWordCount());
    }

    public function testFetchWith301StatusCode(): void
    {
        Http::fake([
            '*' => Http::response('<html></html>', 301),
        ]);

        $page = new Page();
        $page->fetch('http://example.com');

        $this->assertEquals('http://example.com', $page->getUrl());
        $this->assertEquals('301', $page->getStatusCode());
        $this->assertEquals('', $page->getTitle());
        $this->assertEquals([], $page->getUniqueExternalLinks());
        $this->assertEquals([], $page->getUniqueInternalLinks());
        $this->assertEquals([], $page->getUniqueImages());
        $this->assertEquals(0, $page->getWordCount());
    }

    public function testFetchWithTitle(): void
    {
        Http::fake([
            '*' => Http::response('
                <html>
                    <head>
                        <title>Test Title</title>
                    </head>
                </html>
            ', 200),
        ]);

        $page = new Page();
        $page->fetch('http://example.com');

        $this->assertEquals('http://example.com', $page->getUrl());
        $this->assertEquals('200', $page->getStatusCode());
        $this->assertEquals('Test Title', $page->getTitle());
        $this->assertEquals([], $page->getUniqueExternalLinks());
        $this->assertEquals([], $page->getUniqueInternalLinks());
        $this->assertEquals([], $page->getUniqueImages());
        $this->assertEquals(0, $page->getWordCount());
    }
}
