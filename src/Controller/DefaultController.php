<?php

declare(strict_types=1);

namespace Mailery\Campaign\Regular\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Mailery\Campaign\Regular\Form\CampaignForm;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Mailery\Campaign\Repository\CampaignRepository;
use Mailery\Brand\Service\BrandLocatorInterface;

class DefaultController
{
    /**
     * @var ViewRenderer
     */
    private ViewRenderer $viewRenderer;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var CampaignRepository
     */
    private CampaignRepository $campaignRepo;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param BrandLocatorInterface $brandLocator
     * @param CampaignRepository $campaignRepo
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        BrandLocatorInterface $brandLocator,
        CampaignRepository $campaignRepo
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewBasePath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->campaignRepo = $campaignRepo->withBrand($brandLocator->getBrand());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function view(Request $request): Response
    {
        $campaignId = $request->getAttribute('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('view', compact('campaign'));
    }

    /**
     * @param Request $request
     * @param CampaignForm $campaignForm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function create(Request $request, CampaignForm $campaignForm, UrlGenerator $urlGenerator): Response
    {
        $campaignForm
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
        ;

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $campaignForm->loadFromServerRequest($request);

            if (($campaign = $campaignForm->save()) !== null) {
                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $urlGenerator->generate('/campaign/standard/view', ['id' => $campaign->getId()]));
            }
        }

        return $this->viewRenderer->render('create', compact('campaignForm', 'submitted'));
    }

    /**
     * @param Request $request
     * @param CampaignForm $campaignForm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function edit(Request $request, CampaignForm $campaignForm, UrlGenerator $urlGenerator): Response
    {
        $campaignId = $request->getAttribute('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $campaignForm
            ->withCampaign($campaign)
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
        ;

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $campaignForm->loadFromServerRequest($request);

            if ($campaignForm->save() !== null) {
                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $urlGenerator->generate('/campaign/standard/view', ['id' => $campaign->getId()]));
            }
        }

        return $this->viewRenderer->render('edit', compact('campaign', 'campaignForm', 'submitted'));
    }
}
