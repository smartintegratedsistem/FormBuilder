<?php

namespace Sis\FormBuilder;

use Carbon\Carbon;

trait GenerateInputDateTime
{
    private static function generateInputDate($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<div class='form-group'>
            <label>$label</label>
            <input class='form-control' type='date' id='$name' name='$name' value='$defaultValue' $otherAttribute>
        </div>";

        return $html;
    }

    private static function generateInputMonth($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<div class='form-group'>
            <label>$label</label>
            <input class='form-control' type='month' id='$name' name='$name' value='$defaultValue' $otherAttribute>
        </div>";

        return $html;
    }

    private static function generateInputTime($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<div class='form-group'>
            <label>$label</label>
            <input class='form-control' type='time' id='$name' name='$name' value='$defaultValue' $otherAttribute>
        </div>";

        return $html;
    }

    private static function generateInputDateTime($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $defaultValue = empty($defaultValue) ? $defaultValue : Carbon::parse($defaultValue)->toDateTimeLocalString();

        $html = "<div class='form-group'>
            <label>$label</label>
            <input class='form-control' type='datetime-local' id='$name' name='$name' value='$defaultValue' $otherAttribute>
        </div>";

        return $html;
    }
}
