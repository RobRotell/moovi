<?php


namespace Moovi\Datasets;


final class Styles extends \Moovi\Abstracts\Dataset
{
	// todo -- better way to weight "realistic" more heavily
	public static $values = [
		'animated',
		'fantastical',
		'gritty',
		'postmodern',
		'romantic',
		'scary',
		'surrealistic',
	];

}
