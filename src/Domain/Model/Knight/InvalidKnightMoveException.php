<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

use Chess\Domain\DomainException;

/**
 * Exception for invalid Knight's move.
 */
class InvalidKnightMoveException extends DomainException {}
