<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdminToUsersTable extends Migration
{
	public function up()
	{
        $fields=[
            'admin' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],

        ];
        $this->forge->addColumn('users', $fields);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
