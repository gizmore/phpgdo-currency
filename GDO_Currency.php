<?php
namespace GDO\Currency;

use GDO\Core\GDO;
use GDO\Core\GDT_Char;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Decimal;
use GDO\Core\GDT_String;
use GDO\Core\GDT_EditedAt;
use GDO\Core\GDT_UInt;

/**
 * A currency. Primary key is  the 3 letter ISO in uppercase.
 * Can convert to credits.
 * Can convert between currencies.
 * 
 * @TODO: implement currency conversion.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.8.0
*/
final class GDO_Currency extends GDO
{
	public function gdoCached() : bool { return true; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return [
			GDT_Char::make('ccy_iso')->ascii()->caseS()->length(3)->primary(),
			GDT_String::make('ccy_symbol')->max(3)->notNull(),
			GDT_UInt::make('ccy_digits')->bytes(1)->min(1)->max(4),
			GDT_Decimal::make('ccy_ratio')->digits(6, 6),
			GDT_Checkbox::make('ccy_auto_update')->initial('1'),
			GDT_EditedAt::make('ccy_updated_at'),
		];
	}

	##############
	### Getter ###
	##############
	public function getRatio() { return $this->gdoVar('ccy_ratio'); }
	public function getDigits() { return $this->gdoVar('ccy_digits'); }
	public function getSymbol() { return $this->gdoVar('ccy_symbol'); }
	public function isSyncAutomated() { return $this->gdoVar('ccy_auto_update') === '1'; }

	################
	### Display ####
	################
	public function renderName(): string { return $this->displayValue($this->getRatio(), true); }
	public function displayValue($value, $with_symbol=true)
	{
		return sprintf('%s%.0'.$this->getDigits().'f',
			$with_symbol ? $this->getSymbol().' ' : '',
			$value);
	}
	
	public function renderOption() : string
	{
		return $this->renderName();
	}
	
	###############
	### Factory ###
	###############
	/**
	 * @param string $iso
	 * @return self
	 */
	public static function getByISO($iso)
	{
		return self::getById($iso);
	}
	
	##################
	### Conversion ###
	##################
	public function toCredits($money)
	{
		return floor($money * 100.0);
	}
	
	public function toMoney($credits)
	{
		$money = $credits / 100.0;
		$digits = $this->getDigits();
		return sprintf("%.0{$digits}f", $money);
	}
	
	public static function convertCurrency($value, $from, $to)
	{

	}
	
}
