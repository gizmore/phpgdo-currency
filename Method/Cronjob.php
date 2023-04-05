<?php
declare(strict_types=1);
namespace GDO\Currency\Method;

use GDO\Cronjob\MethodCronjob;
use GDO\Currency\GDO_Currency;
use GDO\Currency\Module_Currency;

/**
 * Hourly sync currencies with EZB.
 *
 * @version 7.0.3
 * @since 6.4.0
 * @author gizmore
 */
final class Cronjob extends MethodCronjob
{

	public function runAt(): string
	{
		return Module_Currency::instance()->cfgUpdateFrequency();
	}

	public function run(): void
	{
		$module = Module_Currency::instance();
		$this->log('Requesting ECB exchange rates');
		$xml = simplexml_load_file('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
		$this->log('Got EZB exchange rates');
		foreach ($xml->Cube->Cube->Cube as $rate)
		{
			$this->syncCurrency($module, $rate['currency']->__toString(), $rate['rate']->__toString());
		}
	}

	private function syncCurrency(Module_Currency $module, string $iso, string $rate): void
	{
		if (!($currency = GDO_Currency::getByISO($iso)))
		{
			$currency = GDO_Currency::blank([
				'ccy_iso' => $iso,
				'ccy_ratio' => $rate,
				'ccy_symbol' => $iso,
				'ccy_digits' => $this->calcDigits($rate),
			]);
		}

		if ($currency->isSyncAutomated())
		{
			$currency->setVar('ccy_ratio', $rate);
			$currency->setVar('ccy_digits', $this->calcDigits($rate));
		}

		$currency->save();
	}

	private function calcDigits($rate): string
	{
		return (string) (strlen($rate) - (strpos($rate, '.') + 1));
	}

}
