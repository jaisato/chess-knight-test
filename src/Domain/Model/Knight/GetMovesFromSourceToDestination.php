<?php

declare(strict_types=1);

namespace Chess\Domain\Model\Knight;

use Chess\Domain\Event\Knight\NewShortestPathFound;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Board\InvalidBoxException;
use Chess\Domain\Model\Board\NotFoundBoardException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Knight's domain service to get the valid moves from source to destination.
 */
class GetMovesFromSourceToDestination
{
    /**
     * Optimal solution.
     *
     * @var list<Box>|null
     */
    private ?array $optimalSolution = null;

    /**
     * Squares already reached by the current search, by one-dimension value.
     *
     * @var array<int, true>
     */
    private array $visited = [];

    public function __construct(
        private readonly KnightRepository $knightRepository,
        private readonly BoardRepository $boardRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * Calculate the number of knight moves from source box to destination box on chessboard.
     *
     * @param BoardId   $boardId     board id
     * @param KnightId  $knightId    knight id
     * @param Box       $source      source board box
     * @param Box       $destination destination board box
     * @param list<Box> $solution    current solution
     *
     * @throws NotFoundKnightException
     * @throws InvalidBoxException
     * @throws NotFoundBoardException
     */
    public function execute(BoardId $boardId, KnightId $knightId, Box $source, Box $destination, array $solution): bool
    {
        $board = $this->boardRepository->ofIdOrFail($boardId);
        $knight = $this->knightRepository->ofIdOrFail($knightId);

        // Every call starts from a clean slate. The two fields below are the
        // whole state of the search, and leaving a previous run's optimum in
        // place would let it prune - or be returned as - the answer to a
        // different question.
        $this->optimalSolution = null;
        $this->visited = [];

        if ($source->equalsTo($destination)) {
            $this->optimalSolution = $solution;
            $this->publishNewSolution($knightId, $source, $destination, $solution);

            return true;
        }

        // Breadth-first, not depth-first.
        //
        // The previous search walked every path the knight could take without
        // repeating a square, keeping the shortest it had seen. On an 8x8 board
        // that is a walk over self-avoiding paths - the count of those runs into
        // the billions - and the "is this still shorter than the best so far"
        // pruning only starts biting once a first solution exists, which depth
        // first order reaches by wandering dozens of moves deep. The published
        // answer was right; getting to it was not something you would wait for.
        //
        // Breadth-first visits squares in order of distance from the source, so
        // the first time the destination comes off a move it is by definition on
        // a shortest path: the search stops there, having touched each of the 64
        // squares at most once. Marking a square on the way in rather than on
        // the way out is what keeps it at most once - a square queued twice
        // would be expanded twice, at the same depth, for the same answer.
        $knightMoves = Knight::getMoves();

        /** @var list<array{box: Box, path: list<Box>}> $queue */
        $queue = [['box' => $source, 'path' => $solution]];
        $this->visited[$source->getOneDimensionValue()] = true;

        // An index rather than array_shift(): shifting reindexes the whole
        // queue on every dequeue.
        for ($head = 0; $head < \count($queue); ++$head) {
            ['box' => $currentBox, 'path' => $currentPath] = $queue[$head];

            foreach ($knightMoves as $knightMove) {
                $board->putAt($knight, $currentBox);

                // moveTo() answers false, and leaves the knight where it was,
                // for a move that would take it off the board.
                if (!$board->moveTo($knight, $knightMove)) {
                    continue;
                }

                $nextBox = $board->getPosition($knight);
                \assert(null !== $nextBox, 'The knight has just moved, so it is on the board');
                $nextKey = $nextBox->getOneDimensionValue();

                if (isset($this->visited[$nextKey])) {
                    continue;
                }

                $this->visited[$nextKey] = true;
                $nextPath = [...$currentPath, $nextBox];

                if ($nextBox->equalsTo($destination)) {
                    $this->optimalSolution = $nextPath;
                    // The real source, not new Box(0): the event carries the
                    // endpoints of the path it announces, and the old call
                    // reported square 0 for every search.
                    $this->publishNewSolution($knightId, $source, $destination, $nextPath);

                    return true;
                }

                $queue[] = ['box' => $nextBox, 'path' => $nextPath];
            }
        }

        // Unreachable on a standard board - every square is reachable from every
        // other - but a board whose dimensions made one unreachable would land
        // here rather than looping.
        return false;
    }

    /**
     * Gets moves of solution.
     *
     * @return list<Box>|null null until a search finds a path
     */
    public function getMoves(): ?array
    {
        return $this->optimalSolution;
    }

    /**
     * Publish event new shortest path solution.
     *
     * @param list<Box> $solution New optimal solution
     */
    private function publishNewSolution(KnightId $knightId, Box $source, Box $destination, array $solution): void
    {
        $this->eventDispatcher->dispatch(new NewShortestPathFound($knightId, $source, $destination, $solution));
    }
}
