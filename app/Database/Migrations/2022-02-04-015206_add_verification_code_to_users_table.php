<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVerificationCodeToUsersTable extends Migration
{
	public function up()
	{
        $fields=[
            'verification_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'after'=>'admin',
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
