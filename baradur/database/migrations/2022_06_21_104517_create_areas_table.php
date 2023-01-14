<?php

class createAreasTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('areas',
			$table->integer('codigo', true, true, 5),
			$table->string('descripcion', 50),
			$table->primary('codigo')
		);

	}

	public function down()
	{
		Schema::dropIfExists('areas');
	}
}