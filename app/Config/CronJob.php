<?php namespace Config;

use CodeIgniter\Config\BaseConfig;
use Daycry\CronJob\Scheduler;

class CronJob extends \Daycry\CronJob\Config\CronJob
{
	/**
	 * Directory
	 */
    public $FilePath = WRITEPATH . 'cronJob/';
    
	/**
	 * Filename setting
	 */
	public $FileName = 'jobs';

	/**
	 * Set true if you want save logs
	 */
	public $logPerformance = false;

	/*
    |--------------------------------------------------------------------------
    | Log Saving Method
    |--------------------------------------------------------------------------
    |
    | Set to specify the REST API requires to be logged in
    |
    | 'file'   Save in file
    | 'database'  Save in database
    |
    */
	public $logSavingMethod = 'file';

	/*
    |--------------------------------------------------------------------------
    | Database Group
    |--------------------------------------------------------------------------
    |
    | Connect to a database group for logging, etc.
    |
    */
	public $databaseGroup = 'default';

	/*
    |--------------------------------------------------------------------------
    | Cronjob Table Name
    |--------------------------------------------------------------------------
    |
    | The table name in your database that stores cronjobs
    |
    */
	public $tableName = 'cronjob';



    /*
    |--------------------------------------------------------------------------
	| Cronjobs
	|--------------------------------------------------------------------------
    |
	| Register any tasks within this method for the application.
	| Called by the TaskRunner.
	|
	| @param Scheduler $schedule
	*/
	public function init( Scheduler $schedule )
	{
		// $schedule->command('foo:bar')->everyMinute();

		// $schedule->shell('cp foo bar')->daily( '11:00 pm' );

		 $schedule->call( function() {
            $db = db_connect();
            $db->table('users')->where(['email_verified_at'=>null,'verify_send_time <='=>date('y-m-d H:i:sP',strtotime('now'))])->delete();
         })->daily();
	}
}