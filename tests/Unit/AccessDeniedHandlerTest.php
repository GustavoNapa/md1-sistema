<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Request;
use App\Exceptions\Handler;

class AccessDeniedHandlerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_redirects_back_on_access_denied()
    {
        $handler = new Handler(app());
        $request = Request::create("/some-protected-route", "GET");
        $exception = new AccessDeniedHttpException();

        $response = $handler->render($request, $exception);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString(url()->previous(), $response->headers->get("Location"));
    }
}

