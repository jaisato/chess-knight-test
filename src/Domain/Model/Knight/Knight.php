<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

/**
 * Knight entity.
 */
class Knight
{
    public function __construct(private readonly KnightId $knightId) {}

    /**
     * Gets Knight's valid moves.
     *
     * @return list<Move>
     */
    public static function getMoves(): array
    {
        return [
            new Move(Move::X_PLUS_1, Move::Y_PLUS_2),    //  1,  2
            new Move(Move::X_PLUS_1, Move::Y_MINUS_2),   //  1, -2
            new Move(Move::X_MINUS_1, Move::Y_PLUS_2),   // -1,  2
            new Move(Move::X_MINUS_1, Move::Y_MINUS_2),  // -1, -2
            new Move(Move::X_PLUS_2, Move::Y_PLUS_1),    //  2,  1
            new Move(Move::X_PLUS_2, Move::Y_MINUS_1),   //  2, -1
            new Move(Move::X_MINUS_2, Move::Y_PLUS_1),   // -2,  1
            new Move(Move::X_MINUS_2, Move::Y_MINUS_1),  // -2, -1
        ];
    }

    /**
     * Gets knight id.
     */
    public function id(): KnightId
    {
        return $this->knightId;
    }
}
