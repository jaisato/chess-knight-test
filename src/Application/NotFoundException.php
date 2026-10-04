<?php

declare(strict_types=1);

namespace Chess\Application;

/**
 * A resource named by the caller (a board or a knight id) does not exist.
 *
 * It is the caller's mistake rather than an application failure, so it gets
 * its own type: the UI layer can answer it as "not found" instead of folding
 * it into a generic server error.
 */
class NotFoundException extends ApplicationException {}
