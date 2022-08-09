<?php

namespace App\Http\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class UploaderFormField extends AbstractHandler
{
    protected $codename = 'uploader';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {

        return view('vendor.voyager.formfields.uploader', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}