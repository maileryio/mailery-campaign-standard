<?php declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\Router\UrlGeneratorInterface $urlGenerator */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Standard\Form\CampaignForm $form */
/** @var string $csrf */

$this->setTitle('New standard campaign');

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h3">New standard campaign</h1>
            <div class="btn-toolbar float-right">
                <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/campaign/default/index'); ?>">
                    Back
                </a>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>

<?php if ($form->getChannel() === null) {
    ?><div class="row">
        <div class="col-12 col-xl-4">
            <?= Form::widget()
                ->options(
                    [
                        'id' => 'form-campaign-channel',
                        'csrf' => $csrf,
                        'enctype' => 'multipart/form-data',
                    ]
                )
                ->begin(); ?>

            <h3 class="h6">Select channel</h3>
            <div class="form-text text-muted">What is the campaign channel?</div>
            <div class="mb-4"></div>

            <?= $field->config($form, 'channel')
                ->dropDownList($form->getChannelListOptions()); ?>

            <?= Html::submitButton(
                'Next',
                [
                    'class' => 'btn btn-primary float-right mt-2',
                    'name' => 'creating-next-step',
                    'value' => '1',
                ]
            ); ?>

            <?= Form::end(); ?>
        </div>
    </div><?php
} else {
    ?><?= $this->render('_form', compact('csrf', 'field', 'form')) ?><?php
} ?>
