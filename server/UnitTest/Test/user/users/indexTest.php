<?php
include __DIR__ . '/../../../../user/users/index.php';

class UsersTest extends PHPUnit_Framework_TestCase
{
	private $model, $db;

	public function setUp()
	{
		$this->model = new Users();
		$this->db = new testDb();
	}

	public function tearDown()
	{
		// $this->db->exec('DELETE FROM booker_users
		// 				 WHERE `name` = "Abracadabra"');
		// $this->db->exec('DELETE FROM booker_users
		// 				 WHERE `name` = "Abra Cadabra"');
						 
		$this->model = NULL;
		$this->db = NULL;
	}

	public function testPutUsersTrue()
	{
		// $this->db->exec( "INSERT INTO booker_users (name, email, password, hash)
  		// 				 VALUES ('Abracadabra', 'mail@mail.mail', '12345', 'BUM')" );
		// $id = $this->db->lastId();

		$result = $this->model->putUsers( [
			'name' => 'Test User',
			'password' => '11111'
		] );
		$this->assertInternalType('array', $result);
		$this->assertNotInternalType('bool', $result);
		$this->assertTrue( count($result) > 0 );
	}

	public function testPutUsersError()
	{
		$result = $this->model->putUsers( [
			'name' => 'Test 1 User',
			'password' => '11111'
		] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testDeleteUsersTrue()
	{
		$this->db->exec("UPDATE booker_users
						 SET hash = 'testhash'
						 WHERE name = 'Test User'");

		$result = $this->model->deleteUsers( ['params' => 'testhash'] );
		$this->assertNotInternalType('array', $result);
		$this->assertInternalType('bool', $result);
		$this->assertTrue($result);
	}

	public function testDeleteUsersError()
	{
		$result = $this->model->deleteUsers( ['params' => 'NOT_HASH'] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

}
