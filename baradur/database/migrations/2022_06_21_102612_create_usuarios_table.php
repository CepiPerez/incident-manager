<?php

class createUsuariosTable extends Migration
{
	public function up()
	{
		$table = new Table;

		Schema::create('usuarios',
			$table->integer('id', 5),
			$table->string('Usuario', 50),
			$table->string('nombre', 50)->nullable(),
			$table->string('Password', 50)->nullable(),
			$table->integer('nivel', false, false, 5)->nullable(),
			$table->integer('STATUS', false, false, 5)->nullable(),
			$table->string('Mail', 50)->nullable(),
			$table->integer('activo', false, false, 1),
			$table->string('rol', 20),
			$table->integer('cliente', false, false, 5)->nullable(),
			$table->string('token', 100)->nullable(),
			$table->primary('id')
		);
		DB::statement('ALTER TABLE usuarios CHANGE id id INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT');

		$user = new User;
		$user->Usuario = 'admin';
		$user->nombre = 'Admin';
		$user->Password = 'televisor';
		$user->activo = 1;
		$user->rol = 'admin';
		$user->cliente = 1;
		$user->save();

	}

	public function down()
	{
		Schema::dropIfExists('usuarios');
	}
}