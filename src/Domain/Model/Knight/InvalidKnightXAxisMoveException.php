<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

use Chess\Domain\DomainException;

/**
 * Exception for invalid Knight's X-axis move.
 */
class InvalidKnightXAxisMoveException extends DomainException {}
