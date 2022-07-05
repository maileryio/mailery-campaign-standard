<?php declare(strict_types=1);

use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\Icon\Icon;
use Mailery\Widget\Link\Link;
use Mailery\Web\Widget\DateTimeFormat;
use Yiisoft\Yii\Bootstrap5\Nav;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Standard\Entity\StandardCampaign $campaign */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle($campaign->getName());

$status = $campaign->getStatus();

?><div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-0">Campaign #<?= $campaign->getId(); ?> <span class="badge <?= $status->getCssClass(); ?> align-top ml-2"><?= $status->getLabel(); ?></span></h4>
                        <p class="mt-1 mb-0 small">
                            Changed at <?= DateTimeFormat::widget()->dateTime($campaign->getUpdatedAt()) ?>
                        </p>
                    </div>
                    <div class="col-auto">
                        <div class="btn-toolbar float-right">
                            <?php if ($status->isScheduled()) { ?>
                                <?= Link::widget()
                                    ->csrf($csrf)
                                    ->label(Icon::widget()->name('cancel')->options(['class' => 'mr-1'])->render() . ' Cancel schedule')
                                    ->method('delete')
                                    ->href($url->generate('/campaign/standard/schedule/cancel', ['id' => $campaign->getId()]))
                                    ->confirm('Are you sure?')
                                    ->afterRequest(<<<JS
                                        (res) => {
                                            res.redirected && res.url && (window.location.href = res.url);
                                        }
                                        JS
                                    )
                                    ->options([
                                        'class' => 'btn btn-sm btn-secondary mx-sm-1 mb-2',
                                    ])
                                    ->encode(false);
                                ?>
                            <?php } else { ?>
                                <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $url->generate('/campaign/standard/schedule', ['id' => $campaign->getId()]); ?>">
                                    Schedule
                                </a>
                                <?= Link::widget()
                                    ->csrf($csrf)
                                    ->label('Send immediately')
                                    ->method('post')
                                    ->href($url->generate('/campaign/sendout/create', ['id' => $campaign->getId()]))
                                    ->confirm('Are you sure?')
                                    ->afterRequest(<<<JS
                                        (res) => {
                                            res.redirected && res.url && (window.location.href = res.url);
                                        }
                                        JS
                                    )
                                    ->options([
                                        'class' => 'btn btn-sm btn-primary mx-sm-1 mb-2',
                                    ]);
                                ?>
                            <?php } ?>

                            <b-dropdown right size="sm" variant="secondary" class="mb-2">
                                <template v-slot:button-content>
                                    <?= Icon::widget()->name('settings'); ?>
                                </template>
                                <?= ActivityLogLink::widget()
                                    ->tag('b-dropdown-item')
                                    ->label('Activity log')
                                    ->entity($campaign); ?>
                            </b-dropdown>
                            <div class="btn-toolbar float-right">
                                <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $url->generate('/campaign/default/index'); ?>">
                                    Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($status->isScheduled()) { ?>
                    <div class="mb-4"></div>
                    <div class="row">
                        <div class="col">
                            <div class="alert alert-warning" role="alert">
                                Campaign will be sent on
                                <?= DateTimeFormat::widget()->dateTime($campaign->getSchedule()->getDatetime()) ?>
                                <?= DateTimeFormat::widget()->format('(P)')->dateTime($campaign->getSchedule()->getDatetime()) ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <?= Nav::widget()
                    ->currentPath($currentRoute->getUri()->getPath())
                    ->items([
                        [
                            'label' => 'Overview',
                            'url' => $url->generate($campaign->getViewRouteName(), $campaign->getViewRouteParams()),
                        ],
                        [
                            'label' => 'Details',
                            'url' => $url->generate($campaign->getEditRouteName(), $campaign->getEditRouteParams()),
                        ],
                        [
                            'label' => 'Content',
                            'url' => $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]),
                        ],
                        [
                            'label' => 'Recipients',
                            'url' => $url->generate('/campaign/standard/recipients', ['id' => $campaign->getId()]),
                        ],
                        [
                            'label' => 'Tracking',
                            'url' => $url->generate('/campaign/standard/tracking', ['id' => $campaign->getId()]),
                        ],
                        [
                            'label' => 'Schedule',
                            'url' => $url->generate('/campaign/standard/schedule', ['id' => $campaign->getId()]),
                        ],
                    ])
                    ->options([
                        'class' => 'nav nav-tabs nav-tabs-bordered font-weight-bold',
                    ])
                    ->withoutEncodeLabels();
                ?>

                <div class="mb-4"></div>
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
