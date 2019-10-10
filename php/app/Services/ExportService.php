<?php

namespace App\Services;

use Excel;

class ExportService
{
    public static function export($aData, $sPushType)
    {
        Excel::create(date("YmdHis").$sPushType.'结果', function ($excel) use ($aData) {
            if (!empty($aData)) {
                $excel->sheet("模板消息推送结果", function ($sheet) use ($aData) {
                    $sheet->rows($aData);
                    // Set font
                    $sheet->setStyle(array(
                        'font' => array(
                            //'name' => 'Calibri',
                            'size' => 12
                        )
                    ));
                    $sheet->row(1, function ($row) {
                        $row->setFontWeight('bold');
                    });
                });
            }
        })->export('xlsx');
    }
}