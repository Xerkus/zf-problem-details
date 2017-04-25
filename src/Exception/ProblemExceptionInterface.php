<?php

/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

declare(strict_types = 1);

namespace ZF\ProblemDetails\Exception;

/**
 * Interface for exceptions that can provide additional API Problem details.
 */
interface ProblemExceptionInterface
{
    /**
     * @return null|array|\Traversable
     */
    public function getAdditionalDetails();

    /**
     * @return string
     */
    public function getType() : ?string;

    /**
     * @return string
     */
    public function getTitle() : ?string;
}
