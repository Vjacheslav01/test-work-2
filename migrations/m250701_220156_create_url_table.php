<?php
use yii\db\Migration;
use app\models\Url;

class m250701_220156_create_url_table extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        $this->createTable(Url::tableName(), [
            'id' => $this->primaryKey(),
            'original_url' => $this->text()->notNull(),
            'short_code' => $this->string(10)->notNull()->unique(),
            'counter' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $this->dropTable(Url::tableName());
    }
}