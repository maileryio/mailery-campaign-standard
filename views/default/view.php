<?php declare(strict_types=1);

use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\Icon\Icon;
use Mailery\Widget\Link\Link;
use Mailery\Web\Widget\FlashMessage;
use Mailery\Sender\Email\Model\SenderLabel;
use Yiisoft\Html\Html;
use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Standard\Entity\StandardCampaign $campaign */
/** @var Mailery\Campaign\Form\SendTestForm $testForm */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle($campaign->getName());

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h3">Campaign #<?= $campaign->getId(); ?></h1>
            <div class="btn-toolbar float-right">
                <?= Link::widget()
                    ->csrf($csrf)
                    ->label(Icon::widget()->name('delete')->options(['class' => 'mr-1'])->render() . ' Delete')
                    ->method('delete')
                    ->href($url->generate($campaign->getDeleteRouteName(), $campaign->getDeleteRouteParams()))
                    ->confirm('Are you sure?')
                    ->options([
                        'class' => 'btn btn-sm btn-danger mx-sm-1 mb-2',
                    ])
                    ->encode(false);
                ?>
                <a class="btn btn-sm btn-secondary mx-sm-1 mb-2" href="<?= $url->generate($campaign->getEditRouteName(), $campaign->getEditRouteParams()); ?>">
                    <?= Icon::widget()->name('pencil')->options(['class' => 'mr-1']); ?>
                    Update
                </a>
                <b-dropdown right size="sm" variant="secondary" class="mb-2">
                    <template v-slot:button-content>
                        <?= Icon::widget()->name('settings'); ?>
                    </template>
                    <?= ActivityLogLink::widget()
                        ->tag('b-dropdown-item')
                        ->label('Activity log')
                        ->entity($campaign); ?>
                </b-dropdown>
                <div class="btn-toolbar float-right">
                    <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $url->generate('/campaign/default/index'); ?>">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12 col-xl-4">
        <?= FlashMessage::widget(); ?>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-md-4">
        <h3 class="h6">Test send this campaign</h3>
        <div class="mb-4"></div>

        <?= Form::widget()
                ->action($url->generate('/campaign/sendout/test', ['id' => $campaign->getId()]))
                ->csrf($csrf)
                ->id('campaign-test-form')
                ->begin(); ?>

        <?= $field->text($testForm, 'recipients'); ?>
        <div class="form-text text-muted">Enter unlimited number of addresses, separated by a commas, ex. <?= Html::encode('"Bob Smith" <bob@company.com>, joe@company.com') ?></div>

        <?= $field->submitButton()
                ->class('btn btn-outline-secondary float-right mt-2')
                ->value('Test send this newsletter'); ?>

        <?= Form::end(); ?>
    </div>
    <div class="col-md-8">
        <div class="callout callout-info">
            <p>
                <strong class="h6">From</strong>
                <span class="border border-light rounded bg-light text-dark p-1"><?= Html::encode(new SenderLabel($campaign->getSender()->getName(), $campaign->getSender()->getEmail())) ?></span>
            </p>
            <p>
                <strong class="h6">Reply to</strong>
                <span class="border border-light rounded bg-light text-dark p-1"><?= Html::encode(new SenderLabel($campaign->getSender()->getReplyName(), $campaign->getSender()->getReplyEmail())) ?></span>
            </p>
            <p>
                <strong class="h6">Subject</strong>
                <span class="border border-light rounded bg-light text-dark p-1"><?= Html::encode($campaign->getName()) ?></span>
            </p>
        </div>

        <iframe class="border-0 w-100 min-vh-100" src="<?= $url->generate('/campaign/standard/preview', ['id' => $campaign->getId()]) ?>"></iframe>
    </div>
</div>
