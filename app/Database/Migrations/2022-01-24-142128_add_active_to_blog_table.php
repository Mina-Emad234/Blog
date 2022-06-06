<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActiveToBlogTable extends Migration
{
	public function up()
	{
        $fields=[
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
        ];
        $this->forge->addColumn('blog', $fields);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
