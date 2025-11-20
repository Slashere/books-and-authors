<?php

namespace app\services;

use Yii;

class SmsService
{
    private string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? Yii::$app->params['smsPilotApiKey'];
    }

    public function send(string $phone, string $message): bool
    {
        $url = 'https://smspilot.ru/api.php';

        $params = [
            'send'   => $message,
            'to'     => $phone,
            'apikey' => $this->apiKey,
            'format' => 'json',
        ];

        $fullUrl = $url . '?' . http_build_query($params);

        // Логируем запрос
        Yii::info("SmsPilot запрос: {$fullUrl}", __METHOD__);

        $ch = curl_init($fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            Yii::error("SmsPilot CURL ошибка: {$curlError}", __METHOD__);
            return false;
        }

        // Логируем ответ API
        Yii::info("SmsPilot ответ: {$response}", __METHOD__);

        // Декодируем JSON и логируем статус
        $decoded = json_decode($response, true);
        if ($decoded === null) {
            Yii::error("SmsPilot невалидный JSON: {$response}", __METHOD__);
            return false;
        }

        // Логируем JSON у поля error
        if (!empty($decoded['error'])) {
            Yii::error('SmsPilot вернул ошибку: ' . var_export($decoded, true), __METHOD__);
            return false;
        }

        return true;
    }
}