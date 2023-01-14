<?php

class createTipoIncidentesTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('tipo_incidentes',
			$table->integer('codigo', true, true, 5),
			$table->string('descripcion', 50)->nullable(),
			$table->integer('pondera', false, false, 5)->nullable(),
			$table->integer('activo', false, false, 1)->nullable(),
			$table->primary('codigo')
		);
		DB::statement('ALTER TABLE tipo_incidentes CHANGE codigo codigo INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');

	}

	public function down()
	{
		Schema::dropIfExists('tipo_incidentes');
	}
}