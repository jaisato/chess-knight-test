<?php

namespace Chess\Infrastructure\UI\Web\Controller;

use Chess\Application\ApplicationException;
use Chess\Application\InvalidParameterException;
use Chess\Application\Knight\GetMinimumNumberOfMovesRequest;
use Chess\Application\Knight\GetMinimumNumberOfMovesService;
use Chess\Infrastructure\InfrastructureException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @throws InvalidArgumentException
     * @throws InfrastructureException
     */
    public function getNumberOfMoves(Request $request)
    {
        $boardId = $request->query->get('boardId');
        $knightId = $request->query->get('knightId');
        $source = $request->query->getInt('source', 0);
        $destination = $request->query->getInt('destination', 63);

        try {
            $knightMovesDto = $this->getMinimumNumberOfMovesService->execute(
                new GetMinimumNumberOfMovesRequest($boardId, $knightId, $source, $destination)
            );
        } catch (InvalidParameterException $exception) {
            throw new InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
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
}
