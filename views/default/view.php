<?php declare(strict_types=1);

use Mailery\Icon\Icon;
use Mailery\Web\Widget\FlashMessage;
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
            <div class="camp-view-content__preview">
                <iframe
                    src="<?= $url->generate('/campaign/standard/preview', ['id' => $campaign->getId()]) ?>"
                    scrolling="no">
                </iframe>

                <div class="camp-view-content__preview--overlay d-flex justify-content-center align-items-center" v-b-modal.modal-preview>
                    <div class="flex-grow-1 text-center">
                        <?= Icon::widget()->name('magnify'); ?>
                        <p class="font-weight-bold">Preview</p>
                    </div>
                </div>
            </div>

            <div class="camp-view-content__details ml-4">
                <h5>Card title</h5>
            </div>
        </div>

        <a class="btn btn-outline-secondary float-right mt-2" href="<?= $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]); ?>">
            Edit content
        </a>
    </div>
</div>

<b-modal id="modal-preview" centered hide-footer>
    <iframe
        src="<?= $url->generate('/campaign/standard/preview', ['id' => $campaign->getId()]) ?>"
        scrolling="no">
    </iframe>
</b-modal>

<?= ContentDecorator::end() ?>
