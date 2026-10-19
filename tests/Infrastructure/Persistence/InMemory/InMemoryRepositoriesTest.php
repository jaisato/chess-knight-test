<?php

declare(strict_types=1);

namespace Chess\Tests\Infrastructure\Persistence\InMemory;

use Chess\Domain\Model\Board\Board;
use Chess\Domain\Model\Board\BoardId;
use Chess\Domain\Model\Board\NotFoundBoardException;
use Chess\Domain\Model\Knight\Knight;
use Chess\Domain\Model\Knight\KnightId;
use Chess\Domain\Model\Knight\NotFoundKnightException;
use Chess\Infrastructure\Persistence\InMemory\Board\InMemoryBoardRepository;
use Chess\Infrastructure\Persistence\InMemory\Knight\InMemoryKnightRepository;
use PHPUnit\Framework\TestCase;

/**
 * The in-memory repositories behave as a collection with one entity per identity.
 */
final class InMemoryRepositoriesTest extends TestCase
{
    public function testAddingAnIdentityTwiceKeepsAndReturnsTheTrackedBoard(): void
    {
        $repository = new InMemoryBoardRepository();
        $tracked = $repository->add(new Board(new BoardId('board')));

        $returned = $repository->add(new Board(new BoardId('board')));

        static::assertSame($tracked, $returned);
        static::assertSame($tracked, $repository->ofId(new BoardId('board')));
    }

    public function testAddingAnIdentityTwiceKeepsAndReturnsTheTrackedKnight(): void
    {
        $repository = new InMemoryKnightRepository();
        $tracked = $repository->add(new Knight(new KnightId('knight')));

        $returned = $repository->add(new Knight(new KnightId('knight')));

        static::assertSame($tracked, $returned);
        static::assertSame($tracked, $repository->ofId(new KnightId('knight')));
    }

    public function testRemovedBoardIsNoLongerFound(): void
    {
        $repository = new InMemoryBoardRepository();
        $repository->add(new Board(new BoardId('1')));
        $repository->add(new Board(new BoardId('2')));

        $repository->remove(new Board(new BoardId('1')));

        static::assertNull($repository->ofId(new BoardId('1')));
        static::assertNotNull($repository->ofId(new BoardId('2')));
        $this->expectException(NotFoundBoardException::class);
        $repository->ofIdOrFail(new BoardId('1'));
    }

    public function testRemovedKnightIsNoLongerFound(): void
    {
        $repository = new InMemoryKnightRepository();
        $knight = $repository->add(new Knight($repository->newIdentity()));

        $repository->remove($knight);

        $this->expectException(NotFoundKnightException::class);
        $repository->ofIdOrFail($knight->id());
    }
}
