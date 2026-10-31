<?php

namespace app\models;

use app\modules\cms\models\AuthUser;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\SiteService;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'message' => '{attribute} không được để trống'],
            ['rememberMe', 'boolean'],
            ['username', 'validateAccount'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, AuthService::$RESPONSES['INCORRECT_EMAIL_PASSWORD']);
            }
        }
    }

    public function validateAccount($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user) {
                if(!$user->confirmed) {
                    $this->addError($attribute, AuthService::$RESPONSES['UNCONFIRMED']);
                    Yii::$app->session->set('unconfirmed-email', $user->access_token);
                } else if (!$user->status || !$user->delete) {
                    $this->addError($attribute, AuthService::$RESPONSES['ACCOUNT_ERROR']);
                }
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
            SiteService::WriteLog(Yii::$app->user->id, SiteService::$ACTIVITIES['LOGIN']);

            return true;
        }
        return false;
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = AuthUser::findByUsername($this->username);
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Tên đăng nhập'),
            'password' => Yii::t('app', 'Mật khẩu'),
            'rememberMe' => Yii::t('app', 'Ghi nhớ tôi?'),
        ];
    }
}
