<?php

class createAdjuntosIncidentesTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('adjuntos_incidentes',
			$table->integer('incidente', false, true, 5),
			$table->integer('avance', false, true, 5),
			$table->string('adjunto', 60)->nullable()
		);

	}

	public function down()
	{
		Schema::dropIfExists('adjuntos_incidentes');
	}
}