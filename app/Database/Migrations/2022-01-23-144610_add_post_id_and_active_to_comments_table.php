<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPostIdAndActiveToCommentsTable extends Migration
{
	public function up()
	{
        $fields=[
            'post_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ];
        $this->forge->addColumn('comments', $fields);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
