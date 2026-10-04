<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Board;

/**
 * Chess board box.
 */
class Box
{
    /** X position. */
    private int $x;

    /** Y position. */
    private int $y;

    /**
     * @param int $boxValue box position, 0 to 63
     *
     * @throws InvalidBoxException
     */
    public function __construct(int $boxValue)
    {
        $this->setBox($boxValue);
    }

    /**
     * Sets box value.
     *
     * @throws InvalidBoxException
     */
    private function setBox(int $boxValue): void
    {
        $this->assertIsValidOneDimensionPosition($boxValue);

        $this->setXPosition($boxValue);
        $this->setYPosition($boxValue);
    }

    /**
     * Asserts box one dimension value is valid.
     *
     * @throws InvalidBoxException
     */
    private function assertIsValidOneDimensionPosition(int $boxValue): void
    {
        self::assertIsValid($boxValue % Board::NUMBER_OF_COLUMNS, intdiv($boxValue, Board::NUMBER_OF_COLUMNS));
    }

    /**
     * Creates box from 2-dimension position (x, y).
     *
     * @throws InvalidBoxException
     */
    public static function createFromXYPosition(int $xPosition, int $yPosition): self
    {
        self::assertIsValid($xPosition, $yPosition);

        return new self($xPosition + ($yPosition * Board::NUMBER_OF_COLUMNS));
    }

    /**
     * Asserts if position is valid for board box.
     *
     * @throws InvalidBoxException
     */
    public static function assertIsValid(int $xPosition, int $yPosition): void
    {
        if (!self::assertXPositionIsValid($xPosition) || !self::assertYPositionIsValid($yPosition)) {
            throw new InvalidBoxException('X-axis value must be between 0 and ' . (Board::NUMBER_OF_COLUMNS - 1) . '. Y-axis value must be between 0 and ' . (Board::NUMBER_OF_ROWS - 1));
        }
    }

    /**
     * Gets box X-axis position.
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Gets box Y-axis position.
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Gets global One Dimension value.
     */
    public function getOneDimensionValue(): int
    {
        return $this->getX() + ($this->getY() * Board::NUMBER_OF_COLUMNS);
    }

    /**
     * Check if box is equal to another box.
     */
    public function equalsTo(self $aBox): bool
    {
        return $this->x === $aBox->getX() && $this->y === $aBox->getY();
    }

    /**
     * Sets x-axis position from box value.
     */
    private function setXPosition(int $boxValue): void
    {
        $this->x = $boxValue % Board::NUMBER_OF_COLUMNS;
    }

    /**
     * Sets y-axis position from box value.
     */
    private function setYPosition(int $boxValue): void
    {
        $this->y = intdiv($boxValue, Board::NUMBER_OF_COLUMNS);
    }

    /**
     * Asserts if X-axis position is valid.
     */
    public static function assertXPositionIsValid(int $xValue): bool
    {
        return $xValue >= 0 && $xValue < Board::NUMBER_OF_COLUMNS;
    }

    /**
     * Asserts if Y-axis position is valid.
     */
    public static function assertYPositionIsValid(int $yValue): bool
    {
        return $yValue >= 0 && $yValue < Board::NUMBER_OF_ROWS;
    }
}
