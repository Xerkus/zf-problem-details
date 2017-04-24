<?php

/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\ApiProblem;

use PHPUnit_Framework_TestCase as TestCase;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use Psr\Http\Message\ResponseInterface;

class ApiProblemResponseTest extends TestCase
{
    public function testApiProblemResponseIsAnPsr7Response()
    {
        $response = new ApiProblemResponse(new ApiProblem(400, 'Random error'));
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testApiProblemResponseSetsStatusCodeAndReasonPhrase()
    {
        $response = new ApiProblemResponse(new ApiProblem(400, 'Random error'));
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertInternalType('string', $response->getReasonPhrase());
        $this->assertNotEmpty($response->getReasonPhrase());
        $this->assertEquals('bad request', strtolower($response->getReasonPhrase()));
    }

    public function testApiProblemResponseBodyIsSerializedApiProblem()
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

        $apiProblem = new ApiProblem(400, 'Random error', null, null, $additional);
        $response   = new ApiProblemResponse($apiProblem);
        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }

    public function testApiProblemResponseSetsContentTypeHeader()
    {
        $response = new ApiProblemResponse(new ApiProblem(400, 'Random error'));
        $this->assertTrue($response->hasHeader('content-type'));
        $header = $response->getHeader('content-type');
        $this->count(1, $header);
        $this->assertEquals(ApiProblem::CONTENT_TYPE, $header[0]);
    }

    public function testComposeApiProblemIsAccessible()
    {
        $apiProblem = new ApiProblem(400, 'Random error');
        $response   = new ApiProblemResponse($apiProblem);
        $this->assertSame($apiProblem, $response->getApiProblem());
    }

    /**
     * @group 14
     */
    public function testOverridesReasonPhraseIfStatusCodeIsUnknown()
    {
        $response = new ApiProblemResponse(new ApiProblem(7, 'Random error'));
        $this->assertContains('Internal Server Error', $response->getReasonPhrase());
    }
}
