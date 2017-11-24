<?php

namespace Chess\Domain\Model\Board;

use Chess\Domain\DomainException;

/**
 * Exception for invalid board movements.
 *
 * @package Chess\Domain\Model\Board
 */
class InvalidBoardMoveException extends DomainException
{
}