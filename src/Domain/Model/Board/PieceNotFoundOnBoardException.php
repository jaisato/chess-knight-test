<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

use Chess\Domain\DomainException;

/**
 * The piece is not on the board.
 */
class PieceNotFoundOnBoardException extends DomainException {}
