<?php

declare(strict_types=1);

namespace Mailery\Campaign\Standard\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Mailery\Campaign\Standard\Form\CampaignForm;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Mailery\Campaign\Repository\CampaignRepository;
use Mailery\Brand\BrandLocatorInterface;
use Mailery\Campaign\Standard\Service\CampaignCrudService;
use Mailery\Campaign\Standard\Service\CampaignSenderService;
use Mailery\Campaign\Standard\ValueObject\CampaignValueObject;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Session\Flash\FlashInterface;

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
     * @var UrlGenerator
     */
    private UrlGenerator $urlGenerator;

    /**
     * @var CampaignRepository
     */
    private CampaignRepository $campaignRepo;

    /**
     * @var CampaignCrudService
     */
    private CampaignCrudService $campaignCrudService;

    /**
     * @var CampaignSenderService
     */
    private CampaignSenderService $campaignSenderService;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param BrandLocatorInterface $brandLocator
     * @param UrlGenerator $urlGenerator
     * @param CampaignRepository $campaignRepo
     * @param CampaignCrudService $campaignCrudService
     * @param CampaignSenderService $campaignSenderService
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        BrandLocatorInterface $brandLocator,
        UrlGenerator $urlGenerator,
        CampaignRepository $campaignRepo,
        CampaignCrudService $campaignCrudService,
        CampaignSenderService $campaignSenderService
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewBasePath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->campaignRepo = $campaignRepo->withBrand($brandLocator->getBrand());
        $this->campaignCrudService = $campaignCrudService->withBrand($brandLocator->getBrand());
        $this->campaignSenderService = $campaignSenderService;
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
     * @param ValidatorInterface $validator
     * @param CampaignForm $form
     * @return Response
     */
    public function create(Request $request, ValidatorInterface $validator, CampaignForm $form): Response
    {
        $body = $request->getParsedBody();

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form, $form->getRules())) {
            $valueObject = CampaignValueObject::fromForm($form);
            $campaign = $this->campaignCrudService->create($valueObject);

            if (!empty($body['send'])) {
                $this->campaignSenderService->send($campaign);
            }

            return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $this->urlGenerator->generate('/campaign/standard/view', ['id' => $campaign->getId()]));
        }

        return $this->viewRenderer->render('create', compact('form'));
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param CampaignForm $form
     * @return Response
     */
    public function edit(Request $request, ValidatorInterface $validator, FlashInterface $flash, CampaignForm $form): Response
    {
        $body = $request->getParsedBody();
        $campaignId = $request->getAttribute('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $form = $form->withCampaign($campaign);

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form, $form->getRules())) {
            $valueObject = CampaignValueObject::fromForm($form);
            $this->campaignCrudService->update($campaign, $valueObject);

            if (!empty($body['send'])) {
                $this->campaignSenderService->send($campaign);
            }

            $flash->add(
                'success',
                [
                    'body' => 'Data have been saved!',
                ],
                true
            );
        }

        return $this->viewRenderer->render('edit', compact('form', 'campaign'));
    }
}
