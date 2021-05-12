<?php

declare(strict_types=1);

namespace Mailery\Campaign\Standard\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Mailery\Campaign\Form\SendTestForm;
use Mailery\Campaign\Standard\Form\CampaignForm;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Mailery\Campaign\Repository\CampaignRepository;
use Mailery\Brand\BrandLocatorInterface;
use Mailery\Campaign\Service\SendoutService;
use Mailery\Campaign\Service\SendoutCrudService;
use Mailery\Campaign\ValueObject\SendoutValueObject;
use Mailery\Campaign\Standard\Service\CampaignCrudService;
use Mailery\Campaign\Standard\ValueObject\CampaignValueObject;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Mailery\Sender\Repository\SenderRepository;
use Mailery\Sender\Email\Model\SenderLabel;
use Mailery\Channel\Model\ChannelTypeList;

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
     * @var SenderRepository
     */
    private SenderRepository $senderRepo;

    /**
     * @var CampaignCrudService
     */
    private CampaignCrudService $campaignCrudService;

    /**
     * @var SendoutService
     */
    private SendoutService $sendoutService;

    /**
     * @var SendoutCrudService
     */
    private SendoutCrudService $sendoutCrudService;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param BrandLocatorInterface $brandLocator
     * @param UrlGenerator $urlGenerator
     * @param CampaignRepository $campaignRepo
     * @param SenderRepository $senderRepo
     * @param CampaignCrudService $campaignCrudService
     * @param SendoutService $sendoutService
     * @param SendoutCrudService $sendoutCrudService
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        BrandLocatorInterface $brandLocator,
        UrlGenerator $urlGenerator,
        CampaignRepository $campaignRepo,
        SenderRepository $senderRepo,
        CampaignCrudService $campaignCrudService,
        SendoutService $sendoutService,
        SendoutCrudService $sendoutCrudService
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewBasePath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->campaignRepo = $campaignRepo->withBrand($brandLocator->getBrand());
        $this->senderRepo = $senderRepo->withBrand($brandLocator->getBrand());
        $this->campaignCrudService = $campaignCrudService->withBrand($brandLocator->getBrand());
        $this->sendoutService = $sendoutService;
        $this->sendoutCrudService = $sendoutCrudService;
    }

    /**
     * @param Request $request
     * @param SendTestForm $testForm
     * @return Response
     */
    public function view(Request $request, SendTestForm $testForm): Response
    {
        $campaignId = $request->getAttribute('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $sender = $this->senderRepo->findByPK($campaign->getSender()->getId());

        return $this->viewRenderer->render('view', compact('campaign', 'sender', 'testForm'));
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
            if ($form->getChannel() === null) {
                return $this->viewRenderer->render('create', compact('form'));
            }

            if (empty($body['creating-next-step']) && $validator->validate($form)->isValid()) {
                $valueObject = CampaignValueObject::fromForm($form);
                $campaign = $this->campaignCrudService->create($valueObject);

                $flash->add(
                    'success',
                    [
                        'body' => 'Data have been saved!',
                    ],
                    true
                );

                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $this->urlGenerator->generate('/campaign/standard/view', ['id' => $campaign->getId()]));
            }
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

        $form = $form->withEntity($campaign);

        if ($request->getMethod() === Method::POST && $form->load($body) && $validator->validate($form)) {
            $valueObject = CampaignValueObject::fromForm($form);
            $this->campaignCrudService->update($campaign, $valueObject);

            $flash->add(
                'success',
                [
                    'body' => 'Data have been saved!',
                ],
                true
            );

            return $this->responseFactory
                ->createResponse(302)
                ->withHeader('Location', $this->urlGenerator->generate('/campaign/standard/view', ['id' => $campaign->getId()]));
        }

        return $this->viewRenderer->render('edit', compact('form', 'campaign'));
    }

    /**
     * @param Request $request
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $campaignId = $request->getAttribute('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $this->campaignCrudService->delete($campaign);

        return $this->responseFactory
            ->createResponse(302)
            ->withHeader('Location', $this->urlGenerator->generate('/campaign/default/index'));
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SendTestForm $form
     * @param ChannelTypeList $channelTypeList
     * @return Response
     */
    public function test(Request $request, ValidatorInterface $validator, SendTestForm $form, ChannelTypeList $channelTypeList): Response
    {
        $campaignId = $request->getAttribute('id');
        if (empty($campaignId) || ($campaign = $this->campaignRepo->findByPK($campaignId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $body = $request->getParsedBody();

        if ($request->getMethod() === Method::POST && $form->load($body) && $validator->validate($form)) {
            $recipients = $channelTypeList->findByEntity($campaign->getChannel())
                ->getRecipientIterator()
                ->appendIdentificators($form->getAttributeValue('recipients'));

            $sendout = $this->sendoutCrudService->create(
                (new SendoutValueObject())
                    ->withCampaign($campaign)
                    ->withRecipients($recipients)
            );

            $this->sendoutService->send($sendout);
        }

        return $this->responseFactory
            ->createResponse(302)
            ->withHeader('Location', $this->urlGenerator->generate('/campaign/standard/view', ['id' => $campaign->getId()]));
    }
}
