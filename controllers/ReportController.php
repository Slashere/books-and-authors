<?php

namespace app\controllers;

use yii\web\Controller;
use yii\db\Query;

class ReportController extends Controller
{
    public function actionTopAuthors(?int $year = null): string
    {
        $year = $year ?? (int)date('Y');

        $rows = (new Query())
            ->select([
                'a.id',
                'a.full_name',
                'book_count' => 'COUNT(b.id)'
            ])
            ->from(['a' => 'author'])
            ->innerJoin(['ba' => 'book_author'], 'ba.author_id = a.id')
            ->innerJoin(['b' => 'book'], 'ba.book_id = b.id')
            ->where(['b.year' => $year])
            ->groupBy(['a.id', 'a.full_name'])
            ->orderBy(['book_count' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('top-authors', [
            'year' => $year,
            'rows' => $rows,
        ]);
    }
}