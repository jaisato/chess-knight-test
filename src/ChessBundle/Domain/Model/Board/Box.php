<?php

namespace Chess\Domain\Model\Board;

/**
 * Chess board box.
 *
 * @package Chess\Persistence\Knight\Board
 */
class Box
{
    /**
     * @var int X position.
     */
    private $x;

    /**
     * @var int Y position.
     */
    private $y;

    /**
     * Box constructor.
     *
     * @param int $boxValue Box position.
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
     * @param int $boxValue Box value.
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
     * @param int $boxValue Box value.
     *
     * @throws InvalidBoxException
     */
    private function assertIsValidOneDimensionPosition(int $boxValue)
    {
        self::assertIsValid($boxValue % Board::NUMBER_OF_COLUMNS, intdiv($boxValue, Board::NUMBER_OF_COLUMNS));
    }

    /**
     * Creates box from 2-dimension position (x, y).
     *
     * @param int $xPosition X-axis position.
     * @param int $yPosition Y-axis position.
     *
     * @return Box
     * @throws InvalidBoxException
     */
    public static function createFromXYPosition(int $xPosition, int $yPosition)
    {
        self::assertIsValid($xPosition, $yPosition);
        return new self($xPosition + ($yPosition * Board::NUMBER_OF_COLUMNS));
    }

    /**
     * Asserts if position is valid for board box.
     *
     * @param int $xPosition X-axis position.
     * @param int $yPosition Y-axis position.
     *
     * @throws InvalidBoxException
     */
    public static function assertIsValid(int $xPosition, int $yPosition): void
    {
        if (!self::assertXPositionIsValid($xPosition) || !self::assertYPositionIsValid($yPosition)) {
            throw new InvalidBoxException(
                'X-axis value must be between 0 and ' . Board::NUMBER_OF_ROWS .
                '. Y-axis value must be between 0 and ' . Board::NUMBER_OF_COLUMNS
            );
        }
    }

    /**
     * Gets box X-axis position.
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Gets box Y-axis position
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Gets global One Dimension value.
     *
     * @return int
     */
    public function getOneDimensionValue()
    {
        return $this->getX() + ($this->getY() * Board::NUMBER_OF_COLUMNS);
    }

    /**
     * Check if box is equal to another box.
     *
     * @param Box $aBox Another box.
     *
     * @return bool
     */
    public function equalsTo(Box $aBox)
    {
        return $this->x == $aBox->getX() && $this->y == $aBox->getY();
    }

    /**
     * Sets x-axis position from box value.
     *
     * @param int $boxValue Box value.
     */
    private function setXPosition(int $boxValue): void
    {
        $this->x = $boxValue % Board::NUMBER_OF_COLUMNS;
    }

    /**
     * Sets y-axis position from box value.
     *
     * @param int $boxValue Box value.
     */
    private function setYPosition(int $boxValue): void
    {
        $this->y = intdiv($boxValue, Board::NUMBER_OF_COLUMNS);
    }

    /**
     * Asserts if X-axis position is valid.
     *
     * @param $xValue X-axis position.
     *
     * @return bool
     */
    public static function assertXPositionIsValid(int $xValue): bool
    {
        return $xValue >= 0 && $xValue < Board::NUMBER_OF_COLUMNS;
    }

    /**
     * Asserts if Y-axis position is valid.
     *
     * @param $yValue Y-axis position.
     *
     * @return bool
     */
    public static function assertYPositionIsValid(int $yValue): bool
    {
        return $yValue >= 0 && $yValue < Board::NUMBER_OF_ROWS;
    }
}