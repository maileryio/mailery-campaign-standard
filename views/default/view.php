<?php declare(strict_types=1);

use Mailery\Icon\Icon;
use Mailery\Web\Widget\FlashMessage;
use Mailery\Web\Widget\SizeUnitsFormat;
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
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle($campaign->getName());

?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-campaign-standard/views/default/_layout.php')
    ->parameters(compact('campaign', 'csrf'))
    ->begin(); ?>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">General details</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <p>
            <strong class="h6">Subject</strong>
            <span class="border border-light rounded bg-light text-dark p-1"><?= Html::encode($campaign->getName()) ?></span>
        </p>
        <p>
            <strong class="h6">From</strong>
            <span class="border border-light rounded bg-light text-dark p-1"><?= Html::encode(new SenderLabel($campaign->getSender()->getName(), $campaign->getSender()->getEmail())) ?></span>
        </p>
        <p>
            <strong class="h6">Reply to</strong>
            <span class="border border-light rounded bg-light text-dark p-1"><?= Html::encode(new SenderLabel($campaign->getSender()->getReplyName(), $campaign->getSender()->getReplyEmail())) ?></span>
        </p>

        <a class="btn btn-outline-secondary float-right mt-2" href="<?= $url->generate($campaign->getEditRouteName(), $campaign->getEditRouteParams()); ?>">
            Edit details
        </a>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">Content</h6>
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
                            <span class="mb-2 ml-2">Total size of one email: <?= SizeUnitsFormat::widget()->string($campaign->getTemplate()->getHtmlContent()) ?></span>
                        </p>
                    </li>
                </ul>
            </div>
        </div>

        <div class="camp-view-content__send-test mt-3">
            <a class="font-weight-bold" href="javascript:void(0);" v-b-modal.modal-send-test>Send test email</a>
        </div>

        <a class="btn btn-outline-secondary float-right mt-2" href="<?= $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]); ?>">
            Edit content
        </a>
    </div>
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

<?= ContentDecorator::end() ?>
