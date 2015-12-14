<?php

namespace Bloglyzer\Services;

class ExcelService {


	public static function getExcel($data, $filename = null)
	{
		if( ! $filename)
		{
			$filename = \Str::slug('statistics' . '_' . time());
		}

		return \Excel::create($filename, function($excel) use ($data) {
		    $excel->sheet('sheet1', function($sheet) use ($data) {
		        $sheet->fromModel($data, null, 'A1', true);
		    });
		})->download('xls');;

	}


}