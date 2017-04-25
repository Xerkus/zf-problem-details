<?php

/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

declare(strict_types = 1);

namespace ZFTest\ProblemDetails;

use PHPUnit\Framework\TestCase;
use ZF\ProblemDetails\ProblemDetails;
use ZF\ProblemDetails\ProblemDetailsResponse;
use Psr\Http\Message\ResponseInterface;

/**
 *
 * @coversDefaultClass ZF\ProblemDetails\ProblemDetailsResponse
 * @covers ::<!public>
 */
class ProblemDetailsResponseTest extends TestCase
{
    public function testProblemDetailsResponseIsAnPsr7Response()
    {
        $response = new ProblemDetailsResponse(new ProblemDetails(400, 'Random error'));
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testProblemDetailsResponseSetsStatusCodeAndReasonPhrase()
    {
        $response = new ProblemDetailsResponse(new ProblemDetails(400, 'Random error'));
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertInternalType('string', $response->getReasonPhrase());
        $this->assertNotEmpty($response->getReasonPhrase());
        $this->assertEquals('bad request', strtolower($response->getReasonPhrase()));
    }

    public function testProblemDetailsResponseBodyIsSerializedProblemDetails()
    {
        $additional = [
            'foo' => fopen('php://memory', 'r')
        ];

        $expected = [
            'foo' => null,
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Bad Request',
            'status' => 400,
            'detail' => 'Random error',
        ];

        $apiProblem = new ProblemDetails(400, 'Random error', null, null, $additional);
        $response   = new ProblemDetailsResponse($apiProblem);
        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }

    public function testProblemDetailsResponseSetsContentTypeHeader()
    {
        $response = new ProblemDetailsResponse(new ProblemDetails(400, 'Random error'));
        $this->assertTrue($response->hasHeader('content-type'));
        $header = $response->getHeader('content-type');
        $this->count(1, $header);
        $this->assertEquals(ProblemDetails::CONTENT_TYPE, $header[0]);
    }

    public function testComposeProblemDetailsIsAccessible()
    {
        $apiProblem = new ProblemDetails(400, 'Random error');
        $response   = new ProblemDetailsResponse($apiProblem);
        $this->assertSame($apiProblem, $response->getProblemDetails());
    }

    /**
     * @group 14
     */
    public function testOverridesReasonPhraseIfStatusCodeIsUnknown()
    {
        $response = new ProblemDetailsResponse(new ProblemDetails(7, 'Random error'));
        $this->assertContains('Internal Server Error', $response->getReasonPhrase());
    }
}
