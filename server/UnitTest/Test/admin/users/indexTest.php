<?php
include __DIR__ . '/../../../../admin/users/index.php';

class AdminUsersTest extends PHPUnit_Framework_TestCase
{
	private $model, $db;

	public function setUp()
	{
		$this->model = new AdminUsers();
		$this->db = new testDb();
	}

	public function tearDown()
	{
		$this->db->exec('DELETE FROM booker_users
						 WHERE `name` = "Abracadabra"');
		$this->db->exec('DELETE FROM booker_users
						 WHERE `name` = "Abra Cadabra"');
						 
		$this->model = NULL;
		$this->db = NULL;
	}
	
	public function testGetUsersByParamsTrue()
	{
		$result = $this->model->getUsersByParams( ['params' => ADMIN_HASH] );
		$this->assertArrayNotHasKey('status', $result);
		$this->assertArrayHasKey('id', $result[0]);
		$this->assertTrue(count($result) > 0);

		$result = $this->model->getUsersByParams( ['params' => ADMIN_HASH . '/5'] );
		$this->assertArrayNotHasKey('status', $result);
		$this->assertArrayHasKey('id', $result[0]);
		$this->assertTrue(count($result) > 0);
	}

	public function testGetUsersByParamsError()
	{
		$result = $this->model->getUsersByParams( ['params' => 'ADMIN_HASH'] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);

		$result = $this->model->getUsersByParams( ['params' => ADMIN_HASH . '/AAA'] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testPostUsersTrue()
	{
		$result = $this->model->postUsers( [
			'hash' => ADMIN_HASH,
			'name' => 'Abracadabra',
			'password' => '12345',
			'email' => 'mail@mail.mail'
		] );
		$this->assertNotInternalType('array', $result);
		$this->assertInternalType('bool', $result);
		$this->assertTrue($result);
	}

	public function testPostUsersError()
	{
		$result = $this->model->postUsers( [
			'hash' => ADMIN_HASH,
			'name' => '1 Abracadabra 1',
			'password' => '12345',
			'email' => 'mail mail . mail'
		] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testPutUsersTrue()
	{
		$this->db->exec( "INSERT INTO booker_users (name, email, password, hash)
  						  VALUES ('Abracadabra', 'mail@mail.mail', '12345', 'BUM')" );
		$id = $this->db->lastId();

		$result = $this->model->putUsers( [
			'hash' => ADMIN_HASH,
			'id' => $id,
			'name' => 'Abra Cadabra',
			'password' => '12345',
			'email' => 'mailmail@mail.mail',
		] );
		$this->assertNotInternalType('array', $result);
		$this->assertInternalType('bool', $result);
		$this->assertTrue($result);
	}

	public function testPutUsersError()
	{
		$result = $this->model->putUsers( [
			'hash' => ADMIN_HASH,
			'id' => 000,
			'name' => 'Abra 111 Cadabra',
			'password' => '12345',
			'email' => 'mail mail.mail',
		] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testDeleteUsersTrue()
	{
		$this->db->exec( "INSERT INTO booker_users (name, email, password, hash)
  						  VALUES ('Abracadabra', 'mail@mail.mail', '12345', 'BUM')" );
		$id = $this->db->lastId();

		$result = $this->model->deleteUsers( ['params' => ADMIN_HASH . '/' . $id] );
		$this->assertNotInternalType('array', $result);
		$this->assertInternalType('bool', $result);
		$this->assertTrue($result);
	}

	public function testDeleteUsersError()
	{
		$result = $this->model->deleteUsers( ['params' => ADMIN_HASH . '/000'] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

}
