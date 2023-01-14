<?php

class createModulosTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('modulos',
			$table->integer('codigo', true, true, 5),
			$table->string('descripcion', 50)->nullable(),
			$table->integer('pondera', false, false, 5)->nullable(),
			$table->integer('activo', false, false, 1)->nullable(),
			$table->primary('codigo')
		);
		DB::statement('ALTER TABLE modulos CHANGE codigo codigo INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');

	}

	public function down()
	{
		Schema::dropIfExists('modulos');
	}
}