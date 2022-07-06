<?php declare(strict_types=1);

use Yiisoft\Yii\Widgets\ContentDecorator;

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
    <div class="col-12">
        <h6 class="font-weight-bold">Link activity</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        ....
    </div>
</div>

<div class="mb-4"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">Last opened</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        ....
    </div>
</div>

<div class="mb-4"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">Last unsubscribed</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        ....
    </div>
</div>

<div class="mb-4"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">Last bounced</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        ....
    </div>
</div>

<div class="mb-4"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">Last marked as spam</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        ....
    </div>
</div>

<div class="mb-4"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">All countries</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        ....
    </div>
</div>

<?= ContentDecorator::end() ?>
