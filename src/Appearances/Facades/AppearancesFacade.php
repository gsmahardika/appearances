<?php
namespace Wiratama\Appearances\Console;

use Illuminate\Support\Facades\Facade;

class AppearancesFacade extends Facade
{
	public static function getFacadeAccessor() {
		return 'appearances';
	}
}
