<?php

namespace Chess\Application\Knight;

use Chess\Application\ApplicationException;
use Chess\Application\InvalidParameterException;
use Chess\Domain\Model\Board\Board;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\BoardRepository;
use Chess\Domain\Model\Board\Box;
use Chess\Domain\Model\Board\InvalidBoardIdException;
use Chess\Domain\Model\Board\NotFoundBoardException;
use Chess\Domain\Model\Knight\GetMovesFromSourceToDestination;
use Chess\Domain\Model\Knight\InvalidKnightIdException;
use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Domain\Model\Knight\KnightRepository;
use Chess\Domain\Model\Knight\NotFoundKnightException;

/**
 * Application service to retrieve the minimum number of knight's moves.
 */
class GetMinimumNumberOfMovesService
{
    /**
     * @var KnightRepository
     */
    private $knightRepository;

    /**
     * @var BoardRepository
     */
    private $boardRepository;

    /**
     * GetMinimumNumberOfMovesService constructor.
     *
     * @param KnightRepository $knightRepository Knight repository.
     * @param BoardRepository  $boardRepository  Board repository.
     */
    public function __construct(KnightRepository $knightRepository, BoardRepository $boardRepository)
    {
        $this->knightRepository = $knightRepository;
        $this->boardRepository = $boardRepository;
    }

    /**
     * Gets the minimum number of Knight's moves from source to destination.
     *
     * @param GetMinimumNumberOfMovesRequest $request Request for service.
     *
     * @return KnightMovesDto
     *
     * @throws ApplicationException
     * @throws InvalidParameterException
     */
    public function execute(GetMinimumNumberOfMovesRequest $request): KnightMovesDto
    {
        try {
            $boardId        = $this->getBoardId($request->boardId);
            $knightId       = $this->getKnightId($request->knightId);
            $sourceBox      = new Box($request->source);
            $destinationBox = new Box($request->destination);
        } catch (InvalidBoardIdException|InvalidKnightIdException $exception) {
            throw new InvalidParameterException("Invalid knight id or board id", $exception->getCode(), $exception);
        }

        try {
            $getMovesService = new GetMovesFromSourceToDestination($this->knightRepository, $this->boardRepository);
            $getMovesService->execute($boardId, $knightId, $sourceBox, $destinationBox, []);
        } catch (NotFoundBoardException|NotFoundKnightException $exception) {
            throw new ApplicationException("Board or knight not found", $exception->getCode(), $exception);
        }

        echo "<h1>Solution: </h1><br>";
        
        return new KnightMovesDto(
            $knightId,
            $sourceBox,
            $destinationBox,
            $getMovesService->getMoves()
        );
    }

    /**
     * It just creates a new board entity and adds it into board repository.
     * This method just exists for pragmatic reasons. It wont exist in the real-world.
     *
     * @param null|string $boardId Board id.
     *
     * @return BoardId
     */
    private function getBoardId(?string $boardId): BoardId
    {
        if ($boardId !== null) {
            return new BoardId($boardId);
        }

        $board = $this->boardRepository->add(new Board($this->boardRepository->newIdentity()));
        return $board->id();
    }

    /**
     * It just creates a new knight entity and adds it into knight repository.
     * This method just exists for pragmatic reasons. It wont exist in the real-world.
     *
     * @param null|string $knightId Knight.
     *
     * @return KnightId
     */
    private function getKnightId(?string $knightId): KnightId
    {
        if ($knightId !== null) {
            return new KnightId($knightId);
        }

        $knight = $this->knightRepository->add(new Knight($this->knightRepository->newIdentity()));
        return $knight->id();
    }
}