<div class="admin-default-index">
<?php 
use yii\grid\GridView;
use yii\widgets\DetailView;
echo "<h1>Валюта</h1>";

echo DetailView::widget([
    'model' => $currency,
    'attributes' => [
        'id',
        'cbr_id',
        'cbr_numcode',
        'cbr_charcode',
        'name'
    ],
]);

echo "<h1>История валюты</h1>";

echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 
            'currency_nominal',
            'currency_value',
            [
                'attribute' => 'update',
                'format' =>  ['date', 'php:d-m-Y'],
                'options' => ['width' => '200']
            ],
            
        ],
    ]); ?>
</div>