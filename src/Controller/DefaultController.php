<?php

declare(strict_types=1);

namespace Mailery\Campaign\Standard\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Http\Header;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Mailery\Campaign\Form\CampaignForm;
use Mailery\Campaign\Form\SendTestForm;
use Mailery\Campaign\Form\ScheduleForm;
use Mailery\Campaign\Form\TrackingForm;
use Mailery\Campaign\ValueObject\ScheduleValueObject;
use Mailery\Campaign\ValueObject\TrackingValueObject;
use Mailery\Campaign\Service\TrackingCrudService;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Mailery\Campaign\Repository\CampaignRepository;
use Mailery\Brand\BrandLocatorInterface;
use Mailery\Campaign\Standard\Service\CampaignCrudService;
use Mailery\Campaign\Standard\ValueObject\CampaignValueObject;
use Mailery\Subscriber\Counter\SubscriberCounter;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Router\CurrentRoute;

class DefaultController
{
    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param UrlGenerator $urlGenerator
     * @param CampaignRepository $campaignRepo
     * @param CampaignCrudService $campaignCrudService
     * @param TrackingCrudService $trackingCrudService
     * @param BrandLocatorInterface $brandLocator
     */
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ResponseFactory $responseFactory,
        private UrlGenerator $urlGenerator,
        private CampaignRepository $campaignRepo,
        private CampaignCrudService $campaignCrudService,
        private TrackingCrudService $trackingCrudService,
        BrandLocatorInterface $brandLocator
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewPath(dirname(dirname(__DIR__)) . '/views');

        $this->campaignRepo = $campaignRepo->withBrand($brandLocator->getBrand());
        $this->campaignCrudService = $campaignCrudService->withBrand($brandLocator->getBrand());
    }

    /**
     * @param CurrentRoute $currentRoute
     * @param SubscriberCounter $subscriberCounter
     * @param SendTestForm $testForm
     * @return Response
     */
    public function view(CurrentRoute $currentRoute, SubscriberCounter $subscriberCounter, SendTestForm $testForm): Response
    {
        $campaignId = $currentRoute->getArgument('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $subscriberCounter = $subscriberCounter->withGroups(...$campaign->getGroups()->toArray());

        return $this->viewRenderer->render('view', compact('campaign', 'subscriberCounter', 'testForm'));
    }

    /**
     * @param CurrentRoute $currentRoute
     * @return Response
     */
    public function preview(CurrentRoute $currentRoute): Response
    {
        $campaignId = $currentRoute->getArgument('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($campaign->getTemplate()->getHtmlContent());

        return $response;
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

        if (
            $request->getMethod() === Method::POST
            && $form->load($body)
        ) {
            if ($form->getSender() === null) {
                $validator->validate($form);
                return $this->viewRenderer->render('create', compact('form'));
            }

            if (empty($body['creating-next-step']) && $validator->validate($form)->isValid()) {
                $valueObject = CampaignValueObject::fromForm($form);
                $campaign = $this->campaignCrudService->create($valueObject);

                return $this->responseFactory
                    ->createResponse(Status::FOUND)
                    ->withHeader(Header::LOCATION, $this->urlGenerator->generate($campaign->getViewRouteName(), $campaign->getViewRouteParams()));
            }
        }

        return $this->viewRenderer->render('create', compact('form'));
    }

    /**
     * @param Request $request
     * @param CurrentRoute $currentRoute
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param CampaignForm $form
     * @return Response
     */
    public function edit(Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator, FlashInterface $flash, CampaignForm $form): Response
    {
        $body = $request->getParsedBody();
        $campaignId = $currentRoute->getArgument('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $form = $form->withEntity($campaign);

        if ($request->getMethod() === Method::POST && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = CampaignValueObject::fromForm($form);
            $this->campaignCrudService->update($campaign, $valueObject);

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

    /**
     * @param Request $request
     * @param CurrentRoute $currentRoute
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param CampaignForm $form
     * @return Response
     */
    public function content(Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator, FlashInterface $flash, CampaignForm $form): Response
    {
        $body = $request->getParsedBody();
        $campaignId = $currentRoute->getArgument('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $form = $form->withEntity($campaign);

        if ($request->getMethod() === Method::POST && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = CampaignValueObject::fromForm($form);
            $this->campaignCrudService->update($campaign, $valueObject);

            $flash->add(
                'success',
                [
                    'body' => 'Data have been saved!',
                ],
                true
            );
        }

        return $this->viewRenderer->render('content', compact('form', 'campaign'));
    }

    /**
     * @param Request $request
     * @param CurrentRoute $currentRoute
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param CampaignForm $form
     * @return Response
     */
    public function recipients(Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator, FlashInterface $flash, CampaignForm $form): Response
    {
        $body = $request->getParsedBody();
        $campaignId = $currentRoute->getArgument('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $form = $form->withEntity($campaign);

        if ($request->getMethod() === Method::POST && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = CampaignValueObject::fromForm($form);
            $this->campaignCrudService->update($campaign, $valueObject);

            $flash->add(
                'success',
                [
                    'body' => 'Data have been saved!',
                ],
                true
            );
        }

        return $this->viewRenderer->render('recipients', compact('form', 'campaign'));
    }

    /**
     * @param Request $request
     * @param CurrentRoute $currentRoute
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param TrackingForm $form
     * @return Response
     */
    public function tracking(Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator, FlashInterface $flash, TrackingForm $form): Response
    {
        $body = $request->getParsedBody();
        $campaignId = $currentRoute->getArgument('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $form = $form->withEntity($campaign);

        if ($request->getMethod() === Method::POST && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = TrackingValueObject::fromForm($form);
            $this->trackingCrudService->update($campaign, $valueObject);

            $flash->add(
                'success',
                [
                    'body' => 'Data have been saved!',
                ],
                true
            );
        }

        return $this->viewRenderer->render('tracking', compact('form', 'campaign'));
    }

    /**
     * @param Request $request
     * @param CurrentRoute $currentRoute
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param ScheduleForm $form
     * @return Response
     */
    public function schedule(Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator, FlashInterface $flash, ScheduleForm $form): Response
    {
        $body = $request->getParsedBody();
        $campaignId = $currentRoute->getArgument('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        if (($schedule = $campaign->getSchedule()) !== null) {
            $form = $form->withEntity($schedule);
        }

        if ($request->getMethod() === Method::POST && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = ScheduleValueObject::fromForm($form);
            $this->scheduleCrudService->update($campaign, $valueObject);

            $flash->add(
                'success',
                [
                    'body' => 'Data have been saved!',
                ],
                true
            );
        }

        return $this->viewRenderer->render('schedule', compact('form', 'campaign'));
    }

    /**
     * @param CurrentRoute $currentRoute
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function delete(CurrentRoute $currentRoute): Response
    {
        $campaignId = $currentRoute->getArgument('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $this->campaignCrudService->delete($campaign);

        return $this->responseFactory
            ->createResponse(Status::SEE_OTHER)
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/campaign/default/index'));
    }
}
