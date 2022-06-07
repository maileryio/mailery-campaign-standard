<?php declare(strict_types=1);

use Mailery\Icon\Icon;
use Mailery\Web\Widget\ByteUnitsFormat;
use Mailery\Campaign\Standard\Entity\StandardCampaign as Campaign;
use Mailery\Sender\Email\Model\SenderLabel;
use Yiisoft\Html\Html;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;
use Yiisoft\Yii\DataView\DetailView;

/** @var Yiisoft\Assets\AssetManager $assetManager */
/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Standard\Entity\StandardCampaign $campaign */
/** @var Mailery\Campaign\Form\SendTestForm $testForm */
/** @var Mailery\Subscriber\Counter\SubscriberCounter $subscriberCounter */
/** @var Yiisoft\Yii\View\Csrf $csrf */
/** @var Yiisoft\Translator\TranslatorInterface $translator */

$this->setTitle($campaign->getName());

?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-campaign-standard/views/default/_layout.php')
    ->parameters(compact('campaign', 'csrf'))
    ->begin(); ?>

<div class="mb-2"></div>
<div class="row">
    <div class="col-md">
        <h6 class="font-weight-bold">General details</h6>
    </div>
    <div class="col-auto">
        <div class="btn-toolbar float-right">
            <?php if ($campaign->canBeEdited()) { ?>
                <a class="btn btn-outline-secondary float-right" href="<?= $url->generate($campaign->getEditRouteName(), $campaign->getEditRouteParams()); ?>">
                    Edit details
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= DetailView::widget()
            ->model($campaign)
            ->options([
                'class' => 'table table-top-borderless detail-view',
            ])
            ->emptyValue('<span class="text-muted">(not set)</span>')
            ->attributes([
                [
                    'label' => 'Subject',
                    'value' => function (Campaign $data) {
                        return $data->getName();
                    },
                ],
                [
                    'label' => 'From',
                    'value' => function (Campaign $data) {
                        return new SenderLabel($data->getSender()->getName(), $data->getSender()->getEmail());
                    },
                    'encode' => true,
                ],
                [
                    'label' => 'Reply to',
                    'value' => function (Campaign $data) {
                        return new SenderLabel($data->getSender()->getReplyName(), $data->getSender()->getReplyEmail());
                    },
                    'encode' => true,
                ],
            ]);
        ?>
    </div>
</div>

<hr class="mb-4"/>
<div class="row">
    <div class="col-md">
        <h6 class="font-weight-bold">Content</h6>
    </div>
    <div class="col-auto">
        <div class="btn-toolbar float-right">
            <?php if ($campaign->canBeEdited()) { ?>
                <a class="btn btn-outline-secondary float-right" href="<?= $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]); ?>">
                    Edit content
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <div class="camp-view-content d-flex">
            <!--<?= $assetManager->getAssetUrl(Mailery\Campaign\Assets\CampaignAssetBundle::class, 'images/empty-image-bg.svg') ?>-->
            <div class="camp-view-content__thumbnail-preview">
                <iframe
                    src="<?= $url->generate('/campaign/standard/preview', ['id' => $campaign->getId()]) ?>"
                    scrolling="no">
                </iframe>

                <div class="camp-view-content__thumbnail-preview--overlay d-flex justify-content-center align-items-center" v-b-modal.modal-preview-html>
                    <div class="flex-grow-1 text-center">
                        <?= Icon::widget()->name('magnify'); ?>
                        <p class="font-weight-bold">Preview</p>
                    </div>
                </div>
            </div>

            <div class="camp-view-content__details ml-3">
                <ul class="list-unstyled mt-2">
                    <li>
                        <p class="d-flex align-items-center mb-0">
                            <?= Icon::widget()->name('check-circle-outline')->options(['class' => 'text-success h5']) ?>
                            <span class="mb-2 ml-2">A plain-text version of this email will be included: <a href="javascript:void(0);" v-b-modal.modal-preview-text>Preview</a></span>
                        </p>
                    </li>
                    <li>
                        <p class="d-flex align-items-center mb-0">
                            <?= Icon::widget()->name('check-circle-outline')->options(['class' => 'text-success h5']) ?>
                            <span class="mb-2 ml-2">
                                Total size of one email: <?= ByteUnitsFormat::widget()->string($campaign->getTemplate()->getHtmlContent()) ?>
                                <?= Icon::widget()
                                    ->name('help-circle-outline')
                                    ->options([
                                        'id' => $tooltipTarget = 'tooltip-' . uniqid(),
                                    ]); ?>

                                <b-tooltip target="<?= $tooltipTarget ?>">
                                    Total size does not take into account third-party resources, including images.
                                </b-tooltip>
                            </span>
                        </p>
                    </li>
                </ul>
            </div>
        </div>

        <div class="camp-view-content__send-test mt-3">
            <a class="font-weight-bold" href="javascript:void(0);" v-b-modal.modal-send-test>Send test email</a>
        </div>

        <b-modal id="modal-preview-html" title="Design preview">
            <template #modal-footer="{ cancel }">
                <b-button variant="outline-secondary" @click="cancel()">Cancel</b-button>
                <a class="btn btn-primary" href="<?= $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]) ?>">Edit content</a>
            </template>

            <iframe src="<?= $url->generate('/campaign/standard/preview', ['id' => $campaign->getId()]) ?>"></iframe>
        </b-modal>

        <b-modal id="modal-preview-text" title="Text preview">
            <template #modal-footer="{ cancel }">
                <b-button variant="outline-secondary" @click="cancel()">Cancel</b-button>
                <a class="btn btn-primary" href="<?= $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]) ?>">Edit content</a>
            </template>

            <?= nl2br(Html::encode($campaign->getTemplate()->getTextContent())); ?>
        </b-modal>

        <b-modal id="modal-send-test" title="Test send this campaign">
            <template #modal-footer="{ cancel, ok }">
                <b-button variant="outline-secondary" @click="cancel()">Cancel</b-button>
                <b-button variant="primary" @click="ok()">Send email</b-button>
            </template>

            <?= Form::widget()
                ->action($url->generate('/campaign/sendout/test', ['id' => $campaign->getId()]))
                ->csrf($csrf)
                ->id('campaign-test-form')
                ->begin(); ?>

            <?= $field->text($testForm, 'recipients'); ?>
            <div class="form-text text-muted">Enter addresses, separated by a commas, ex. <?= Html::encode('"Bob Smith" <bob@company.com>, joe@company.com') ?></div>

            <?= Form::end(); ?>
        </b-modal>
    </div>
</div>

<hr class="mb-4"/>
<div class="row">
    <div class="col-md">
        <h6>
            <span class="font-weight-bold">Recipients</span>
            <span class="ml-2">5 <span class="text-muted">/ 10</span></span>
            <?= Icon::widget()
                ->name('help-circle-outline')
                ->options([
                    'id' => $tooltipTarget = 'tooltip-' . uniqid(),
                ]); ?>

            <b-tooltip target="<?= $tooltipTarget ?>">
                Active contacts who will receive the newsletter / all contacts.
                <br/>
                The newsletter will not be received by inactive contacts and those who have unsubscribed. Even if the subscriber belongs to more than one group, he will receive the email only once.
            </b-tooltip>
        </h6>
    </div>
    <div class="col-auto">
        <div class="btn-toolbar float-right">
            <?php if ($campaign->canBeEdited()) { ?>
                <a class="btn btn-outline-secondary float-right" href="<?= $url->generate('/campaign/standard/recipients', ['id' => $campaign->getId()]); ?>">
                    Edit recipients
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <table class="table table-top-borderless">
            <tbody>
                <?php foreach ($campaign->getGroups() as $group) {
                    ?><tr>
                        <td><?= $group->getName() ?></td>
                    </tr><?php
                } ?>
            </tbody>
        </table>
    </div>
</div>

<hr class="mb-4"/>
<div class="row">
    <div class="col-md">
        <h6 class="font-weight-bold">Tracking settings</h6>
    </div>
    <div class="col-auto">
        <div class="btn-toolbar float-right">
            <?php if ($campaign->canBeEdited()) { ?>
                <a class="btn btn-outline-secondary float-right" href="<?= $url->generate('/campaign/standard/tracking', ['id' => $campaign->getId()]); ?>">
                    Edit tracking
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <table class="table table-top-borderless">
            <tbody>
                <tr>
                    <td>Track opens</td>
                    <td>
                        <span class="badge badge-success">Enable</span>
                    </td>
                </tr>
                <tr>
                    <td>Track clicks</td>
                    <td>
                        <span class="badge badge-success">Enable</span>
                    </td>
                </tr>
                <tr>
                    <td>Enable UTM tags</td>
                    <td>
                        <span class="badge badge-success">Enable</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= ContentDecorator::end() ?>
