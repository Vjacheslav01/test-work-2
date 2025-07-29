<?php
namespace app\models;

use http\Exception\RuntimeException;
use Yii;
use yii\db\ActiveRecord;

class UrlLog extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%url_log}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['url_id', 'ip_address'], 'required'],
            [['url_id'], 'integer'],
            [['accessed_at'], 'safe'],
            [['ip_address'], 'string', 'max' => 45],
        ];
    }

    /**
     * @param $urlId
     * @return true
     * @throws \yii\db\Exception
     */
    public static function addLog($urlId)
    {
        $log = new self();

        $log->setAttributes([
            'url_id' => $urlId,
            'ip_address' => Yii::$app->request->getUserIP()
        ]);
        if ($log->save()) {
            return true;
        }
        throw new RuntimeException('Failed to save log');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUrl()
    {
        return $this->hasOne(Url::class, ['id' => 'url_id']);
    }
}