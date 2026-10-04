<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

use Chess\Domain\DomainException;

/**
 * No board with the given identifier.
 */
class NotFoundBoardException extends DomainException {}
