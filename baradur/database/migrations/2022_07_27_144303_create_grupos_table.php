<?php

class createGruposTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('grupos',
			$table->integer('id', true, true,  5),
			$table->string('descripcion', 50),
			$table->primary('id')
		);
		DB::statement('ALTER TABLE clientes CHANGE id id INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');
	}

	public function down()
	{
		Schema::dropIfExists('grupos');
	}
}