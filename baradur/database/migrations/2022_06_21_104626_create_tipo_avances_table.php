<?php

class createTipoAvancesTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('tipo_avances',
			$table->integer('codigo', true, true, 5),
			$table->string('descripcion', 50)->nullable(),
			$table->integer('pondera', false, true, 5)->nullable(),
			$table->integer('hab_usuario', false, true, 5)->nullable(),
			$table->integer('visible', false, false, 1)->nullable(),
			$table->integer('correo', false, false, 1)->nullable(),
			$table->primary('codigo')
		);
		DB::statement('ALTER TABLE tipo_avances CHANGE codigo codigo INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');

	}

	public function down()
	{
		Schema::dropIfExists('tipo_avances');
	}
}