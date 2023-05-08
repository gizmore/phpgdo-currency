<?php
namespace GDO\Currency;

use GDO\Core\GDT_ObjectSelect;

/**
 * Currency selection.
 *
 * @version 6.11.3
 * @since 6.8.0
 * @see GDO_Currency
 * @author gizmore
 */
final class GDT_Currency extends GDT_ObjectSelect
{

	###########
	### GDT ###
	###########
	public $supported = false;

	#################
	### Supported ###
	#################

	public function __construct()
	{
		parent::__construct();
		$this->table(GDO_Currency::table());
	}

	public function supported($supported = true)
	{
		$this->supported = $supported;
		return $this;
	}

	##############
	### Select ###
	##############
	protected function getChoices(): array
	{
		if ($this->supported)
		{
			return Module_Currency::instance()->cfgSupported();
		}
		return $this->table ? $this->table->allCached() : [];
	}


}
