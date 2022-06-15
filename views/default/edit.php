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

$this->setTitle('Campaign details #' . $campaign->getId());

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
                ->id('campaign-details-form')
                ->begin(); ?>

        <h6 class="font-weight-bold">Subject and sender</h6>
        <div class="form-text text-muted">What is the subject line of the campaign?</div>
        <div class="mb-3"></div>

        <?= $field->select(
                $form,
                'sender',
                [
                    'class' => Select::class,
                    'items()' => [$form->getSenderListOptions()],
                    'searchable()' => [false],
                    'clearable()' => [false],
                ]
            ); ?>

        <?= $field->text($form, 'name')->autofocus(); ?>

        <?= $field->submitButton()
                ->class('btn btn-primary float-right mt-2')
                ->value('Save changes'); ?>

        <?= Form::end(); ?>
    </div>
</div>

<?= ContentDecorator::end() ?>
