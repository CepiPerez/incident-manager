<?php

class Notificacion extends Mailable
{
	private $data;

	public function __construct($template)
	{
		$this->data = $template;
	}

	public function build()
	{
		return $this->view('mail.notificacion')->with(['incidente' => $this->data]);
	}

}