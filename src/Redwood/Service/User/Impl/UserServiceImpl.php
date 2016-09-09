<?php
namespace Redwood\Service\User\Impl;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Redwood\Service\Common\BaseService;
use Redwood\Service\User\UserService;
use Redwood\Component\OAuthClient\OAuthClientFactory;

use Redwood\Common\ArrayToolkit;
use Redwood\Common\SimpleValidator;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;


class UserServiceImpl extends BaseService implements UserService
{
    public function getUser($id)
    {
        $user = $this->getUserDao()->getUser($id);
        if(!$user){
            return null;
        } else {
            return UserSerialize::unserialize($user);
        }
    }

    public function getUserByEmail($email)
    {
        if (empty($email)) {
            return null;
        }
        $user = $this->getUserDao()->findUserByEmail($email);
        return $user;
        if(!$user){
            return null;
        } else {
            return UserSerialize::unserialize($user);
        }
    }

    public function getUserByUsername($username)
    {
        $user = $this->getUserDao()->findUserByUsername($username);
        if(!$user){
            return null;
        } else {
            return UserSerialize::unserialize($user);
        }
    }

    public function getUserByLoginField($keyword)
    {
        if (SimpleValidator::email($keyword)) {
            $user = $this->getUserDao()->findUserByEmail($keyword);
        } else {
            $user = $this->getUserDao()->findUserByUsername($keyword);
        }

        return !$user ? null : UserSerialize::unserialize($user);
    }

    public function findUsersByIds(array $ids)
    {
        $users = UserSerialize::unserializes(
            $this->getUserDao()->findUsersByIds($ids)
        );
        return ArrayToolkit::index($users, 'id');
    }

    public function register($registration, $type = 'default')
    {
        if (!$this->validateUsername($registration['username'])) {
            throw $this->createServiceException('username error!');
        }

        if (!$this->validateEmail($registration['email'])) {
            throw $this->createServiceException('email error!');
        }

        if (!$this->isEmailAvaliable($registration['email'])) {
            throw $this->createServiceException('Email已存在');
        }

        if (!$this->isUsernameAvaliable($registration['username'])) {
            throw $this->createServiceException('用户名已存在');
        }
        $user = array();
        $user['email'] = $registration['email'];
        $user['username'] = $registration['username'];
        $user['emailVerified'] = 0;
        $user['roles'] =  array('ROLE_USER');
        $user['createdIp'] = empty($registration['createdIp']) ? '' : $registration['createdIp'];

        $user['salt'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        if (in_array($type, array('default', 'phpwind', 'discuz'))) {

            //salt 加密
            $user['password'] = $this->getPasswordEncoder()->encodePassword($registration['password'], $user['salt']);
            // $user['setup']    = 1;
        } elseif (in_array($type, array('qq', 'weibo', 'renren', 'weixinweb', 'weixinmob', 'gitlab')) ) {
            // $user['password'] = '';
            $user['password'] = $this->getPasswordEncoder()->encodePassword($registration['password'], $user['salt']);
            // $user['setup']    = 1;
        } else {
            $user['salt']     = '';
            $user['password'] = '';
            // $user['setup']    = 0;
        }
        $user = UserSerialize::unserialize(
            $this->getUserDao()->addUser(UserSerialize::serialize($user))
        );
        if ($type != 'default') {
            $this->bindUser($type, $registration['token']['userId'], $user['id'], $registration['token']);
        }
        return $user;
    }

    public function bindUser($type, $fromId, $toId, $token)
    {
        $user = $this->getUserDao()->getUser($toId);

        if (empty($user)) {
            throw $this->createServiceException('用户不存在，第三方绑定失败');
        }

        if (!$this->typeInOAuthClient($type)) {
            throw $this->createServiceException("{$type}类型不正确，第三方绑定失败。");
        }

        if ($type == 'weixinweb' || $type == 'weixinmob') {
            $type = 'weixin';
        }

        $this->getUserBindDao()->addBind(array(
            'type'        => $type,
            'fromId'      => $fromId,
            'toId'        => $toId,
            'token'       => empty($token['token']) ? '' : $token['token'],
            'refreshToken'       => empty($token['refreshToken']) ? '' : $token['refreshToken'],
            'createdTime' => time(),
            'expiredTime' => empty($token['expiredTime']) ? 0 : $token['expiredTime']
        ));
    }

    protected function typeInOAuthClient($type)
    {   
        $types = array_keys(OAuthClientFactory::clients());
        return in_array($type, $types);
    }

    private function getPasswordEncoder()
    {
        return new MessageDigestPasswordEncoder('sha256');
    }

    /**
    * 校验是否为邮箱
    */
    private function validateEmail($data)
    {
        $data = (string) $data;
        $valid = filter_var($data, FILTER_VALIDATE_EMAIL);
        return $valid !== false ;
    }

    /**
    * 校验Username是否为真
    */
    private function validateUsername($value, array $option = array())
    {
        $option = array_merge(
            array('minLength' => 4, 'maxLength' => 14),
            $option
        );

        $len = (strlen($value) + mb_strlen($value, 'utf-8')) / 2;
        if ($len > $option['maxLength'] or $len < $option['minLength']) {
            return false;
        }

        return !!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-z0-9_]+$/u', $value);
    }

