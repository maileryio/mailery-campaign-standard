<?php declare(strict_types=1);

use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\Icon\Icon;
use Mailery\Widget\Link\Link;
use Mailery\Web\Widget\DateTimeFormat;
use Mailery\Widget\Highcharts\Highcharts;
use Yiisoft\Yii\Bootstrap5\Nav;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Counter\RecipientCounter $recipientCounter */
/** @var Mailery\Campaign\Standard\Entity\StandardCampaign $campaign */
/** @var Yiisoft\Translator\TranslatorInterface $translator */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle($campaign->getName());

$status = $campaign->getStatus();
$sendout = $campaign->getLastDefaultSendout();

if ($sendout !== null) {
    $recipientCounter = $recipientCounter->withSendout($sendout);
}

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
                        <?php if ($sendout !== null) { ?>
                            <p class="mt-3 mb-0">
                                Sent on <?= DateTimeFormat::widget()->dateTime($sendout->getCreatedAt()) ?> to <b><?= $recipientCounter->getSentCount() ?></b> / <?= $recipientCounter->getTotalCount() ?> subscribers
                                <?= Icon::widget()
                                    ->name('help-circle-outline')
                                    ->options([
                                        'id' => $tooltipTarget = 'tooltip-' . uniqid(),
                                    ]); ?>

                                <b-tooltip target="<?= $tooltipTarget ?>">
                                    Active contacts to whom the campaign was sent / all active contacts.
                                </b-tooltip>
                            </p>
                        <?php } ?>
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
                            <?php } else if($status->isSent()) { ?>
                                <a class="btn btn-sm btn-primary mx-sm-1 mb-2" href="<?= $url->generate('/campaign/standard/report', ['id' => $campaign->getId()]); ?>">
                                    See report
                                </a>
                            <?php } else if($status->isDraft()) { ?>
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
                <?php } else if($sendout !== null) { ?>
                    <?php if ($sendout->getStatus()->isErrored()) { ?>
                        <div class="mb-4"></div>
                        <div class="row">
                            <div class="col">
                                <div class="alert alert-danger" role="alert">
                                    <?= $sendout->getError(); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="mb-4"></div>
                    <div class="row">
                        <div class="col-md-5">
                            <?= Highcharts::widget()
                                ->options([
                                    'chart' => [
                                        'type' => 'pie',
                                        'height' => 300,
                                    ],
                                    'title' => [
                                        'text' => '<small><b>Top 10 countries</b></small>',
                                        'verticalAlign' => 'bottom',
                                        'useHTML' => true,
                                    ],
                                    'tooltip' => [
                                        'headerFormat' => '',
                                        'pointFormat' => '{point.custom.tooltip}',
                                    ],
                                    'credits' => [
                                        'enabled' => false,
                                    ],
                                    'plotOptions' => [
                                        'pie' => [
                                            'size' => 200,
                                            'allowPointSelect' => true,
                                            'cursor' => 'pointer',
                                            'dataLabels' => [
                                                'enabled' => true,
                                                'format' => '<b>{point.name}</b>: {point.percentage:.1f} %',
                                            ],
                                        ],
                                    ],
                                    'series' => [
                                        [
                                            'type' => 'pie',
                                            'name' => 'Countries',
                                            'data' => [
                                                [
                                                    'name' => 'United States',
                                                    'y' => 117,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 1]),
                                                    ],
                                                    'sliced' => true,
                                                    'selected' => true,
                                                ],
                                                [
                                                    'name' => 'Not defined',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                                [
                                                    'name' => 'United Kingdom',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                                [
                                                    'name' => 'United Kingdom',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                                [
                                                    'name' => 'United Kingdom',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                                [
                                                    'name' => 'United Kingdom',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                                [
                                                    'name' => 'United Kingdom',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                                [
                                                    'name' => 'United Kingdom',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                                [
                                                    'name' => 'United Kingdom',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                                [
                                                    'name' => 'United Kingdom',
                                                    'y' => 33,
                                                    'custom' => [
                                                        'tooltip' => $translator->translate('<b>{name}</b>: {count, number} {count, plural, one{contact} other{contacts}}', ['name' => 'United Kingdom', 'count' => 33]),
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ]); ?>
                        </div>
                        <div class="col-md-7">
                            <?= Highcharts::widget()
                                ->options([
                                    'chart' => [
                                        'type' => 'bar',
                                        'height' => 300,
                                    ],
                                    'title' => [
                                        'text' => '<small><b>Activity</b></small>',
                                        'verticalAlign' => 'bottom',
                                        'useHTML' => true,
                                    ],
                                    'xAxis' => [
                                        'visible' => false,
                                    ],
                                    'yAxis' => [
                                        'title' => '',
                                        'min' => 0,
                                        'allowDecimals' => false,
                                    ],
                                    'tooltip' => [
                                        'headerFormat' => '',
                                        'pointFormat' => '{series.name}: {point.y}',
                                    ],
                                    'credits' => [
                                        'enabled' => false,
                                    ],
                                    'plotOptions' => [
                                        'bar' => [
                                            'borderWidth' => 0,
                                            'shadow' => false,
                                            'groupPadding' => 0,
                                            'cursor' => 'pointer',
                                            'dataLabels' => [
                                                'enabled' => true,
                                                'style' => [
                                                    'fontWeight' => 'normal',
                                                    'color' => "#797979",
                                                ],
                                            ],
                                        ],
                                    ],
                                    'series' => [
                                        [
                                            'name' => 'Recipients',
                                            'data' => [
                                                $recipientCounter->getSentCount(),
                                            ],
                                            'color' => '#6c757d',
                                        ],
                                        [
                                            'name' => 'Opened',
                                            'data' => [
                                                $recipientCounter->getOpenedCount(),
                                            ],
                                            'color' => '#28a745',
                                        ],
                                        [
                                            'name' => 'Unopened',
                                            'data' => [
                                                $recipientCounter->getSentCount() - $recipientCounter->getOpenedCount(),
                                            ],
                                            'color' => '#ffc107',
                                        ],
                                        [
                                            'name' => 'Clicked',
                                            'data' => [
                                                $recipientCounter->getClickedCount(),
                                            ],
                                            'color' => '#007bff',
                                        ],
                                        [
                                            'name' => 'Unsubscribed',
                                            'data' => [
                                                $recipientCounter->getUnsubscribedCount(),
                                            ],
                                            'color' => '#dc3545',
                                        ],
                                        [
                                            'name' => 'Bounced',
                                            'data' => [
                                                $recipientCounter->getBouncedCount(),
                                            ],
                                            'color' => '#343a40',
                                        ],
                                        [
                                            'name' => 'Marked as spam',
                                            'data' => [
                                                $recipientCounter->getComplainedCount(),
                                            ],
                                            'color' => '#343a40',
                                        ],
                                    ],
                                ]); ?>
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
                    ->items(array_filter([
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
                        $sendout !== null
                            ? [
                                'label' => 'Report',
                                'url' => $url->generate('/campaign/standard/report', ['id' => $campaign->getId()]),
                            ]
                            : null,
                    ]))
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
