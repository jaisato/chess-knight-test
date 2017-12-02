<?php

namespace Chess\Domain\Model\Knight;

use Chess\Domain\Event\Knight\NewShortestPathFound;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Board\InvalidBoxException;
use Chess\Domain\Model\Board\NotFoundBoardException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * Optimal solution.
     *
     * @var array|null
     */
    private $optimalSolution;

    /**
     * List of visited position on current solution.
     *
     * @var array
     */
    private $visited = [];

    /**
     * GetNumberOfMovesFromSourceToDestination constructor.
     *
     * @param KnightRepository $knightRepository Knight repository.
     * @param BoardRepository $boardRepository Board repository.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        KnightRepository $knightRepository,
        BoardRepository $boardRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->knightRepository = $knightRepository;
        $this->boardRepository = $boardRepository;
        $this->eventDispatcher = $eventDispatcher;

        $this->optimalSolution = null;
        $this->visited = [];
    }

    /**
     * Calculate the number of knight moves from source box to destination box on chessboard.
     *
     * @param BoardId $boardId Board id.
     * @param KnightId $knightId Knight id.
     * @param Box $source Source board box.
     * @param Box $destination Destination board box.
     * @param array $solution Current solution.
     *
     * @return bool
     *
     * @throws NotFoundKnightException
     * @throws InvalidBoxException
     * @throws NotFoundBoardException
     */
    public function execute(BoardId $boardId, KnightId $knightId, Box $source, Box $destination, array $solution): bool
    {
        $board = $this->boardRepository->ofIdOrFail($boardId);
        $knight = $this->knightRepository->ofIdOrFail($knightId);

        $this->addToVisited($source);

        if (!$this->canBeOptimal($solution)) {
            return false;
        }

        if ($source->equalsTo($destination)) {
            $this->checkSolution($solution, $knightId, new Box(0), $destination);
            return true;
        }

        foreach (Knight::getMoves() as $knightMove) {
            $board->putAt($knight, $source);

            if ($board->checkCanMove($knight, $knightMove) && !$this->isVisited($source, $knightMove)) {
                $board->moveTo($knight, $knightMove);

                $currentPosition = $board->getPosition($knight);
                $solutionCopy = $this->addToSolution($solution, $currentPosition);
                $this->addToVisited($currentPosition);

                $this->execute($boardId, $knightId, $currentPosition, $destination, $solutionCopy);

                $this->removeFromVisited($currentPosition);
            }
        }

        return false;
    }

    /**
     * Gets moves of solution.
     *
     * @return array
     */
    public function getMoves()
    {
        return $this->optimalSolution;
    }

    /**
     * Checks if box position already visited at given path.
     *
     * @param Box $source Source position.
     * @param Move $knightMove Knight move.
     *
     * @return bool
     *
     * @throws InvalidBoxException
     */
    private function isVisited(Box $source, Move $knightMove)
    {
        $checkingBox = Box::createFromXYPosition(
            $source->getX() + $knightMove->getX(),
            $source->getY() + $knightMove->getY()
        );

        reset($this->visited);
        foreach ($this->visited as $box) {
            if ($checkingBox->equalsTo($box)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add position to list of visited.
     *
     * @param Box $position Visited position
     */
    private function addToVisited(Box $position)
    {
        $this->visited[] = $position;
    }

    /**
     * Removes position from list of visited.
     *
     * @param Box $position Position to remove from visited.
     */
    private function removeFromVisited(Box $position)
    {
        reset($this->visited);

        foreach ($this->visited as $index => $visitedBox) {
            if ($visitedBox->equalsTo($position)) {
                unset($this->visited[$index]);
            }
        }
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
        if ($this->canBeOptimal($solution)) {
            $this->optimalSolution = $solution;
            $this->publishNewSolution($knightId, $source, $destination, $solution);
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
        return $this->optimalSolution === null || count($this->optimalSolution) > count($solution);
    }

    /**
     * Publish event new shortest path solution.
     *
     * @param KnightId $knightId    Knight id.
     * @param Box      $source      Source position
     * @param Box      $destination Destination position
     * @param array    $solution    New optimal solution
     */
    private function publishNewSolution(KnightId $knightId, Box $source, Box $destination, array $solution)
    {
        $this->eventDispatcher->dispatch(
            NewShortestPathFound::class,
            new NewShortestPathFound($knightId, $source, $destination, $solution)
        );
    }
}