<?php

namespace App\Model;

use Nette;

class UserRepository
{
	use Nette\SmartObject;

	private Nette\Database\Explorer $db;

	public function __construct(Nette\Database\Explorer $db)
	{
		$this->db = $db;
	}

	public function getUser($ip, $userAgent):? Nette\Database\Table\ActiveRow
	{
		$hash = md5($userAgent);
		$user = $this->db->table('user')
			->where('ip', $ip)
			->where('hash', $hash)
			->fetch();

		return $user;
	}

	public function createUser($ip, $userAgent):Nette\Database\Table\ActiveRow
	{
		return $this->db->table('user')->insert([
			'ip' => $ip,
			'hash' => md5($userAgent),
			'other' => $userAgent,
		]);
	}
}