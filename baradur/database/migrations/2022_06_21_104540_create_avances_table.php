<?php

class createAvancesTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('avances',
			$table->integer('id', true, true, 5),
			$table->integer('incidente', false, false, 5),
			$table->integer('tipo_avance', false, false, 5),
			$table->string('descripcion', 50)->nullable(),
			$table->datetime('fecha_ingreso'),
			$table->string('usuario', 20)->nullable(),
			$table->string('destino', 20)->nullable(),
			$table->primary('id')
		);
		DB::statement('ALTER TABLE avances CHANGE id id INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');

	}

	public function down()
	{
		Schema::dropIfExists('avances');
	}
}