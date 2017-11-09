<?php
include __DIR__ . '/../../../../user/events/index.php';

class UserEventsTest extends PHPUnit_Framework_TestCase
{
	private $model, $db;

	public function setUp()
	{
		$this->model = new UserEvents();
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
		$id = $this->db->lastId();

		$result = $this->model->getEventsByParams( ['params' => $id] );
		
		$this->assertArrayNotHasKey('status', $result);
		$this->assertArrayHasKey('id', $result[0]);
		$this->assertTrue(count($result) > 0);
	}

	public function testGetEventsByParamsError()
	{
		$result = $this->model->getEventsByParams( ['params' => '00'] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);

		$result = $this->model->getEventsByParams( ['params' => ADMIN_HASH . '/AAA'] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testPostEventsTrueOneEvent()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$result = $this->model->postEvents( [
			'userId' => 5,
			'roomId' => 1,
			'desc' => 'SOME DESC',
			'timeCreate' => $timeNow,
			'timeStart' => $timeStart,
			'timeEnd' => $timeEnd,
			'type' => '',
			'duration' => '',
		] );
		$this->assertInternalType('array', $result);
		$this->assertArrayNotHasKey('status', $result);		
		$this->assertNotInternalType('bool', $result);
		$this->assertTrue(count($result) > 0);
	}

	public function testPostEventsErrorOneEvent()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$result = $this->model->postEvents( [
			'userId' => 5,
			'roomId' => 1,
			'desc' => 'SOME DESC',
			'timeCreate' => $timeNow,
			'timeStart' => $timeEnd,
			'timeEnd' => $timeStart,
			'type' => '',
			'duration' => '',
		] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}


	public function testPostEventsTrueWeekly()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$result = $this->model->postEvents( [
			'userId' => 5,
			'roomId' => 1,
			'desc' => 'SOME DESC',
			'timeCreate' => $timeNow,
			'timeStart' => $timeStart,
			'timeEnd' => $timeEnd,
			'type' => 'Weekly',
			'duration' => '4',
		] );
		$this->assertInternalType('array', $result);
		$this->assertArrayNotHasKey('status', $result);		
		$this->assertNotInternalType('bool', $result);
		$this->assertTrue(count($result) > 0);
	}

	public function testPostEventsErrorWeekly()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$result = $this->model->postEvents( [
			'userId' => 5,
			'roomId' => 1,
			'desc' => 'SOME DESC',
			'timeCreate' => $timeNow,
			'timeStart' => $timeStart,
			'timeEnd' => $timeEnd,
			'type' => 'Weekly',
			'duration' => '5',
		] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testPostEventsTrueBiweekly()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$result = $this->model->postEvents( [
			'userId' => 5,
			'roomId' => 1,
			'desc' => 'SOME DESC',
			'timeCreate' => $timeNow,
			'timeStart' => $timeStart,
			'timeEnd' => $timeEnd,
			'type' => 'Bi-weekly',
			'duration' => '2',
		] );
		$this->assertInternalType('array', $result);
		$this->assertArrayNotHasKey('status', $result);		
		$this->assertNotInternalType('bool', $result);
		$this->assertTrue(count($result) > 0);
	}

	public function testPostEventsErrorBiweekly()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$result = $this->model->postEvents( [
			'userId' => 5,
			'roomId' => 1,
			'desc' => 'SOME DESC',
			'timeCreate' => $timeNow,
			'timeStart' => $timeStart,
			'timeEnd' => $timeEnd,
			'type' => 'Bi-weekly',
			'duration' => '3',
		] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testPostEventsTrueMonthly()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$result = $this->model->postEvents( [
			'userId' => 5,
			'roomId' => 1,
			'desc' => 'SOME DESC',
			'timeCreate' => $timeNow,
			'timeStart' => $timeStart,
			'timeEnd' => $timeEnd,
			'type' => 'Monthly',
			'duration' => '1',
		] );
		$this->assertInternalType('array', $result);
		$this->assertArrayNotHasKey('status', $result);		
		$this->assertNotInternalType('bool', $result);
		$this->assertTrue(count($result) > 0);
	}

	public function testPostEventsErrorMonthly()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour(2);
		$timeEnd = $timeNow + $this->getTimeHour(3);

		$result = $this->model->postEvents( [
			'userId' => 0,
			'roomId' => 0,
			'desc' => 'SOME DESC',
			'timeCreate' => $timeNow,
			'timeStart' => $timeStart,
			'timeEnd' => $timeEnd,
			'type' => 'Monthly',
			'duration' => '1',
		] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testPutEventsTrue()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour();
		$timeEnd = $timeNow + $this->getTimeHour(1);

		$this->db->exec( "INSERT INTO booker_events (user_id, room_id, `desc`, start, end, created, event_id)
                          VALUES (5, 1, 'SOME DESC', $timeStart, $timeEnd, $timeNow, 0)" );
		$id = $this->db->lastId();

		$result = $this->model->putEvents( [
			'hash' => ADMIN_HASH,
			'userId' => 5,
			'roomId' => 1,
			'eventId' => $id,
			'startHour' => 23,
			'startMinutes' => 0,
			'endHour' => 23,
			'endMinutes' => 30,
			'desc' => 'SOME NEW DESC',
			'reacurring' => 'false',
		] );

		$this->assertInternalType('array', $result);
		$this->assertArrayNotHasKey('status', $result);		
		$this->assertNotInternalType('bool', $result);
		$this->assertTrue(count($result) > 0);
	}

	public function testPutEventsError()
	{
		$result = $this->model->putEvents( [
			'hash' => ADMIN_HASH,
			'userId' => 0,
			'roomId' => 0,
			'eventId' => $id,
			'startHour' => 23,
			'startMinutes' => 0,
			'endHour' => 23,
			'endMinutes' => 30,
			'desc' => 'SOME NEW DESC',
			'reacurring' => 'false',
		] );
		$this->assertArrayHasKey('status', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertCount(2, $result);
		$this->assertFalse(count($result) == 0);
	}

	public function testDeleteEventsTrue()
	{
		$timeNow = time();
		$timeStart = $timeNow + $this->getTimeHour();
		$timeEnd = $timeNow + $this->getTimeHour(1);

		$this->db->exec( "INSERT INTO booker_events (user_id, room_id, `desc`, start, end, created, event_id)
                          VALUES (5, 1, 'SOME DESC', $timeStart, $timeEnd, $timeNow, 0)" );
		$id = $this->db->lastId();

		$result = $this->model->deleteEvents( ['params' => ADMIN_HASH . '/' . $id . '/false'] );
		$this->assertInternalType('array', $result);
		$this->assertArrayNotHasKey('status', $result);		
		$this->assertNotInternalType('bool', $result);
		$this->assertTrue(count($result) > 0);
	}

	public function testDeleteEventsError()
	{
		$result = $this->model->deleteEvents( ['params' => ADMIN_HASH . '/000'] );
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
