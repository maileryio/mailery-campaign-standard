<?php declare(strict_types=1);

use Mailery\Icon\Icon;
use Mailery\Web\Widget\ByteUnitsFormat;
use Mailery\Web\Widget\BooleanBadge;
use Mailery\Campaign\Standard\Entity\StandardCampaign as Campaign;
use Mailery\Subscriber\Entity\Group;
use Mailery\Widget\Link\Link;
use Symfony\Component\Mime\Address;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Form\Field;
use Mailery\Web\Vue\Directive;

/** @var Yiisoft\Assets\AssetManager $assetManager */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Counter\RecipientCounter $recipientCounter */
/** @var Mailery\Campaign\Standard\Entity\StandardCampaign $campaign */
/** @var Mailery\Campaign\Form\SendTestForm $testForm */
/** @var Mailery\Subscriber\Counter\SubscriberCounter $subscriberCounter */
/** @var Yiisoft\Yii\View\Csrf $csrf */
/** @var Yiisoft\Translator\TranslatorInterface $translator */

$this->setTitle($campaign->getName());

?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-campaign-standard/views/default/_layout.php')
    ->parameters(compact('campaign', 'csrf', 'recipientCounter'))
    ->begin(); ?>

<div class="mb-2"></div>
<div class="row">
    <div class="col-md">
        <h6 class="font-weight-bold">General details</h6>
    </div>
    <div class="col-auto">
        <div class="btn-toolbar float-right">
            <?php if ($campaign->canBeEdited()) { ?>
                <a class="btn btn-outline-secondary float-right" href="<?= $url->generate($campaign->getEditRouteName(), $campaign->getEditRouteParams()); ?>">
                    Edit details
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= DetailView::widget()
            ->model($campaign)
            ->options([
                'class' => 'table table-top-borderless detail-view',
            ])
            ->emptyValue('<span class="text-muted">(not set)</span>')
            ->attributes([
                [
                    'label' => 'Subject',
                    'value' => function (Campaign $data) {
                        return Directive::pre($data->getName());
                    },
                ],
                [
                    'label' => 'From',
                    'value' => function (Campaign $data) {
                        return Directive::pre((new Address($data->getSender()->getEmail(), $data->getSender()->getName()))->toString());
                    },
                ],
                [
                    'label' => 'Reply to',
                    'value' => function (Campaign $data) {
                        return Directive::pre((new Address($data->getSender()->getReplyEmail(), $data->getSender()->getReplyName()))->toString());
                    },
                ],
            ]);
        ?>
    </div>
</div>

<hr class="mb-4"/>
<div class="row">
    <div class="col-md">
        <h6 class="font-weight-bold">Content</h6>
    </div>
    <div class="col-auto">
        <div class="btn-toolbar float-right">
            <?php if ($campaign->canBeEdited()) { ?>
                <a class="btn btn-outline-secondary float-right" href="<?= $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]); ?>">
                    Edit content
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <div class="camp-view-content d-flex">
            <!--<?= $assetManager->getAssetUrl(Mailery\Campaign\Assets\CampaignAssetBundle::class, 'images/empty-image-bg.svg') ?>-->
            <div class="camp-view-content__thumbnail-preview">
                <iframe
                    src="<?= $url->generate('/campaign/standard/preview', ['id' => $campaign->getId()]) ?>"
                    scrolling="no">
                </iframe>

                <div class="camp-view-content__thumbnail-preview--overlay d-flex justify-content-center align-items-center" v-b-modal.modal-preview-html>
                    <div class="flex-grow-1 text-center">
                        <?= Icon::widget()->name('magnify'); ?>
                        <p class="font-weight-bold">Preview</p>
                    </div>
                </div>
            </div>

            <div class="camp-view-content__details ml-3">
                <ul class="list-unstyled mt-2">
                    <li>
                        <p class="d-flex align-items-center mb-0">
                            <?= Icon::widget()->name('check-circle-outline')->options(['class' => 'text-success h5']) ?>
                            <span class="mb-2 ml-2">A plain-text version of this email will be included: <a href="javascript:void(0);" v-b-modal.modal-preview-text>Preview</a></span>
                        </p>
                    </li>
                    <li>
                        <p class="d-flex align-items-center mb-0">
                            <?= Icon::widget()->name('check-circle-outline')->options(['class' => 'text-success h5']) ?>
                            <span class="mb-2 ml-2">
                                Total size of one email: <?= ByteUnitsFormat::widget()->string($campaign->getTemplate()->getHtmlContent()) ?>
                                <?= Icon::widget()
                                    ->name('help-circle-outline')
                                    ->options([
                                        'id' => $tooltipTarget = 'tooltip-' . uniqid(),
                                    ]); ?>

                                <b-tooltip target="<?= $tooltipTarget ?>">
                                    Total size does not take into account third-party resources, including images.
                                </b-tooltip>
                            </span>
                        </p>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mb-2"></div>

        <?php if ($campaign->canBeEdited()) { ?>
            <div class="camp-view-content__send-test mt-3">
                <a class="font-weight-bold" href="javascript:void(0);" v-b-modal.modal-send-test>Send test email</a>
            </div>

            <ui-listener event="campaign.send-test" v-slot="{ data }">
                <b-modal id="modal-send-test" title="Test send this campaign" @hidden="() => { mailery.app.$emit('campaign.send-test', {}) }">
                    <template #modal-footer="{ ok }">
                        <?= Link::widget()
                            ->csrf($csrf)
                            ->label(<<<TEXT
                                <b-spinner v-if="data.loading" small></b-spinner>
                                <span v-if="data.loading">Sending...</span>
                                <span v-else>Send email</span>
                                TEXT
                            )
                            ->disabled('data.loading')
                            ->options([
                                'class' => 'btn btn-primary',
                            ])
                            ->createRequest(<<<JS
                                () => {
                                    const form = document.getElementById('campaign-test-form');

                                    return new Request(
                                        '{$url->generate('/campaign/sendout/test', ['id' => $campaign->getId()])}',
                                        {
                                            method: 'POST',
                                            body: new URLSearchParams(new FormData(form))
                                        }
                                    );
                                }
                                JS
                            )
                            ->beforeRequest(<<<JS
                                (req) => {
                                    const form = document.getElementById('campaign-test-form');
                                    const isValid = form.reportValidity();

                                    if (isValid) {
                                        mailery.app.\$emit('campaign.send-test', { loading: true });
                                    }
                                    return isValid;
                                }
                                JS
                            )
                            ->afterRequest(<<<JS
                                (res) => {
                                    if (res.redirected && res.url) {
                                        window.location.href = res.url;
                                        return;
                                    } else {
                                        res.json().then((data) => {
                                            mailery.app.\$emit('campaign.send-test', { loading: false, success: data.success, message: data.message });
                                        });
                                    }
                                }
                                JS
                            )
                            ->encode(false);
                        ?>
                    </template>

                    <b-alert :show="'success' in data" :variant="data.success ? 'success' : 'danger'">
                        {{ data.message }}
                    </b-alert>

                    <?= Form::tag()
                        ->csrf($csrf)
                        ->id('campaign-test-form')
                        ->post($url->generate('/campaign/sendout/test', ['id' => $campaign->getId()]))
                        ->open(); ?>

                    <?= Field::text($testForm, 'recipients'); ?>
                    <div class="form-text text-muted">Enter addresses, separated by a commas, ex. <?= Html::encode('"Bob Smith" <bob@company.com>, joe@company.com') ?></div>

                    <?= Form::tag()->close(); ?>
                </b-modal>
            </ui-listener>
        <?php } ?>

        <b-modal id="modal-preview-html" title="Design preview">
            <template #modal-footer="{ cancel }">
                <b-button variant="outline-secondary" @click="cancel()">Cancel</b-button>
                <a class="btn btn-primary" href="<?= $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]) ?>">Edit content</a>
            </template>

            <iframe src="<?= $url->generate('/campaign/standard/preview', ['id' => $campaign->getId()]) ?>"></iframe>
        </b-modal>

        <b-modal id="modal-preview-text" title="Text preview">
            <template #modal-footer="{ cancel }">
                <b-button variant="outline-secondary" @click="cancel()">Cancel</b-button>
                <a class="btn btn-primary" href="<?= $url->generate('/campaign/standard/content', ['id' => $campaign->getId()]) ?>">Edit content</a>
            </template>

            <?= nl2br((string) Directive::pre($campaign->getTemplate()->getTextContent())); ?>
        </b-modal>
    </div>
</div>

<hr class="mb-4"/>
<div class="row">
    <div class="col-md">
        <h6>
            <span class="font-weight-bold">Recipients</span>
            <span class="ml-2"><?= $subscriberCounter->getActiveCount() ?> <span class="text-muted">/ <?= $subscriberCounter->getTotalCount() ?></span></span>
            <?= Icon::widget()
                ->name('help-circle-outline')
                ->options([
                    'id' => $tooltipTarget = 'tooltip-' . uniqid(),
                ]); ?>

            <b-tooltip target="<?= $tooltipTarget ?>">
                Active contacts who will receive the newsletter / all contacts.
                <br/>
                The newsletter will not be received by inactive contacts and those who have unsubscribed. Even if the subscriber belongs to more than one group, he will receive the email only once.
            </b-tooltip>
        </h6>
    </div>
    <div class="col-auto">
        <div class="btn-toolbar float-right">
            <?php if ($campaign->canBeEdited()) { ?>
                <a class="btn btn-outline-secondary float-right" href="<?= $url->generate('/campaign/standard/recipients', ['id' => $campaign->getId()]); ?>">
                    Edit recipients
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= GridView::widget()
            ->layout("{items}\n<div class=\"mb-4\"></div>\n<div class=\"float-right\">{pager}</div>")
            ->options([
                'class' => 'table-responsive',
            ])
            ->tableOptions([
                'class' => 'table table-hover',
            ])
            ->emptyText('No data')
            ->emptyTextOptions([
                'class' => 'text-center text-muted mt-4 mb-4',
            ])
            ->paginator(new OffsetPaginator(new IterableDataReader($campaign->getGroups())))
            ->columns([
                [
                    'label()' => ['Name'],
                    'value()' => [fn (Group $model) => Html::a(Directive::pre($model->getName()), $url->generate($model->getViewRouteName(), $model->getViewRouteParams()))],
                ],
                [
                    'label()' => ['Active'],
                    'value()' => [fn (Group $model) => $subscriberCounter->withGroup($model)->getActiveCount()],
                    'emptyValue()' => ['0'],
                ],
                [
                    'label()' => ['Unconfirmed'],
                    'value()' => [fn (Group $model) => $subscriberCounter->withGroup($model)->getUnconfirmedCount()],
                    'emptyValue()' => ['0'],
                ],
                [
                    'label()' => ['Unsubscribed'],
                    'value()' => [fn (Group $model) => $subscriberCounter->withGroup($model)->getUnsubscribedCount()],
                    'emptyValue()' => ['0'],
                ],
                [
                    'label()' => ['Bounced'],
                    'value()' => [fn (Group $model) => $subscriberCounter->withGroup($model)->getBouncedCount()],
                    'emptyValue()' => ['0'],
                ],
                [
                    'label()' => ['Marked as spam'],
                    'value()' => [fn (Group $model) => $subscriberCounter->withGroup($model)->getComplaintCount()],
                    'emptyValue()' => ['0'],
                ],
            ]);
        ?>
    </div>
</div>

<hr class="mb-4"/>
<div class="row">
    <div class="col-md">
        <h6 class="font-weight-bold">Tracking settings</h6>
    </div>
    <div class="col-auto">
        <div class="btn-toolbar float-right">
            <?php if ($campaign->canBeEdited()) { ?>
                <a class="btn btn-outline-secondary float-right" href="<?= $url->generate('/campaign/standard/tracking', ['id' => $campaign->getId()]); ?>">
                    Edit tracking
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <table class="table table-top-borderless">
            <tbody>
                <tr>
                    <td>Track clicks</td>
                    <td>
                        <?= BooleanBadge::widget()->value($campaign->getTrackClicks()) ?>
                    </td>
                </tr>
                <tr>
                    <td>Track opens</td>
                    <td>
                        <?= BooleanBadge::widget()->value($campaign->getTrackOpens()) ?>
                    </td>
                </tr>
                <tr>
                    <td>Enable UTM tags</td>
                    <td>
                        <?= BooleanBadge::widget()->value($campaign->getEnableUtmTags()) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= ContentDecorator::end() ?>
