<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEndVerifyTimeToUsersTable extends Migration
{
	public function up()
	{
        $fields=[
            'verify_send_time' => [
                'type' => 'datetime',
                'after'=>'email_verified_at',
            ]
        ];
        $this->forge->addColumn('users', $fields);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
