<?php

class createTipoServiciosTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('tipo_servicios',
			$table->integer('codigo', true, true, 5),
			$table->string('descripcion', 50)->nullable(),
			$table->integer('pondera', false, false, 5)->nullable(),
			$table->primary('codigo')
		);
		DB::statement('ALTER TABLE tipo_servicios CHANGE codigo codigo INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');

		$serv = new TipoServicio();
		$serv->descripcion = 'Basic';
		$serv->pondera = 0;
		$serv->save();


	}

	public function down()
	{
		Schema::dropIfExists('tipo_servicios');
	}
}