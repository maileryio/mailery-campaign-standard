<?php declare(strict_types=1);

use Mailery\Widget\Select\Select;
use Mailery\Web\Widget\FlashMessage;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;
use Yiisoft\Form\Field;

/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Campaign\Counter\RecipientCounter $recipientCounter */
/** @var Mailery\Campaign\Standard\Entity\StandardCampaign $campaign */
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('Campaign recipients #' . $campaign->getId());

?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-campaign-standard/views/default/_layout.php')
    ->parameters(compact('campaign', 'csrf', 'recipientCounter'))
    ->begin(); ?>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= FlashMessage::widget(); ?>
    </div>
</div>
<div class="mb-2"></div>

<div class="row">
    <div class="col-12">
        <?= Form::tag()
            ->csrf($csrf)
            ->id('campaign-recipients-form')
            ->post()
            ->open(); ?>

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

        <?= $this->render('_submit-button', compact('campaign')) ?>

        <?= Form::tag()->close(); ?>
    </div>
</div>

<?= ContentDecorator::end() ?>
