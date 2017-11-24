<?php

namespace Chess\Domain\Model\Knight;

/**
 * Knight entity.
 *
 * @package Chess\Persistence\Knight\Knight
 */
class Knight
{
    /**
     * @var KnightId
     */
    private $knightId;

    /**
     * Knight constructor.
     *
     * @param KnightId $knightId Knight id.
     */
    public function __construct(KnightId $knightId)
    {
        $this->knightId = $knightId;
    }

    /**
     * Gets Knight's valid moves.
     *
     * @return array|Move[]
     */
    public static function getMoves()
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
     *
     * @return KnightId
     */
    public function id()
    {
        return $this->knightId;
    }
}