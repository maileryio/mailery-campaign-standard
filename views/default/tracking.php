<?php declare(strict_types=1);

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
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('Campaign tracking #' . $campaign->getId());

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

<div class="row">
    <div class="col-12">
        <?= Form::tag()
            ->csrf($csrf)
            ->id('campaign-tracking-form')
            ->post()
            ->open(); ?>

        <h6 class="font-weight-bold">Tracking configuration</h6>
        <div class="form-text text-muted">Get your campaign statistics right after the start of sending.</div>
        <div class="mb-3"></div>

        <?= Field::input(
            Select::class,
            $form,
            'trackClicks',
            [
                'optionsData()' => [$form->getBooleanListOptions()],
                'clearable()' => [false],
                'searchable()' => [false],
            ]
        ); ?>

        <?= Field::input(
            Select::class,
            $form,
            'trackOpens',
            [
                'optionsData()' => [$form->getBooleanListOptions()],
                'clearable()' => [false],
                'searchable()' => [false],
            ]
        ); ?>

        <?= Field::input(
            Select::class,
            $form,
            'enableUtmTags',
            [
                'optionsData()' => [$form->getBooleanListOptions()],
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
            <?= Field::text($form, 'utmSource')->autofocus(); ?>

            <?= Field::text($form, 'utmMedium')->autofocus(); ?>

            <?= Field::text($form, 'utmCampaign')->autofocus(); ?>

            <?= Field::text($form, 'utmContent')->autofocus(); ?>

            <?= Field::text($form, 'utmTerm')->autofocus(); ?>
        </div>

        <?= $this->render('_submit-button', compact('campaign')) ?>

        <?= Form::tag()->close(); ?>
    </div>
</div>

<?= ContentDecorator::end() ?>
