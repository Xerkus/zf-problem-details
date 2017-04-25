<?php

/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

declare(strict_types = 1);

namespace ZF\ProblemDetails;

use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Response\InjectContentTypeTrait;

/**
 * Represents an ProblemDetails response payload.
 */
class ProblemDetailsResponse extends Response
{
    use InjectContentTypeTrait;

    /**
     * @var ProblemDetails
     */
    private $apiProblem;

    /**
     * @param ProblemDetails $apiProblem
     */
    public function __construct(ProblemDetails $apiProblem, array $headers = [])
    {
        $body = new Stream('php://temp', 'wb+');

        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR;
        $body->write(json_encode($apiProblem->toArray(), $jsonFlags));
        $body->rewind();

        $headers = $this->injectContentType(ProblemDetails::CONTENT_TYPE, $headers);

        $this->apiProblem = $apiProblem;

        parent::__construct($body, $apiProblem->status, $headers);
    }

    /**
     * @return ProblemDetails
     */
    public function getProblemDetails() : ProblemDetails
    {
        return $this->apiProblem;
    }

    /**
     * Retrieve the content.
     *
     * Serializes the composed ProblemDetails instance to JSON.
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
        return $this->getProblemDetails()->title
            ?? parent::getReasonPhrase()
            ?? 'Unknown Error';
    }
}
