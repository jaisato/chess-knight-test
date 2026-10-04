<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

use Chess\Domain\DomainException;

/**
 * Invalid board box.
 */
class InvalidBoardBoxException extends DomainException {}
