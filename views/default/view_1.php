<?php declare(strict_types=1);

use Mailery\Web\Widget\FlashMessage;
use Mailery\Sender\Email\Model\SenderLabel;
use Yiisoft\Html\Html;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;

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
        <?= FlashMessage::widget(); ?>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-md-3">
        <h3 class="h6">Test send this campaign</h3>
        <div class="mb-4"></div>

        <?= Form::widget()
                ->action($url->generate('/campaign/sendout/test', ['id' => $campaign->getId()]))
                ->csrf($csrf)
                ->id('campaign-test-form')
                ->begin(); ?>

        <?= $field->text($testForm, 'recipients'); ?>
        <div class="form-text text-muted">Enter addresses, separated by a commas, ex. <?= Html::encode('"Bob Smith" <bob@company.com>, joe@company.com') ?></div>

        <?= $field->submitButton()
                ->class('btn btn-outline-secondary float-right mt-2')
                ->value('Send test'); ?>

        <?= Form::end(); ?>
    </div>
    <div class="col-md-9">
        <div class="callout callout-info">
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
        </div>

        <iframe class="border-0 w-100 min-vh-100" src="<?= $url->generate('/campaign/standard/preview', ['id' => $campaign->getId()]) ?>"></iframe>
    </div>
</div>

<?= ContentDecorator::end() ?>
