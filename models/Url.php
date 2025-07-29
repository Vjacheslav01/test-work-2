<?php
namespace app\models;

use Da\QrCode\QrCode;
use Da\QrCode\Writer\PngWriter;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class Url extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%url}}';
    }

    public function rules()
    {
        return [
            [['original_url', 'short_code'], 'required'],
            [['created_at'], 'safe'],
            [['counter'], 'integer'],
            [['original_url'], 'string'],
            [['short_code'], 'string', 'max' => 10],
            [['short_code'], 'unique'],
        ];
    }

    /**
     * @param $url
     * @return mixed
     * @throws ServerErrorHttpException
     * @throws \Random\RandomException
     * @throws \yii\db\Exception
     */
    public static function generateQr($url): mixed
    {
        $model = new self();
        $model->setAttributes([
            'original_url' => $url,
            'short_code' => self::generateUniqueShortCode()
        ]);
        if ($model->save()) {
            return $model;
        }
        throw new ServerErrorHttpException('Failed to create QR Code.');
    }

    /**
     * @param $length
     * @return string
     * @throws \Random\RandomException
     */
    public static function generateUniqueShortCode($length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($characters) - 1;

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $max)];
            }
        } while (self::find()->where(['short_code' => $code])->exists());
        return $code;
    }

    /**
     * @param $url
     * @param $size
     * @param $margin
     * @param $background
     * @return string
     * @throws \Da\QrCode\Exception\BadMethodCallException
     * @throws \Da\QrCode\Exception\ValidationException
     */
    public function generateQrCode($url, $size = 250, $margin = 5, $background = [255, 255, 255]): string
    {
        $qrCode = (new QrCode($url))
            ->setSize($size)
            ->setMargin($margin)
            ->setBackgroundColor($background[0], $background[1], $background[2]);

        return $qrCode->writeDataUri();
    }
}