<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

use Chess\Domain\DomainException;

/**
 * A box outside the board.
 */
class InvalidBoxException extends DomainException {}
