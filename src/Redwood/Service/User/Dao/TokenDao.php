<?php

namespace Redwood\Service\User\Dao;

interface TokenDao
{
	public function addToken(array $token);

	public function getToken($id);

	public function findTokenByToken($token);

	public function deleteToken($id);

}