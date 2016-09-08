<?php
namespace Redwood\Component\OAuthClient;

class GitlabOAuthClient extends AbstractOAuthClient
{
    CONST AUTHORIZE_URL = 'http://gitlab.10jqka.com.cn/oauth/authorize?';
    CONST OAUTH_TOKEN_URL = 'http://gitlab.10jqka.com.cn/oauth/token';
    CONST OAUTH_ME_URL = 'http://gitlab.10jqka.com.cn/api/v3/user';

    public function getAuthorizeUrl($callbackUrl)
    {
        $params = array();
        $params['response_type'] = 'code';
        $params['client_id'] = $this->config['key'];
        $params['redirect_uri'] = $callbackUrl;
        return self::AUTHORIZE_URL . http_build_query($params);
    }

    public function getAccessToken($code, $callbackUrl)
    {   
        $params = array(
            'client_id' => $this->config['key'], 
            'client_secret' => $this->config['secret'], 
            'grant_type' => 'authorization_code', 
            'authorization_code' => 'code', 
            'redirect_uri' => $callbackUrl, 
            'code' => $code
        );
      
        $result = $this->postRequest(self:: OAUTH_TOKEN_URL, $params);
        $token = json_decode($result, true);
        $r = array();

        $user = $this->getUserInfo($token);
        $r['userId'] = $user['id'];
        $r['token'] = $token['access_token'];
        $r['access_token'] = $token['access_token'];
        $r['refreshToken'] = $token['refresh_token'];
        $r['createdTime'] = $token['created_at'];
        return $r;
    }

    public function getUserInfo($token)
    {
        $params = array('access_token' => $token['access_token']);
        $result = $this->getRequest(self::OAUTH_ME_URL, $params);
        $user = json_decode($result, true);
        return $this->convertUserInfo($user);
    }

    protected function convertUserInfo($infos)
    {
        $userInfo = array();
        $userInfo['id'] = $infos['id'];
        $userInfo['username'] = $infos['username'];
        // $userInfo['nickname'] = $infos['name'];
        $userInfo['email'] = $infos['email'];
        $userInfo['avatar'] = $infos['avatar_url'];
        return $userInfo;
    }

    public function getClientInfo()
    {
        return array(
            'type' => 'gitlab',
            'name' => 'THS-gitlab',
        );
    }
}