    public function isUsernameAvaliable($username) 
    {
        if (empty($username)) {
            return false;
        }
        $user = $this->getUserDao()->findUserByUsername($username);
        return empty($user) ? true : false;
    }

    public function isEmailAvaliable($email) 
    {
        if (empty($email)) {
            return false;
        }
        $user = $this->getUserDao()->findUserByEmail($email);
        return empty($user) ? true : false;
    }

    public function makeToken($type, $userId = null, $expiredTime = null, $data = null)
    {
        $token = array();
        $token['type'] = $type;
        $token['userId'] = $userId ? (int)$userId : 0;
        $token['token'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $token['data'] = serialize($data);
        $token['expiredTime'] = $expiredTime ? (int) $expiredTime : 0;
        $token['createdTime'] = time();
        $token = $this->getUserTokenDao()->addToken($token);
        return $token['token'];
    }

    public function getTokenByToken($type, $token)
    {
        $token = $this->getUserTokenDao()->findTokenByToken($token);
        if (empty($token) || $token['type'] != $type) {
            return null;
        }
        if ($token['expiredTime'] > 0 && $token['expiredTime'] < time()) {
            return null;
        }
        $token['data'] = unserialize($token['data']);
        return $token;
    }

    public function deleteToken($type, $token)
    {
        $token = $this->getUserTokenDao()->findTokenByToken($token);
        if (empty($token) || $token['type'] != $type) {
            return false;
        }
        $this->getUserTokenDao()->deleteToken($token['id']);
        return true;
    }

    public function changePassword($id, $password)
    {
        $user = $this->getUser($id);
        if (empty($user) or empty($password)) {
            throw $this->createServiceException('参数不正确，更改密码失败。');
        }

        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $fields = array(
            'salt' => $salt,
            'password' => $this->getPasswordEncoder()->encodePassword($password, $salt),
        );

        $this->getUserDao()->updateUser($id, $fields);

        $this->getLogService()->info('user', 'password-changed', "用户{$user['email']}(ID:{$user['id']})重置密码成功");

        return true;
    }

    public function setEmailVerified($userId)
    {
        $this->getUserDao()->updateUser($userId, array('emailVerified' => 1));
    }

    public function markLoginInfo()
    {
        $user = $this->getCurrentUser();
        if (empty($user)) {
            return ;
        }

        $this->getUserDao()->updateUser($user['id'], array(
            'loginIp' => $user['currentIp'],
            'loginTime' => time(),
        ));

        $this->getLogService()->info('user', 'login_success', '登录成功');
    }

    public function changeUserRoles($id, array $roles)
    {

        $user = $this->getUser($id);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，设置用户角色失败。');
        }

        if (empty($roles)) {
            throw $this->createServiceException('用户角色不能为空');
        }

        if (!in_array('ROLE_USER', $roles)) {
            throw $this->createServiceException('用户角色必须包含ROLE_USER');
        }

        // $allowedRoles = array('ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN','ROLE_TEACHER');

        // $notAllowedRoles = array_diff($roles, $allowedRoles);
        // if (!empty($notAllowedRoles)) {
        //     throw $this->createServiceException('用户角色不正确，设置用户角色失败。');
        // }
        $this->getUserDao()->updateUser($id, UserSerialize::serialize(array('roles' => $roles)));

        $this->getLogService()->info('user', 'change_role', "设置用户{$user['username']}(#{$user['id']})的角色为：" . implode(',', $roles));
    }

