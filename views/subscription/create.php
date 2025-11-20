<?php
/** @var $this yii\web\View */
/** @var $model app\models\Subscription */
/** @var $author app\models\Author */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Подписка на автора: ' . $author->full_name;
?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>Оставьте номер телефона, чтобы получать SMS о новых книгах автора.</p>

<?php $form = ActiveForm::begin(); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'phone')->textInput(['placeholder' => '+79999999999']) ?>

    <div class="form-group">
        <?= Html::submitButton('Подписаться', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['author/view', 'id' => $author->id], ['class' => 'btn btn-default']) ?>
    </div>

<?php ActiveForm::end(); ?>