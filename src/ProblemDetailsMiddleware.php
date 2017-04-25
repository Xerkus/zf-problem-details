<?php

/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

declare(strict_types = 1);

namespace ZF\ProblemDetails;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProblemDetailsMiddleware implements MiddlewareInterface
{
    private $showStackTrace;

    public function __construct(bool $showStackTrace = false)
    {
        $this->showStackTrace = $showStackTrace;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate) : ResponseInterface
    {
        try {
            $response = $delegate->process($request);
            if (! $response instanceof ProblemDetailsResponse) {
                return $response;
            }
            $apiProblem = $response->getProblemDetails();
        } catch (\Throwable $e) {
            $apiProblem = new ProblemDetails(500, $e);
        }
        return $this->handleProblemDetails($apiProblem);
    }

    private function handleProblemDetails(ProblemDetails $apiProblem) : ResponseInterface
    {
        if ($this->showStackTrace) {
            // @TODO change to with*()
            $apiProblem->setDetailIncludesStackTrace(true);
        }
        // todo handle content negotiation
        return new ProblemDetailsResponse($apiProblem);
    }
}
