<?php
use yii\helpers\Html;

/* @var $year integer */
/* @var $rows array */

echo "<h1>Топ 10 авторов за {$year} год</h1>";

echo Html::beginForm(['report/top-authors'], 'get');
echo Html::label('Год', 'year');
echo Html::textInput('year', $year, [
    'id' => 'year',
    'class' => 'form-control',
    'style' => 'max-width: 150px; display: inline-block; margin-right: 10px;',
]);
echo Html::submitButton('Показать', ['class' => 'btn btn-primary']);
echo Html::endForm();

if (empty($rows)) {
    echo "<p>Нет данных.</p>";
} else {
    echo "<table class='table table-bordered'>";
    echo "<tr>
            <th>#</th>
            <th>Автор</th>
            <th>Количество книг</th>
            <th>Подписка</th>
          </tr>";

    $i = 1;
    foreach ($rows as $row) {
        echo "<tr>";
        echo "<td>" . $i++ . "</td>";
        echo "<td>" . Html::encode($row['full_name']) . "</td>";
        echo "<td>" . (int)$row['book_count'] . "</td>";
        echo "<td>"
            . Html::a(
                'Подписаться',
                ['subscription/create', 'author_id' => $row['id']],
                ['class' => 'btn btn-sm btn-success']
            )
            . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

