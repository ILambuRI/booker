<?php
include __DIR__ . '/../../../../booker/rooms/index.php';

class RoomsTest extends PHPUnit_Framework_TestCase
{
	private $model;

	public function setUp()
	{
		$this->model = new Rooms();
	}

	public function tearDown()
	{
		$this->model = NULL;
	}

	public function testGetRoomsTrue()
	{
		$result = $this->model->getRooms();
		$this->assertArrayNotHasKey('status', $result);
		$this->assertArrayHasKey('id', $result[0]);
		$this->assertTrue(count($result) > 0);
	}
}