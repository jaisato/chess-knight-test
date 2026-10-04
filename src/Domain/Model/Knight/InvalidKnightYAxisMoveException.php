<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

use Chess\Domain\DomainException;

/**
 * Exception for invalid Knight's Y-axis move.
 */
class InvalidKnightYAxisMoveException extends DomainException {}
