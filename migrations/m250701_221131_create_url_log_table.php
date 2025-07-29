<?php

use app\models\Url;
use app\models\UrlLog;
use yii\db\Migration;

class m250701_221131_create_url_log_table extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        $this->createTable(UrlLog::tableName(), [
            'id' => $this->primaryKey(),
            'url_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(45)->notNull(),
            'accessed_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-url_log-url_id',
            UrlLog::tableName(), 'url_id',
            Url::tableName(), 'id',
            'CASCADE'
        );

        $this->createIndex('idx-url_log-url_id', UrlLog::tableName(), 'url_id');
        $this->createIndex('idx-url_log-accessed_at', UrlLog::tableName(), 'accessed_at');
        $this->createIndex('idx-url_log-ip_address', UrlLog::tableName(), 'ip_address');
        $this->createIndex('idx-url_log-url_id_accessed_at', UrlLog::tableName(), ['url_id', 'accessed_at']);
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-url_log-url_id', UrlLog::tableName());

        $this->dropIndex('idx-url_log-url_id_accessed_at', UrlLog::tableName());
        $this->dropIndex('idx-url_log-ip_address', UrlLog::tableName());
        $this->dropIndex('idx-url_log-accessed_at', UrlLog::tableName());
        $this->dropIndex('idx-url_log-url_id', UrlLog::tableName());

        $this->dropTable(UrlLog::tableName());
    }
}