<?php declare(strict_types=1);

use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\Icon\Icon;
use Mailery\Widget\Link\Link;
use Mailery\Widget\Dataview\DetailView;
use Mailery\Campaign\Standard\Entity\StandardCampaign;
use Mailery\Subscriber\Entity\Group;
use Yiisoft\Html\Html;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Standard\Entity\StandardCampaign $campaign */
/** @var string $csrf */
/** @var bool $submitted */

$this->setTitle($campaign->getName());

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h3">Campaign #<?= $campaign->getId(); ?></h1>
            <div class="btn-toolbar float-right">
                <?= Link::widget()
                    ->label((string) Icon::widget()->name('delete')->options(['class' => 'mr-1']) . ' Delete')
                    ->method('delete')
                    ->href($urlGenerator->generate('/campaign/default/delete', ['id' => $campaign->getId()]))
                    ->confirm('Are you sure?')
                    ->options([
                        'class' => 'btn btn-sm btn-danger mx-sm-1 mb-2',
                        'encode' => false,
                    ]);
                ?>
                <a class="btn btn-sm btn-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate($campaign->getEditRouteName(), $campaign->getEditRouteParams()); ?>">
                    <?= Icon::widget()->name('pencil')->options(['class' => 'mr-1']); ?>
                    Update
                </a>
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
                    <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/campaign/default/index'); ?>">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12 grid-margin">
        <?= DetailView::widget()
            ->data($campaign)
            ->options([
                'class' => 'table detail-view',
            ])
            ->emptyText('(not set)')
            ->emptyTextOptions([
                'class' => 'text-muted',
            ])
            ->attributes([
                [
                    'label' => 'Name',
                    'value' => function (StandardCampaign $data, $index) {
                        return $data->getName();
                    },
                ],
                [
                    'label' => 'Template',
                    'value' => function (StandardCampaign $data, $index) use($urlGenerator) {
                        return Html::a(
                            $data->getTemplate()->getName(),
                            $urlGenerator->generate($data->getTemplate()->getViewRouteName(), $data->getTemplate()->getViewRouteParams())
                        );
                    },
                ],
                [
                    'label' => 'Groups',
                    'value' => function (StandardCampaign $data, $index) use($urlGenerator) {
                        return implode(
                            '<br />',
                            array_map(
                                function (Group $group) use($urlGenerator) {
                                    return Html::a(
                                        $group->getName(),
                                        $urlGenerator->generate($group->getViewRouteName(), $group->getViewRouteParams())
                                    );
                                },
                                $data->getGroups()->toArray()
                            )
                        );
                    },
                ],
            ]);
        ?>
    </div>
</div>
