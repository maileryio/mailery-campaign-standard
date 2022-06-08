<?php

use Yiisoft\Form\Widget\Form;
use Mailery\Widget\Select\Select;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var \Mailery\Campaign\Standard\Form\CampaignForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>
<?= Form::widget()
        ->csrf($csrf)
        ->id('campaign-form')
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

<div class="mb-4"></div>
<h6 class="font-weight-bold">Content</h6>
<div class="form-text text-muted">What is the campaign content?</div>
<div class="mb-3"></div>

<?= $field->select(
        $form,
        'template',
        [
            'class' => Select::class,
            'items()' => [$form->getTemplateListOptions()],
            'searchable()' => [false],
            'clearable()' => [false],
        ]
    ); ?>

<div class="mb-4"></div>
<h6 class="font-weight-bold">Define recipients</h6>
<div class="form-text text-muted">Who will this campaign be sent to?</div>
<div class="mb-3"></div>

<?= $field->select(
        $form,
        'groups',
        [
            'class' => Select::class,
            'items()' => [$form->getGroupListOptions()],
            'multiple()' => [true],
            'taggable()' => [true],
            'searchable()' => [false],
            'clearable()' => [false],
        ]
    ); ?>

<?= $field->submitButton()
        ->class('btn btn-primary float-right mt-2')
        ->value($form->hasEntity() ? 'Save changes' : 'Add campaign'); ?>

<?= Form::end(); ?>
