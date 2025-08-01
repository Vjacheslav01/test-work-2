<?php

/** @var yii\web\View $this */

$this->title = 'TEST WORK';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">TEST WORK</h1>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-md-12">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'id' => 'shorten-form',
                    'options' => ['class' => 'form-inline'],
                ]); ?>
                <div class="form-group" style="width: 80%">
                    <?= \yii\helpers\Html::input('text', 'url', '', [
                        'class' => 'form-control',
                        'placeholder' => 'Введите URL...',
                        'style' => 'width: 100%'
                    ]) ?>
                </div>
                <?= \yii\helpers\Html::submitButton('OK', [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-left: 10px'
                ]) ?>
                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>

        <div class="row" id="result-container" style="display: none; margin-top: 30px">
            <div class="col-md-8 col-md-offset-2 text-center">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Ваша короткая ссылка</h3>
                    </div>
                    <div class="panel-body">
                        <p>
                            <a id="short-url" href="#" target="_blank" class="lead"></a>
                        </p>
                        <div id="qr-code-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>