<?php

namespace app\models;

use nodge\eauth\ErrorException;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $steamid
 * @property string $username
 * @property string $auth_key
 * @property string $profile_url
 * @property string $avatar
 * @property string $avatar_md
 * @property string $avatar_lg
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        if (Yii::$app->getSession()->has('user-' . $id)) {
            return new self(Yii::$app->getSession()->get('user-' . $id));
        }

        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param \nodge\eauth\ServiceBase $service
     * @return User
     * @throws ErrorException
     */
    public static function findByEAuth($service)
    {
        if (!$service->getIsAuthenticated()) {
            throw new ErrorException('EAuth user should be authenticated before creating identity.');
        }

        $id = $service->getServiceName().'-'.$service->getId();

        $attributes = [
            'id' => $id,
            'steamid' => $service->getAttribute('steamid'),
            'username' => $service->getAttribute('username'),
            'profile_url' => $service->getAttribute('profile_url'),
            'avatar' => $service->getAttribute('avatar'),
            'avatar_md' => $service->getAttribute('avatar_md'),
            'avatar_lg' => $service->getAttribute('avatar_lg'),
        ];

        Yii::$app->getSession()->set('user-'.$id, $attributes);

        return new self($attributes);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['steamid' => 'steamid']);
    }
}

