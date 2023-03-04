<?php

use Yiisoft\Form\Field;

if ($campaign->canBeEdited()) {
    echo Field::submitButton()
        ->content('Save changes');
} else { ?>
    <span id="disabled-wrapper" class="d-inline-block float-right mt-2" tabindex="0">
        <?= Field::submitButton()
            ->content('Save changes')
            ->disabled(!$campaign->canBeEdited()); ?>
    </span>
    <b-tooltip target="disabled-wrapper">
        Campaign cannot be edited because it has already been submitted
    </b-tooltip>
<?php } ?>
