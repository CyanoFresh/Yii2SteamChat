<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property integer $id
 * @property string $message
 * @property integer $date
 * @property integer $steamid
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message', 'date', 'steamid'], 'required'],
            [['message', 'steamid'], 'string'],
            [['date'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'date' => 'Date',
            'steamid' => 'Steamid',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['steamid' => 'steamid']);
    }
}
