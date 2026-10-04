<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

use Chess\Domain\DomainException;

/**
 * Exception for invalid board movements.
 */
class InvalidBoardMoveException extends DomainException {}
