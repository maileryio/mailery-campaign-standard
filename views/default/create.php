<?php declare(strict_types=1);

use Yiisoft\Html\Tag\Form;
use Mailery\Widget\Select\Select;
use Yiisoft\Form\Field;

/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('New standard campaign');

?><div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-0">New standard campaign</h4>
                    </div>
                    <div class="col-auto">
                        <div class="btn-toolbar float-right">
                            <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $url->generate('/campaign/default/index'); ?>">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <?php if ($form->getSender() === null) { ?>
                    <?= Form::tag()
                        ->csrf($csrf)
                        ->id('campaign-form')
                        ->post()
                        ->open(); ?>

                    <h3 class="h6">Select sender</h3>
                    <div class="form-text text-muted">What is the campaign sender?</div>
                    <div class="mb-4"></div>

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

                    <?= Field::submitButton()
                        ->content('Next')
                        ->name('creating-next-step')
                        ->addButtonAttributes([
                            'value' => '1',
                        ]); ?>

                    <?= Form::tag()->close(); ?><?php
                } else {
                    ?><?= $this->render('_form', compact('csrf', 'form')) ?><?php
                } ?>

            </div>
        </div>
    </div>
</div>
