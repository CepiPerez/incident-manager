<?php

class createClientesTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('clientes',
			$table->integer('codigo', true, true,  5),
			$table->string('descripcion', 50),
			$table->integer('tipo_servicio', false, false, 5),
			$table->integer('activo', false, false, 1),
			$table->primary('codigo')
		);
		DB::statement('ALTER TABLE clientes CHANGE codigo codigo INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');
		DB::statement('ALTER TABLE clientes CHANGE tipo_servicio tipo_servicio INT(5) UNSIGNED ZEROFILL NOT NULL');

		$cliente = new Cliente;
		$cliente->descripcion = 'NewRol';
		$cliente->tipo_servicio = 1;
		$cliente->activo = 1;
		$cliente->save();

	}

	public function down()
	{
		Schema::dropIfExists('clientes');
	}
}