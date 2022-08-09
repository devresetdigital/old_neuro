<?php

namespace App\Http\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class PercentageFormField extends AbstractHandler
{
    protected $codename = 'percentage';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {

        return view('vendor.voyager.formfields.percentage', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}