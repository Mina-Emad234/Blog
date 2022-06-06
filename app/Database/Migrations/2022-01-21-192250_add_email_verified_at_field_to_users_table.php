<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailVerifiedAtFieldToUsersTable extends Migration
{
	public function up()
	{
        $fields=[
            'email_verified_at' => [
                'type' => 'timestamp',
                'after'=>'user_mail',
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
