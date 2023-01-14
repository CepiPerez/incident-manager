<?php

class createIncidentesTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('incidentes',
			$table->integer('id', true, true, 5),
			$table->integer('cliente', false, false, 5),
			$table->integer('area', false, false, 5),
			$table->integer('modulo', false, false, 5),
			$table->integer('programa', false, false, 5),
			$table->integer('tipo_incidente', false, false, 5),
			$table->text('descripcion'),
			$table->string('menu', 10),
			$table->integer('punto_menu', false, false, 2),
			$table->string('usuario', 20),
			$table->string('mail', 50),
			$table->string('tel', 50),
			$table->integer('status', false, false, 5),
			$table->datetime('fecha_ingreso')->nullable(),
			$table->integer('prioridad', false, false, 5),
			$table->datetime('fecha_ult_act')->nullable(),
			$table->string('asignado', 20)->nullable(),
			$table->primary('id')
		);
		DB::statement('ALTER TABLE incidentes CHANGE id id INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');

	}

	public function down()
	{
		Schema::dropIfExists('incidentes');
	}
}