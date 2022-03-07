<?php

use Yiisoft\Html\Html;
use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var Mailery\Campaign\Standard\Form\CampaignForm $form */
/** @var string $csrf */

?>
<div class="row">
    <div class="col-12 col-xl-4">
        <?= Form::widget()
            ->options(
                [
                    'id' => 'form-campaign',
                    'csrf' => $csrf,
                    'enctype' => 'multipart/form-data',
                ]
            )
            ->begin(); ?>

        <?= $field->config($form, 'channel')->hiddenInput(); ?>

        <h3 class="h6">Subject and sender</h3>
        <div class="form-text text-muted">What is the subject line of the campaign?</div>
        <div class="mb-4"></div>

        <?= $field->config($form, 'name'); ?>
        <?= $field->config($form, 'sender')
            ->dropDownList($form->getSenderListOptions()); ?>

        <div class="mt-5"></div>
        <h3 class="h6">Content</h3>
        <div class="form-text text-muted">What is the campaign content?</div>
        <div class="mb-4"></div>

        <?= $field->config($form, 'template')
            ->dropDownList($form->getTemplateListOptions()); ?>

        <div class="mt-5"></div>
        <h3 class="h6">Select recipients</h3>
        <div class="form-text text-muted">Who will this campaign be sent to?</div>
        <div class="mb-4"></div>

        <?= $field->config($form, 'groups')
            ->listBox(
                $form->getGroupListOptions(),
                [
                    'class' => 'form-control',
                    'multiple' => true,
                ]
            );
        ?>

        <?= Html::submitButton(
            'Save',
            [
                'class' => 'btn btn-primary float-right mt-2',
            ]
        ); ?>

        <?= Form::end(); ?>
    </div>
</div>
