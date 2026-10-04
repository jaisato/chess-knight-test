<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

/**
 * VO for Knight's moves on the chess board.
 */
class Move
{
    public const int X_PLUS_1 = 1;

    public const int X_MINUS_1 = -1;

    public const int X_PLUS_2 = 2;

    public const int X_MINUS_2 = -2;

    public const int Y_PLUS_1 = 1;

    public const int Y_MINUS_1 = -1;

    public const int Y_PLUS_2 = 2;

    public const int Y_MINUS_2 = -2;

    /** X-axis move. */
    private readonly int $x;

    /** Y-axis move. */
    private readonly int $y;

    /**
     * @param int $x X-axis movement
     * @param int $y Y-axis movement
     *
     * @throws InvalidKnightXAxisMoveException
     * @throws InvalidKnightYAxisMoveException
     * @throws InvalidKnightMoveException
     */
    public function __construct(int $x, int $y)
    {
        $this->assertIsValidX($x);
        $this->assertIsValidY($y);
        $this->assertIsValidMove($x, $y);

        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Gets the X-axis movement.
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Gets Y-axis movement.
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Asserts X-axis movement is valid.
     *
     * @throws InvalidKnightXAxisMoveException
     */
    private function assertIsValidX(int $x): void
    {
        if (!\in_array($x, [self::X_MINUS_1, self::X_PLUS_1, self::X_MINUS_2, self::X_PLUS_2], true)) {
            throw new InvalidKnightXAxisMoveException("The move {$x} at X-axis is not valid for Knight");
        }
    }

    /**
     * Asserts Y-axis movement is valid.
     *
     * @throws InvalidKnightYAxisMoveException
     */
    private function assertIsValidY(int $y): void
    {
        if (!\in_array($y, [self::Y_MINUS_1, self::Y_PLUS_1, self::Y_MINUS_2, self::Y_PLUS_2], true)) {
            throw new InvalidKnightYAxisMoveException("The move {$y} at Y-axis is not valid for Knight");
        }
    }

    /**
     * Asserts Knight's move is valid.
     *
     * @throws InvalidKnightMoveException
     */
    private function assertIsValidMove(int $x, int $y): void
    {
        if (abs($x) === abs($y)) {
            throw new InvalidKnightMoveException("The move {$x}, {$y} is not valid for Knight");
        }
    }
}
