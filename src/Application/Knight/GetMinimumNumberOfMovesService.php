<?php

declare(strict_types=1);

namespace Chess\Application\Knight;

use Chess\Application\ApplicationException;
use Chess\Application\InvalidParameterException;
use Chess\Application\NotFoundException;
use Chess\Domain\Model\Board\Board;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Board\InvalidBoardIdException;
use Chess\Domain\Model\Board\InvalidBoxException;
use Chess\Domain\Model\Board\NotFoundBoardException;
use Chess\Domain\Model\Knight\GetMovesFromSourceToDestination;
use Chess\Domain\Model\Knight\InvalidKnightIdException;
use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Domain\Model\Knight\KnightRepository;
use Chess\Domain\Model\Knight\NotFoundKnightException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Application service to retrieve the minimum number of knight's moves.
 */
class GetMinimumNumberOfMovesService
{
    public function __construct(
        private readonly KnightRepository $knightRepository,
        private readonly BoardRepository $boardRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * Gets the minimum number of Knight's moves from source to destination.
     *
     * @throws ApplicationException
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function execute(GetMinimumNumberOfMovesRequest $request): KnightMovesDto
    {
        try {
            $boardId = $this->getBoardId($request->boardId);
            $knightId = $this->getKnightId($request->knightId);
            $sourceBox = new Box($request->source);
            $destinationBox = new Box($request->destination);
        } catch (InvalidBoardIdException|InvalidKnightIdException $exception) {
            throw new InvalidParameterException('Invalid knight id or board id', $exception->getCode(), $exception);
        } catch (InvalidBoxException $exception) {
            // `source` and `destination` come straight from the query string:
            // an off-board square is bad input, not an application failure,
            // and it used to escape this method as a raw domain exception.
            throw new InvalidParameterException('Invalid source or destination box', $exception->getCode(), $exception);
        }

        $getMovesService = new GetMovesFromSourceToDestination(
            $this->knightRepository,
            $this->boardRepository,
            $this->eventDispatcher,
        );

        try {
            $getMovesService->execute($boardId, $knightId, $sourceBox, $destinationBox, []);
        } catch (NotFoundBoardException|NotFoundKnightException $exception) {
            // Only reachable when the caller passed a boardId/knightId of its
            // own: a missing entity it named is a "not found", not a failure.
            throw new NotFoundException('Board or knight not found', $exception->getCode(), $exception);
        } catch (InvalidBoxException $exception) {
            throw new ApplicationException('Board box is invalid', $exception->getCode(), $exception);
        }

        // Every square of a standard board is reachable from every other, so a
        // search without a solution means the domain broke its own invariant.
        $moves = $getMovesService->getMoves() ?? throw new ApplicationException('No path found from source to destination');

        return new KnightMovesDto($knightId, $sourceBox, $destinationBox, $moves);
    }

    /**
     * It just creates a new board entity and adds it into board repository.
     * This method just exists for pragmatic reasons. It won't exist in the real world.
     *
     * @throws InvalidBoardIdException
     */
    private function getBoardId(?string $boardId): BoardId
    {
        if (null !== $boardId) {
            return new BoardId($boardId);
        }

        return $this->boardRepository->add(new Board($this->boardRepository->newIdentity()))->id();
    }

    /**
     * It just creates a new knight entity and adds it into knight repository.
     * This method just exists for pragmatic reasons. It won't exist in the real world.
     *
     * @throws InvalidKnightIdException
     */
    private function getKnightId(?string $knightId): KnightId
    {
        if (null !== $knightId) {
            return new KnightId($knightId);
        }

        return $this->knightRepository->add(new Knight($this->knightRepository->newIdentity()))->id();
    }
}