    public function lockUser($id)
    {
        $user = $this->getUser($id);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，封禁失败！');
        }
        $this->getUserDao()->updateUser($user['id'], array('locked' => 1));

        $this->getLogService()->info('user', 'lock', "封禁用户{$user['username']}(#{$user['id']})");

        return true;
    }

    public function unlockUser($id)
    {
        $user = $this->getUser($id);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，解禁失败！');
        }
        $this->getUserDao()->updateUser($user['id'], array('locked' => 0));

        $this->getLogService()->info('user', 'unlock', "解禁用户{$user['username']}(#{$user['id']})");

        return true;
    }

    public function searchUsers(array $conditions, array $oderBy, $start, $limit)
    {
        $users = $this->getUserDao()->searchUsers($conditions, $oderBy, $start, $limit);
        return UserSerialize::unserializes($users);
    }


    /**
     *
     * 绑定第三方登录的帐号到系统中的用户帐号
     *
    */
    public function getUserBindByTypeAndFromId($type, $fromId)
    {
        if ($type == 'weixinweb' || $type == 'weixinmob') {
            $type = 'weixin';
        }

        return $this->getUserBindDao()->getBindByTypeAndFromId($type, $fromId);
    }

    public function searchUserCount(array $conditions)
    {
        return $this->getUserDao()->searchUserCount($conditions);
    }

    public function changeAvatar($userId, $filePath, array $options)
    {
        $user = $this->getUser($userId);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，头像更新失败！');
        }

        $avatarRecord = $this->getFileService()->uploadAvatar($filePath, $options);
        $smallAvatarUri = $avatarRecord['smallImageInfo']['directory'].$avatarRecord['smallImageInfo']['filename'];
        $mediumAvatarUri = $avatarRecord['mediumImageInfo']['directory'].$avatarRecord['mediumImageInfo']['filename'];
        $largeAvatarUri = $avatarRecord['largeImageInfo']['directory'].$avatarRecord['largeImageInfo']['filename'];
        
        @unlink($filePath);

        $oldAvatars = array(
            'smallAvatar' => $user['smallAvatar'] ? $this->getFileService()->sqlUriConvertAbsolutUri($user['smallAvatar']) : null,
            'mediumAvatar' => $user['mediumAvatar'] ? $this->getFileService()->sqlUriConvertAbsolutUri($user['mediumAvatar']) : null,
            'largeAvatar' => $user['largeAvatar'] ? $this->getFileService()->sqlUriConvertAbsolutUri($user['largeAvatar']) : null,
        );

        array_map(function($oldAvatar){
            if (!empty($oldAvatar)) {
                @unlink($oldAvatar);
            }
        }, $oldAvatars);

        return  $this->getUserDao()->updateUser($userId, array(
            'smallAvatar'  => $smallAvatarUri,
            'mediumAvatar' => $mediumAvatarUri,
            'largeAvatar'  => $largeAvatarUri,
        ));

    }

    
    protected function getUserBindDao()
    {
        return $this->createDao('User.UserBindDao');
    }

    private function getUserDao()
    {
        return $this->createDao('User.UserDao');
    }

    private function getUserTokenDao()
    {
        return $this->createDao('User.TokenDao');
    }

    protected function getLogService()
    {
        return $this->createService('System.LogService');        
    }

    protected function getFileService()
    {
        return $this->createService('Content.FileService');        
    }

}

class UserSerialize
{
    public static function serialize(array $user)
    {
        $user['roles'] = empty($user['roles']) ? '' :  '|' . implode('|', $user['roles']) . '|';
        return $user;
    }

    public static function unserialize(array $user = null)
    {
        if (empty($user)) {
            return null;
        }
        $user['roles'] = empty($user['roles']) ? array() : explode('|', trim($user['roles'], '|')) ;
     
        return $user;
    }

    public static function unserializes(array $users)
    {
        return array_map(function($user) {
            return UserSerialize::unserialize($user);
        }, $users);
    }

}