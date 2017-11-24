<?php

namespace Chess\Domain\Model\Knight;

/**
 * VO for Knight's moves on the chess board.
 *
 * @package Chess\Domain\Model\Knight
 */
class Move
{
    public const X_PLUS_1 = 1;

    public const X_MINUS_1 = -1;

    public const X_PLUS_2 = 2;

    public const X_MINUS_2 = -2;

    public const Y_PLUS_1 = 1;

    public const Y_MINUS_1 = -1;

    public const Y_PLUS_2 = 2;

    public const Y_MINUS_2 = -2;

    /** @var int x-axis move */
    private $x;

    /** @var int y-axis move */
    private $y;

    /**
     * Move constructor.
     *
     * @param int $x x-axis movement.
     * @param int $y Y-axis movement.
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
     * Gets the Y-axis movement.
     *
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Gets Y-axis movement.
     *
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Asserts X-axis movement is valid.
     *
     * @param int $x X-axis move.
     *
     * @throws InvalidKnightXAxisMoveException
     */
    private function assertIsValidX(int $x)
    {
        if (!in_array($x, [self::X_MINUS_1, self::X_PLUS_1, self::X_MINUS_2, self::X_PLUS_2])) {
            throw new InvalidKnightXAxisMoveException("The move {$x} at X-axis is not valid for Knight");
        }
    }

    /**
     * Asserts X-axis movement is valid.
     *
     * @param int $y Y-axis move.
     *
     * @throws InvalidKnightYAxisMoveException
     */
    private function assertIsValidY(int $y)
    {
        if (!in_array($y, [self::Y_MINUS_1, self::Y_PLUS_1, self::Y_MINUS_2, self::Y_PLUS_2])) {
            throw new InvalidKnightYAxisMoveException("The move {$y} at Y-axis is not valid for Knight");
        }
    }

    /**
     * Asserts Knight's move is valid.
     *
     * @param int $x X-axis move.
     * @param int $y Y-axis move.
     *
     * @throws InvalidKnightMoveException
     */
    private function assertIsValidMove(int $x, int $y)
    {
        if (abs($x) === abs($y)) {
            throw new InvalidKnightMoveException("The move {$x}, {$y} is not valid for Knight");
        }
    }
}