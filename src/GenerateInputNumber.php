<?php

namespace Sis\FormBuilder;

trait GenerateInputNumber
{
    private static function generateInputNumber($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $subLabel = isset($inputField[self::KEY_SUB_LABEL]) ? $inputField[self::KEY_SUB_LABEL] : '';
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $step = isset($inputField[self::KEY_STEP]) ? $inputField[self::KEY_STEP] : 1;

        $html = "<div class='form-group'>
            <label>$label</label>";

        if (!empty($subLabel)) {
            $html = $html . "<p class='font-italic m-1'>$subLabel</p>";
        }

        $html = $html . "<input class='form-control' type='number' step='$step' id='$name' name='$name' value='$defaultValue' $otherAttribute>
            </div>";

        return $html;
    }

    private static function generateInputCurrency($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $subLabel = isset($inputField[self::KEY_SUB_LABEL]) ? $inputField[self::KEY_SUB_LABEL] : '';
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<div class='form-group'>
        <label>$label</label>";

        if (!empty($subLabel)) {
            $html = $html . "<p class='font-italic m-1'>$subLabel</p>";
        }

        $html = $html . "<input class='form-control' type='text' id='$name' name='$name' value='$defaultValue' $otherAttribute>
        </div>";

        $js = self::generateCurrencyJs($name);

        return ['html' => $html, 'js' => $js];
    }

    private static function generateCurrencyJs($name)
    {
        $js = "<script>
        IMask(document.getElementById('$name'), {
            mask: Number,
            thousandsSeparator: '.',
            radix: ',',
            signed: true,
        })
        </script>";

        return $js;
    }
}
