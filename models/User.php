<?php

namespace app\models;
use Yii;
class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
	public $role;
	public $fio;

    private static $users = [];
	
	
	
	public static function isini (){
		if(count(self::$users) == 0){			
			self::$users = Yii::$app->db->createCommand('SELECT * FROM users')->queryAll();
			//var_dump(self::$users);
		}
		
	}

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {	self::isini();
		foreach (self::$users as $user) {
			if($user['id'] == $id){
				return isset($user) ? new static($user) : null;
			}
		}
    }
	
	
    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {	self::isini();
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {	self::isini();
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {	self::isini();
        return $this->id;
    }
	/**
     * {@inheritdoc}
     */
	public function getAll(){
		self::isini();		
		return self::$users;		
	}

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {	self::isini();
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {	self::isini();
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {	self::isini();
        return $this->password === $password;
    }
}
