<?php

use Yiisoft\Html\Tag\Form;
use Mailery\Widget\Select\Select;
use Yiisoft\Form\Field;

/** @var Yiisoft\View\WebView $this */
/** @var Mailery\Campaign\Form\CampaignForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>
<?= Form::tag()
        ->csrf($csrf)
        ->id('campaign-form')
        ->post()
        ->open(); ?>

<h6 class="font-weight-bold">Subject and sender</h6>
<div class="form-text text-muted">What is the subject line of the campaign?</div>
<div class="mb-3"></div>

<?= Field::input(
    Select::class,
    $form,
    'sender',
    [
        'optionsData()' => [$form->getSenderListOptions()],
        'searchable()' => [false],
        'clearable()' => [false],
    ]
); ?>

<?= Field::text($form, 'name')->autofocus(); ?>

<div class="mb-4"></div>
<h6 class="font-weight-bold">Content</h6>
<div class="form-text text-muted">What is the campaign content?</div>
<div class="mb-3"></div>

<?= Field::input(
    Select::class,
    $form,
    'template',
    [
        'optionsData()' => [$form->getTemplateListOptions()],
        'searchable()' => [false],
        'clearable()' => [false],
    ]
); ?>

<div class="mb-4"></div>
<h6 class="font-weight-bold">Define recipients</h6>
<div class="form-text text-muted">Who will this campaign be sent to?</div>
<div class="mb-3"></div>

<?= Field::input(
    Select::class,
    $form,
    'groups',
    [
        'optionsData()' => [$form->getGroupListOptions()],
        'multiple()' => [true],
        'taggable()' => [true],
        'searchable()' => [false],
        'clearable()' => [false],
    ]
); ?>

<?= Field::submitButton()
        ->content($form->hasEntity() ? 'Save changes' : 'Add campaign'); ?>

<?= Form::tag()->close(); ?>
