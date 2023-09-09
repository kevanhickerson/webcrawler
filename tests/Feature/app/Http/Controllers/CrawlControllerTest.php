<?php

namespace Tests\Feature\App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrawlControllerTest extends TestCase
{
    public function testSearch(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Page: ');
        $response->assertSee('<input type="text" id="page" name="page" value="agencyanalytics.com">', false);
        $response->assertSee('<input type="submit">', false);
    }

    public function testResultsWithNoInternalLinks(): void
    {
        Http::fake([
            '*' => Http::response('
                <html>
                    <head>
                        <title>feature test</title>
                        <script>One</script>
                    </head>
                    <body>
                        <div>
                            <div>Two</div>
                            Three
                        </div>
                        Four
                        <script>Five</script>
                        <img src="http://example.com/external1.png">
                        <img src="http://example.com/external1.png">
                        <img src="http://example.com/external2.png">
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external2">E2</a>
                    </body>
                </html>
            ', 200),
        ]);
        $response = $this->get('/results?page=example.com');

        $response->assertStatus(200);
        $response->assertSee('Average Title Length: 12');
        $response->assertSee('Average Word Count: 6');
        $response->assertSee('Number Of Pages Crawled: 1');
        $response->assertSee('Number Of Unique Images: 2');
        $response->assertSee('Number Of Unique Internal Links: 0');
        $response->assertSee('Number Of Unique External Links: 2');
    }

    public function testResultsWithOneInternalLink(): void
    {
        Http::fake([
            'example.com' => Http::response('
                <html>
                    <head>
                        <title>feature test</title>
                        <script>One</script>
                    </head>
                    <body>
                        <div>
                            <div>Two</div>
                            Three
                        </div>
                        Four
                        <script>Five</script>
                        <img src="http://example.com/external1.png">
                        <img src="http://example.com/external1.png">
                        <img src="http://example.com/external2.png">
                        <a href="/internal1">I1</a>
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external2">E2</a>
                    </body>
                </html>
            ', 200),
            'example.com/internal1' => Http::response('
                <html>
                    <head>
                        <title>internal feature test</title>
                        <script>One</script>
                    </head>
                    <body>
                        <div>
                            <div>Two</div>
                            Three
                        </div>
                        Four
                        <script>Five</script>
                        <img src="http://example.com/external1.png">
                        <img src="http://example.com/external1.png">
                        <img src="http://example.com/external2.png">
                        <img src="http://example.com/external3.png">
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external1">E1</a>
                        <a href="http://example.com/external2">E2</a>
                        <a href="http://example.com/external3">E2</a>
                    </body>
                </html>
            ', 200),
        ]);
        $response = $this->get('/results?page=example.com');

        $response->assertStatus(200);
        $response->assertSee('Average Title Length: 16.5');
        $response->assertSee('Average Word Count: 7');
        $response->assertSee('Number Of Pages Crawled: 2');
        $response->assertSee('Number Of Unique Images: 3');
        $response->assertSee('Number Of Unique Internal Links: 1');
        $response->assertSee('Number Of Unique External Links: 3');
    }
}
