<?php

class createAreaClienteTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('area_cliente',
			$table->integer('cliente_codigo', false, true, 5),
			$table->integer('area_codigo', false, true, 5)
		);
		DB::statement('ALTER TABLE area_cliente CHANGE cliente_codigo cliente_codigo INT(5) UNSIGNED ZEROFILL NOT NULL');
		DB::statement('ALTER TABLE area_cliente CHANGE area_codigo area_codigo INT(5) UNSIGNED ZEROFILL NOT NULL');

	}

	public function down()
	{
		Schema::dropIfExists('area_cliente');
	}
}