<?php

namespace Sis\FormBuilder;

trait GenerateInputOther
{
    private static function generateAgreement($inputField)
    {
        $name = $inputField[self::KEY_NAME];
        $label = $inputField[self::KEY_LABEL];
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $defaultValue = $defaultValue ? 'checked' : '';

        $html = "<div class='form-check mb-4'>
            <input class='form-check-input' type='checkbox' value='1' id='$name' name='$name' $defaultValue required>
            <label class='form-check-label' for='$name'>$label</label>
        </div>";

        return $html;
    }

    private static function generateInputHidden($inputField)
    {
        $name = $inputField[self::KEY_NAME];
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<input type='hidden' id='$name' name='$name' value='$defaultValue'>";

        return $html;
    }

    private static function generateInputColor($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<div class='form-group'>
            <label>$label</label>
            <input class='form-control' type='color' id='$name' name='$name' value='$defaultValue' $otherAttribute>
        </div>";

        return $html;
    }

    private static function generateHeader($inputField)
    {
        $label = $inputField[self::KEY_LABEL];

        $html = "<h4 class=' text-primary'>$label</h4><hr>";
        return $html;
    }
}
