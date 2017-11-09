<?php
include __DIR__ . '/../../../../booker/events/index.php';

class EventsTest extends PHPUnit_Framework_TestCase
{
	private $model;

	public function setUp()
	{
		$this->model = new Events();
		$this->db = new testDb();
	}

	public function tearDown()
	{
		$this->db->exec('DELETE FROM booker_events');
		
		$this->model = NULL;
		$this->db = NULL;		
	}

	public function testGetEventsByParamsTrue()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$this->db->exec( "INSERT INTO booker_events (user_id, room_id, `desc`, start, end, created, event_id)
						  VALUES (5, 1, 'SOME DESC', $timeStart, $timeEnd, $timeNow, 0)" );
		
		$result = $this->model->getEventsByParams(['params' => '1/11/2017']);
		$this->assertArrayNotHasKey('status', $result);
		$this->assertArrayHasKey('id', $result[0]);
		$this->assertTrue(count($result) > 0);
	}

	public function testGetEventsByParamsError()
	{
		$result = $this->model->getEventsByParams( ['params' => '1/1/3000'] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	private function getTimeHour($cnt = 1)
    {
        return 60 * 60 * $cnt;
    }
}