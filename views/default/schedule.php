<?php declare(strict_types=1);

use Mailery\Campaign\Field\SendingType;
use Mailery\Widget\Select\Select;
use Mailery\Web\Widget\FlashMessage;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Form\ScheduleForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('Campaign schedule #' . $campaign->getId());

$scheduledSendingType = SendingType::asScheduled();
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
    <div class="col-12">
        <?= Form::widget()
                ->csrf($csrf)
                ->id('campaign-schedule-form')
                ->begin(); ?>

        <h6 class="font-weight-bold">Schedule your campaign</h6>
        <div class="form-text text-muted">When would you like to send this campaign?</div>
        <div class="mb-3"></div>

        <?= $field->select(
            $form,
            'sendingType',
            [
                'class' => Select::class,
                'value()' => [$form->getSendingType()->getValue()],
                'items()' => [$form->getSendingTypeListOptions()],
                'clearable()' => [false],
                'searchable()' => [false],
                'inputCallback()' => [<<<JS
                    (val) => {
                        switch(val) {
                            case '{$scheduledSendingType->getValue()}':
                                document.querySelector(".js-schedule-details").classList.remove("d-none");
                                break;
                            default:
                                document.querySelector(".js-schedule-details").classList.add("d-none");
                                break;
                        }
                    }
                    JS
                ],
            ]
        ); ?>

        <div class="js-schedule-details <?= !$campaign->getSendingType()->isScheduled() ? 'd-none' : '' ?>">
            <div class="mb-3"></div>

            <?= $field->select(
                $form,
                'timezone',
                [
                    'class' => Select::class,
                    'items()' => [$form->getTimezoneListOptions()],
                    'clearable()' => [false],
                    'searchable()' => [true],
                ]
            ); ?>
        </div>

        <?= $field->submitButton()
                ->class('btn btn-primary float-right mt-2')
                ->value('Save changes'); ?>

        <?= Form::end(); ?>
    </div>
</div>

<?= ContentDecorator::end() ?>
