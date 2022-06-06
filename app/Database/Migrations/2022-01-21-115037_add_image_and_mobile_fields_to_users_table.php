<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageAndMobileFieldsToUsersTable extends Migration
{
	public function up()
	{
        $fields=[
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
                'null' => false,
                'after'=>'user_pass',
            ],
            'mobile' => [
                'type' => 'VARCHAR',
                'constraint' => '11',
                'null' => false,
                'unique' => true,
                'after'=>'image',
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
