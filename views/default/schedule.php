<?php declare(strict_types=1);

use Mailery\Campaign\Field\SendingType;
use Mailery\Widget\Datepicker\Datepicker;
use Mailery\Widget\Select\Select;
use Mailery\Web\Widget\FlashMessage;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;
use Yiisoft\Form\Field;

/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Counter\RecipientCounter $recipientCounter */
/** @var Mailery\Campaign\Standard\Entity\StandardCampaign $campaign */
/** @var Mailery\Campaign\Form\ScheduleForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('Campaign schedule #' . $campaign->getId());

$scheduledSendingType = SendingType::asScheduled();
?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-campaign-standard/views/default/_layout.php')
    ->parameters(compact('campaign', 'csrf', 'recipientCounter'))
    ->begin(); ?>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= FlashMessage::widget(); ?>
    </div>
</div>
<div class="mb-2"></div>

<?= Form::tag()
    ->csrf($csrf)
    ->id('campaign-schedule-form')
    ->post()
    ->open(); ?>

<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">Schedule your campaign</h6>
        <div class="form-text text-muted">When would you like to send this campaign?</div>
    </div>
</div>

<div class="mb-3"></div>
<div class="row">
    <div class="col-12">
        <?= Field::input(
            Select::class,
            $form,
            'sendingType',
            [
                'optionsData()' => [$form->getSendingTypeListOptions()],
                'clearable()' => [false],
                'searchable()' => [false],
                'inputCallback()' => [<<<JS
                    (val) => {
                        mailery.app.\$emit('campaign.sending-type.changed', { val });
                    }
                    JS
                ],
            ]
        ); ?>
    </div>
</div>

<ui-listener event="campaign.sending-type.changed" v-slot="{ data }">
    <div v-if="!!data.val ? data.val === '<?= $scheduledSendingType->getValue() ?>' : <?= json_encode($form->getSendingType()->isScheduled()) ?>">
        <div class="row">
            <div class="col-4">
                <?= Field::input(
                    Datepicker::class,
                    $form,
                    'date',
                    [
                        'type()' => ['date'],
                        'format()' => ['YYYY-MM-DD'],
                        'closeOnSelect()' => ['minute'],
                    ]
                ); ?>
            </div>

            <div class="col-3">
                <?= Field::input(
                    Datepicker::class,
                    $form,
                    'time',
                    [
                        'type()' => ['time'],
                        'format()' => ['HH:mm'],
                        'minuteOptions()' => [[0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55]],
                        'closeOnSelect()' => ['minute'],
                    ]
                ); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?= Field::input(
                    Select::class,
                    $form,
                    'timezone',
                    [
                        'optionsData()' => [$form->getTimezoneListOptions()],
                        'clearable()' => [false],
                        'searchable()' => [true],
                        'inputCallback()' => [<<<JS
                            (val) => {
                                mailery.app.\$emit('campaign.timezone.changed', { val });
                            }
                            JS
                        ],
                    ]
                )
                ->hintConfig([
                    'content()' => [<<<TEXT
                        Current time in time zone:
                        <ui-listener event="campaign.timezone.changed" v-slot="{ data }">
                            <ui-clock format="MMMM Do YYYY, HH:mm:ss" :timezone="data.val ?? '{$form->getTimezone()}'"></ui-clock>
                        </ui-listener> • Change this option if your targeted contacts are in a timezone different from yours. This is useful if you have country-specific contact lists.
                        TEXT
                    ],
                    'encode()' => [false],
                    'class()' => ['form-text text-muted'],
                ]); ?>
            </div>
        </div>
    </div>
</ui-listener>

<div class="row">
    <div class="col-12">
        <?= $this->render('_submit-button', compact('campaign')) ?>
    </div>
</div>

<?= Form::tag()->close(); ?>

<?= ContentDecorator::end() ?>
