<?php

namespace Chess\Domain\Model\Knight;

use Chess\Application\Knight\KnightMovesDto;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\Box;

/**
 * Knight's domain service to get the valid moves from source to destination.
 *
 * @package Chess\Persistence\Knight\Knight
 */
class GetMovesFromSourceToDestination
{
    /** @var KnightRepository */
    private $knightRepository;

    /** @var BoardRepository */
    private $boardRepository;

    /** @var array|null */
    private $solution;

    /**
     * GetNumberOfMovesFromSourceToDestination constructor.
     *
     * @param KnightRepository $knightRepository Knight repository.
     * @param BoardRepository  $boardRepository  Board repository.
     */
    public function __construct(KnightRepository $knightRepository, BoardRepository $boardRepository)
    {
        $this->knightRepository = $knightRepository;
        $this->boardRepository = $boardRepository;
        $this->solution = null;
    }

    /**
     * Calculate the number of knight moves from source box to destination box on chessboard.
     *
     * @param BoardId  $boardId     Board id.
     * @param KnightId $knightId    Knight id.
     * @param Box      $source      Source board box.
     * @param Box      $destination Destination board box.
     * @param array    $solution    Current solution.
     *
     * @return bool
     */
    public function execute(BoardId $boardId, KnightId $knightId, Box $source, Box $destination, array $solution): bool
    {
        $board = $this->boardRepository->ofIdOrFail($boardId);
        $knight = $this->knightRepository->ofIdOrFail($knightId);

        $board->putAt($knight, $source);
        $solution = $this->addToSolution($solution, $source);

        if (!$this->canBeOptimal($solution)) {
            return false;
        }

        if ($source->equalsTo($destination)) {
            $this->checkSolution($solution, $knightId, new Box(0), $destination);
            return true;
        }

        foreach (Knight::getMoves() as $knightMove) {
            if ($board->checkCanMove($knight, $knightMove) && !$this->visited($solution, $source, $knightMove)) {
                $board->moveTo($knight, $knightMove);
                $currentPosition = $board->getPosition($knight);
                $solutionCopy = $solution;
                $this->execute($boardId, $knightId, $currentPosition, $destination, $solutionCopy);
            }

            $board->putAt($knight, $source);
        }

        $this->removeFromSolution($solution, $source);
        return false;
    }

    /**
     * Gets moves of solution.
     *
     * @return array
     */
    public function getMoves()
    {
        return $this->solution;
    }

    /**
     * Checks if box position already visited at given path.
     *
     * @param array $solution   Current solution.
     * @param Box   $source     Source position.
     * @param Move  $knightMove Knight move.
     *
     * @return bool
     */
    private function visited(array $solution, Box $source, Move $knightMove)
    {
        $checkingBox = Box::createFromXYPosition(
            $source->getX() + $knightMove->getX(),
            $source->getY() + $knightMove->getY()
        );

        foreach ($solution as $box) {
            if ($checkingBox->equalsTo($box)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes position from current solution.
     *
     * @param array $solution Current solution.
     * @param Box   $position Position to remove.
     *
     * @return array
     */
    private function removeFromSolution(array $solution, Box $position)
    {
        foreach ($solution as $index => $box) {
            if ($position->equalsTo($box)) {
                unset($solution[$index]);
            }
        }

        return $solution;
    }

    /**
     * Adds box to current solution.
     *
     * @param array $solution Current solution.
     * @param Box   $source   Source box.
     *
     * @return array
     */
    private function addToSolution(array $solution, Box $source): array
    {
        $solution[] = $source;

        return $solution;
    }

    /**
     * Checks if it's the current optimal solution.
     *
     * @param array    $solution    Current solution.
     * @param KnightId $knightId    Knight id.
     * @param Box      $source      Source position.
     * @param Box      $destination Destination position.
     */
    private function checkSolution(array $solution, KnightId $knightId, Box $source, Box $destination)
    {
        if ($this->solution === null || count($this->solution) > $solution) {
            $this->solution = $solution;
            $this->printSolution($knightId, $source, $destination, $solution);
        }
    }

    /**
     * Check if current solution still is optimal.
     *
     * @param array $solution Current solution.
     *
     * @return bool
     */
    private function canBeOptimal(array $solution)
    {
        return $this->solution === null || count($this->solution) > count($solution);
    }

    /**
     * TODO: it shows every found solution (it's provisional because algorithm takes a long time).
     * Prints the new current optimal solution.
     *
     * @param KnightId $knightId    Knight id.
     * @param Box      $source      Source position
     * @param Box      $destination Destination position
     * @param array    $solution    New optimal solution
     */
    private function printSolution(KnightId $knightId, Box $source, Box $destination, array $solution)
    {
        $output = fopen('php://output', 'w');
        if (ob_get_level() == 0)
            ob_start();

        fwrite($output, (new KnightMovesDto($knightId, $source, $destination, $solution))->serialize() . "<br>");

        ob_flush();
        flush();

        pclose($output);
        ob_end_flush();
    }
}