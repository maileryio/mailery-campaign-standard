<?php

/** @var Yiisoft\Form\Widget\Field $field */

if ($campaign->canBeEdited()) {
    echo $field->submitButton()
        ->class('btn btn-primary float-right mt-2')
        ->value('Save changes');
} else { ?>
    <span id="disabled-wrapper" class="d-inline-block float-right mt-2" tabindex="0">
        <?= $field->submitButton()
            ->class('btn btn-primary')
            ->disabled()
            ->value('Save changes'); ?>
    </span>
    <b-tooltip target="disabled-wrapper">
        Campaign cannot be modified in read only status
    </b-tooltip>
<?php } ?>
