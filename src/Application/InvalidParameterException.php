<?php

declare(strict_types=1);

namespace Chess\Application;

/**
 * The caller passed a parameter the application cannot work with: bad input,
 * not an application failure.
 */
class InvalidParameterException extends ApplicationException {}
