<?php

namespace app\controllers;

use app\models\Url;
use app\models\UrlLog;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return array|string[]
     * @throws \Random\RandomException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionShorten()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $url = trim(Yii::$app->request->post('url'));

        // Валидация URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['status' => 'error', 'message' => 'Некорректный URL'];
        }

        // Проверка доступности
        if (!$this->isUrlAccessible($url)) {
            return ['status' => 'error', 'message' => 'URL недоступен'];
        }

        $model = Url::findOne(['original_url' => $url]);

        if (!$model) {
            $model = Url::generateQr($url);
            if (!$model) {
                return ['status' => 'error', 'message' => 'Ошибка сохранения'];
            }
        }

        // Генерация QR-кода
        $shortUrl = implode('/', [Yii::$app->request->hostInfo, $model->short_code]);
        $qrCode = $model->generateQrCode($shortUrl);

        return [
            'status' => 'success',
            'shortUrl' => $shortUrl,
            'qrCode' => $qrCode,
        ];
    }

    /**
     * @param string $code
     * @return Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRedirect(string $code)
    {
        $url = Url::findOne(['short_code' => $code]);

        if ($url) {
            // Увеличиваем счетчик
            $url->updateCounters(['counter' => 1]);

            // Логируем переход
            UrlLog::addLog($url->id);

            return $this->redirect($url->original_url);
        }
        throw new \yii\web\NotFoundHttpException('Ссылка не найдена');
    }

    /**
     * @param $url
     * @return bool
     */
    private function isUrlAccessible($url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 400;
    }
}
