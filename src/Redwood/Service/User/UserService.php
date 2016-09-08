<?php
namespace Redwood\Service\User;

interface UserService
{
    public function getUser($id);
    
	public function getUserByEmail($email);

	public function getUserByUsername($username);

    public function findUsersByIds(array $ids);

    public function isUsernameAvaliable($username);

    public function isEmailAvaliable($email);


    /**
     * 用户注册
     * @param  [type] $registration 用户注册信息
     * @return array 用户信息
     */
    public function register($registration, $type = 'default');

    public function makeToken($type, $userId = null, $expiredTime = null, $data = null);

    public function getTokenByToken($type, $token);

    public function setEmailVerified($userId);

    public function deleteToken($type, $token);

    public function changePassword($id, $password);

    public function markLoginInfo();

    public function changeUserRoles($id, array $roles);

    public function lockUser($id);

    public function unlockUser($id);

    public function searchUserCount(array $conditions);

    public function searchUsers(array $conditions, array $oderBy, $start, $limit);

    /**
     *
     * 绑定第三方登录的帐号到系统中的用户帐号
     *
     */
    // public function bindUser($type, $fromId, $toId, $token);

    public function getUserBindByTypeAndFromId($type, $fromId);

    // public function getUserBindByTypeAndUserId($type, $toId);

    // public function getUserBindByToken($token);

    // public function findBindsByUserId($userId);

    // public function unBindUserByTypeAndToId($type, $toId);

}