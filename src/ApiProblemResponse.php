<?php

/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\ApiProblem;

use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Response\InjectContentTypeTrait;

/**
 * Represents an ApiProblem response payload.
 */
class ApiProblemResponse extends Response
{
    use InjectContentTypeTrait;

    /**
     * @var ApiProblem
     */
    private $apiProblem;

    /**
     * @param ApiProblem $apiProblem
     */
    public function __construct(ApiProblem $apiProblem, array $headers = [])
    {
        $body = new Stream('php://temp', 'wb+');

        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR;
        $body->write(json_encode($apiProblem->toArray(), $jsonFlags));
        $body->rewind();

        $headers = $this->injectContentType(ApiProblem::CONTENT_TYPE, $headers);

        $this->apiProblem = $apiProblem;

        parent::__construct($body, $apiProblem->status, $headers);
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem() : ApiProblem
    {
        return $this->apiProblem;
    }

    /**
     * Retrieve the content.
     *
     * Serializes the composed ApiProblem instance to JSON.
     *
     * @deprecated Since 2.0
     * @return string
     */
    public function getContent()
    {
        return $this->getBody()->getContents();
    }

    /**
     * Override reason phrase handling.
     *
     * If no corresponding reason phrase is available for the current status
     * code, return "Unknown Error".
     *
     * @return string
     */
    public function getReasonPhrase() : string
    {
        return $this->getApiProblem()->title
            ?? parent::getReasonPhrase()
            ?? 'Unknown Error';
    }
}
