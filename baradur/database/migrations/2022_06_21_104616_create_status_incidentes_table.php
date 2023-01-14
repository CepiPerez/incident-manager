<?php

class createStatusIncidentesTable extends Migration
{
	public function up()
	{
		$table = new Table;

			Schema::create('status_incidentes',
			$table->integer('codigo', true, true, 5),
			$table->string('descripcion', 50)->nullable(),
			$table->primary('codigo')
		);
		DB::statement('ALTER TABLE status_incidentes CHANGE codigo codigo INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');

	}

	public function down()
	{
		Schema::dropIfExists('status_incidentes');
	}
}