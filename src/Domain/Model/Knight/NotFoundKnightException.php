<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

use Chess\Domain\DomainException;

/**
 * No knight with the given identifier.
 */
class NotFoundKnightException extends DomainException {}
