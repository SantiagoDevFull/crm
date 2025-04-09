<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SoldExport implements FromView, ShouldAutoSize
{
	/**
	 * @return \Illuminate\Support\Collection
	 */

	protected $solds;
	protected $datetime;

	public function __construct($solds, $datetime)
	{
		$this->solds = $solds;
		$this->datetime = $datetime;
	}

	public function view(): View
	{
		$solds = $this->solds;
		$datetime = $this->datetime;

		return view('excel.sold')->with(compact('solds', 'datetime'));
	}
}
