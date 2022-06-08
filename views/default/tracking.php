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
            ]
        ); ?>

        <?= $field->submitButton()
                ->class('btn btn-primary float-right mt-2')
                ->value('Save changes'); ?>

        <?= Form::end(); ?>
    </div>
</div>

<?= ContentDecorator::end() ?>
