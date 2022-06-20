<?php declare(strict_types=1);

use Mailery\Widget\Select\Select;
use Mailery\Web\Widget\FlashMessage;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('Campaign tracking #' . $campaign->getId());

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
                ->id('campaign-tracking-form')
                ->begin(); ?>

        <h6 class="font-weight-bold">Tracking configuration</h6>
        <div class="form-text text-muted">Get your campaign statistics right after the start of sending.</div>
        <div class="mb-3"></div>

        <?= $field->select(
            $form,
            'trackClicks',
            [
                'class' => Select::class,
                'items()' => [$form->getBooleanListOptions()],
                'clearable()' => [false],
                'searchable()' => [false],
            ]
        ); ?>

        <?= $field->select(
            $form,
            'trackOpens',
            [
                'class' => Select::class,
                'items()' => [$form->getBooleanListOptions()],
                'clearable()' => [false],
                'searchable()' => [false],
            ]
        ); ?>

        <?= $field->select(
            $form,
            'enableUtmTags',
            [
                'class' => Select::class,
                'items()' => [$form->getBooleanListOptions()],
                'clearable()' => [false],
                'searchable()' => [false],
                'inputCallback()' => [<<<JS
                    (val) => {
                        var classList = document.querySelector(".js-utm-tags-details").classList;
                        if (val == 1) {
                            classList.remove("d-none");
                        } else {
                            classList.add("d-none");
                        }
                    }
                    JS
                ],
            ]
        ); ?>

        <div class="js-utm-tags-details <?= !$form->getEnableUtmTags() ? 'd-none' : '' ?>">
            <div class="mb-3"></div>
            <?= $field->text($form, 'utmSource')->autofocus(); ?>

            <?= $field->text($form, 'utmMedium')->autofocus(); ?>

            <?= $field->text($form, 'utmCampaign')->autofocus(); ?>

            <?= $field->text($form, 'utmContent')->autofocus(); ?>

            <?= $field->text($form, 'utmTerm')->autofocus(); ?>
        </div>

        <?= $this->render('_submit-button', compact('field', 'campaign')) ?>

        <?= Form::end(); ?>
    </div>
</div>

<?= ContentDecorator::end() ?>
