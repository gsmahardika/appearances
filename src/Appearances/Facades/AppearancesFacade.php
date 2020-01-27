<?php
namespace Wiratama\Appearances\Facades;

use Illuminate\Support\Facades\Facade;

class AppearancesFacade extends Facade
{
	public static function getFacadeAccessor() {
		return 'appearances';
	}
}
