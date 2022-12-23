<?php

namespace Sis\FormBuilder;

trait GenerateInputSelect
{
    private static function generateInputSelect($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $placeholder = isset($inputField[self::KEY_PLACEHOLDER]) ? $inputField[self::KEY_PLACEHOLDER] : "";
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $options = isset($inputField[self::KEY_OPTIONS]) ? $inputField[self::KEY_OPTIONS] : [];

        $html = "<div class='form-group'>
            <label>$label</label>
            <select class='form-control' name='$name' id='$name' $otherAttribute>";

        if ($placeholder != "") {
            $html = $html . "<option selected disabled>$placeholder</option>";
        }

        foreach ($options as $key => $value) {
            if ($defaultValue == $key) {
                $html = $html . "<option value='$key' selected> $value </option>";
            } else {
                $html = $html . "<option value='$key'> $value </option>";
            }
        }
        $html = $html . "</select></div>";

        return $html;
    }

    private static function generateInputSelect2($inputField)
    {
        //--- BASE SETUP ---
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $options = isset($inputField[self::KEY_OPTIONS]) ? $inputField[self::KEY_OPTIONS] : [];

        //--- AJAX SETUP ---
        $url = isset($inputField[self::KEY_SELECT2_URL]) ? $inputField[self::KEY_SELECT2_URL] : "";
        $placeholder = isset($inputField[self::KEY_SELECT2_PLACEHOLDER]) ? $inputField[self::KEY_SELECT2_PLACEHOLDER] : "";
        $minimumInput = isset($inputField[self::KEY_SELECT2_MINIMUM_INPUT]) ? $inputField[self::KEY_SELECT2_MINIMUM_INPUT] : 3;
        $cache = isset($inputField[self::KEY_SELECT2_CACHE]) ? $inputField[self::KEY_SELECT2_CACHE] : false;
        $cache = $cache ? "true" : "false";

        $html = "<div class='form-group'>
            <label>$label</label>
            <select class='form-control' name='$name' id='$name' $otherAttribute>";
        foreach ($options as $key => $value) {
            if ($defaultValue == $key) {
                $html = $html . "<option value='$key' selected> $value </option>";
            } else {
                $html = $html . "<option value='$key'> $value </option>";
            }
        }
        $html = $html . "</select></div>";

        $js = "<script>
            $(function(){
                $('#$name').select2({
                    placeholder: '$placeholder',
                    minimumInputLength: $minimumInput,
                    ajax: {
                        url: '$url',
                        dataType: 'json',
                        data: function(params) {
                            return {
                                q: $.trim(params.term),
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: $cache,
                    }
                });
            })
        </script>";

        return ['html' => $html, 'js' => $js];
    }
}
