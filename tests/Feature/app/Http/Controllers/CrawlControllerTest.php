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

    public function testResults(): void
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
}
