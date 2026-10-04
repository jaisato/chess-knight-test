<?php

namespace Chess\Infrastructure\UI\Web\Controller;

use Chess\Application\ApplicationException;
use Chess\Application\InvalidParameterException;
use Chess\Application\NotFoundException;
use Chess\Application\Knight\GetMinimumNumberOfMovesRequest;
use Chess\Application\Knight\GetMinimumNumberOfMovesService;
use Chess\Infrastructure\InfrastructureException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class KnightController
 *
 * @package Chess\Controller
 */
class KnightController
{
    /**
     * @var GetMinimumNumberOfMovesService
     */
    private $getMinimumNumberOfMovesService;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * KnightController constructor.
     *
     * @param GetMinimumNumberOfMovesService $getMinimumNumberOfMovesService Get minimum number of Knight's moves service.
     * @param EngineInterface                $templateEngine                 Template engine interface.
     */
    public function __construct(
        GetMinimumNumberOfMovesService $getMinimumNumberOfMovesService,
        EngineInterface $templateEngine
    ) {
        $this->getMinimumNumberOfMovesService = $getMinimumNumberOfMovesService;
        $this->templateEngine = $templateEngine;
    }

    /**
     * Gets the minimum number of moves from source to destination by knight piece.
     *
     * @Route("/", name="homepage")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws InfrastructureException
     */
    public function getNumberOfMoves(Request $request)
    {
        $boardId = $this->getOptionalStringQueryParameter($request, 'boardId');
        $knightId = $this->getOptionalStringQueryParameter($request, 'knightId');
        $source = $request->query->getInt('source', 0);
        $destination = $request->query->getInt('destination', 63);

        try {
            $knightMovesDto = $this->getMinimumNumberOfMovesService->execute(
                new GetMinimumNumberOfMovesRequest($boardId, $knightId, $source, $destination)
            );
        } catch (InvalidParameterException $exception) {
            // A client error: answer 400 instead of letting a plain \Exception
            // surface as a 500.
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        } catch (NotFoundException $exception) {
            // An id the client supplied that names nothing: 404, not 500.
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        } catch (ApplicationException $exception) {
            throw new InfrastructureException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return new Response(
            $this->templateEngine->render(
                '@web_views/knight-moves-solution.html.twig',
                [ 'solution' => $knightMovesDto ]
            )
        );
    }

    /**
     * Reads an optional string from the query string.
     *
     * `?boardId[]=x` makes ParameterBag::get() return an array, which the
     * `?string` request DTO rejects with a TypeError - a 500 for what is
     * malformed client input.
     *
     * @param Request $request Request.
     * @param string  $name    Query parameter name.
     *
     * @return string|null
     *
     * @throws BadRequestHttpException
     */
    private function getOptionalStringQueryParameter(Request $request, string $name): ?string
    {
        $value = $request->query->get($name);

        if ($value !== null && !is_string($value)) {
            throw new BadRequestHttpException("Query parameter \"{$name}\" must be a string");
        }

        return $value;
    }
}
