<?php

declare(strict_types=1);

namespace Chess\Infrastructure\UI\Web\Controller;

use Chess\Application\ApplicationException;
use Chess\Application\InvalidParameterException;
use Chess\Application\Knight\GetMinimumNumberOfMovesRequest;
use Chess\Application\Knight\GetMinimumNumberOfMovesService;
use Chess\Application\NotFoundException;
use Chess\Infrastructure\InfrastructureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

/**
 * Web entry point: the shortest path of a knight between two squares.
 */
#[AsController]
class KnightController
{
    public function __construct(
        private readonly GetMinimumNumberOfMovesService $getMinimumNumberOfMovesService,
        private readonly Environment $twig,
    ) {}

    /**
     * Gets the minimum number of moves from source to destination by knight piece.
     *
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws InfrastructureException
     */
    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function getNumberOfMoves(Request $request): Response
    {
        $boardId = $this->getOptionalStringQueryParameter($request, 'boardId');
        $knightId = $this->getOptionalStringQueryParameter($request, 'knightId');
        // A value that is not an integer (`?source=abc`) makes getInt() throw a
        // BadRequestException, which the kernel answers with a 400.
        $source = $request->query->getInt('source', 0);
        $destination = $request->query->getInt('destination', 63);

        try {
            $knightMovesDto = $this->getMinimumNumberOfMovesService->execute(
                new GetMinimumNumberOfMovesRequest($boardId, $knightId, $source, $destination),
            );
        } catch (InvalidParameterException $exception) {
            // A client error: answer 400 instead of letting it surface as a 500.
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        } catch (NotFoundException $exception) {
            // An id the client supplied that names nothing: 404, not 500.
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        } catch (ApplicationException $exception) {
            throw new InfrastructureException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return new Response($this->twig->render('knight_moves_solution.html.twig', [
            'solution' => $knightMovesDto,
        ]));
    }

    /**
     * Reads an optional string from the query string.
     *
     * `?boardId[]=x` is malformed client input, never a 500: InputBag::getString()
     * refuses a non-scalar value with a BadRequestException, which the kernel
     * answers with a 400.
     */
    private function getOptionalStringQueryParameter(Request $request, string $name): ?string
    {
        return $request->query->has($name) ? $request->query->getString($name) : null;
    }
}
