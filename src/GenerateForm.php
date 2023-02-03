<?php

namespace App\Helpers\FormBuilder;

trait GenerateForm
{
    public static function generateForm(
        $btnLabel,
        $action,
        $inputFields,
        $methodField = null,
    ) {
        $inputFieldHtml = self::generateInputFields($inputFields);

        $form = "<form id='form' method='post' action='$action' enctype='multipart/form-data' onsubmit='onSubmitForm()'>";
        $form = $form . csrf_field();
        $form = $form . (!empty($methodField) ? method_field($methodField) : '');
        $form = $form . $inputFieldHtml['html'];
        $form = $form . (!empty($btnLabel) ? self::generateButton($btnLabel) : '');
        $form = $form . "</form>";

        $js = $inputFieldHtml['js'] . self::generateOnSubmitFormListener();
        $css = $inputFieldHtml['css'];

        return ['html' => $form, 'js' => $js, 'css' => $css];
    }

    public static function generateInputFields($inputFields)
    {
        $inputFieldHtml = self::generateInput($inputFields);
        $js = "";
        $css = "";

        //--- CK EDITOR SETUP ---
        if ($inputFieldHtml['isUsingCkEditor']) {
            if (self::CDN_CK_EDITOR) {
                $js = (self::VER_CK_EDITOR == 4 ? self::CDN_CK_EDITOR_4 : self::CDN_CK_EDITOR_5) . $inputFieldHtml['js'];
            } else {
                $js = "<script src='" . asset(self::LOCAL_CK_EDITOR) . "'></script>" . $inputFieldHtml['js'];
            }
        } else {
            $js = $inputFieldHtml['js'];
        }

        //--- IMASK SETUP ---
        if ($inputFieldHtml['isUsingIMask']) {
            $js = self::CDN_IMASK . $js;
        }

        //--- SELECT2 SETUP ---
        if ($inputFieldHtml['isUsingSelect2'] && !self::CDN_SELECT2_INCLUDED) {
            $js = self::CDN_SELECT2_JS . $js;
            $css = self::CDN_SELECT2_CSS . $css;
        }

        return ['html' => $inputFieldHtml['html'], 'js' => $js, 'css' => $css];
    }

    //-------------GENERATE INPUT-------------

    private static function generateInput($inputFields)
    {
        $html = "";
        $js = "";
        $isUsingCkEditor = false;
        $isUsingIMask = false;
        $isUsingSelect2 = false;

        foreach ($inputFields as $inputField) {
            if (!isset($inputField[self::KEY_TYPE])) {
                $res = self::generateMultipleColumnInput($inputField);
            } else {
                $res = self::generateOneColumnInput($inputField);
            }

            $html = $html . $res['html'];
            $js = $js . $res['js'];

            $isUsingCkEditor = $isUsingCkEditor || $res['isUsingCkEditor'];
            $isUsingIMask = $isUsingIMask || $res['isUsingIMask'];
            $isUsingSelect2 = $isUsingSelect2 || $res['isUsingSelect2'];
        }

        return [
            'html' => $html,
            'js' => $js,
            'isUsingCkEditor' => $isUsingCkEditor,
            'isUsingIMask' => $isUsingIMask,
            'isUsingSelect2' => $isUsingSelect2,
        ];
    }

    private static function generateMultipleColumnInput($inputFields)
    {
        $html = "";
        $js = "";
        $isUsingCkEditor = false;
        $isUsingIMask = false;
        $isUsingSelect2 = false;

        $html = "<div class='row'>";
        foreach ($inputFields as $inputField) {
            $res = self::generateOneColumnInput($inputField);

            $html = $html . "<div class='col-md-6'>";
            $html = $html . $res['html'];
            $html = $html . "</div>";

            $js = $js . $res['js'];

            $isUsingCkEditor = $isUsingCkEditor || $res['isUsingCkEditor'];
            $isUsingIMask = $isUsingIMask || $res['isUsingIMask'];
            $isUsingSelect2 = $isUsingSelect2 || $res['isUsingSelect2'];
        }
        $html = $html . "</div>";

        return [
            'html' => $html,
            'js' => $js,
            'isUsingCkEditor' => $isUsingCkEditor,
            'isUsingIMask' => $isUsingIMask,
            'isUsingSelect2' => $isUsingSelect2,
        ];
    }

    private static function generateOneColumnInput($inputField)
    {
        $html = "";
        $js = "";
        $isUsingCkEditor = false;
        $isUsingIMask = false;
        $isUsingSelect2 = false;

        $type = $inputField[self::KEY_TYPE];

        if ($type == self::INPUT_TYPE_TEXT) {
            $html = $html . self::generateInputText($inputField);

        } else if ($type == self::INPUT_TYPE_COLOR) {
            $html = $html . self::generateInputColor($inputField);

        }  else if ($type == self::INPUT_TYPE_AGREEMENT) {
            $html = $html . self::generateAgreement($inputField);

        } else if ($type == self::INPUT_TYPE_PHONE) {
            $html = $html . self::generateInputPhone($inputField);

        } else if ($type == self::INPUT_TYPE_PASSWORD) {
            $result = self::generateInputPassword($inputField);
            $html = $html . $result['html'];
            $js = $js . $result['js'];
            
        } else if ($type == self::INPUT_TYPE_TEXT_AREA) {
            $html = $html . self::generateInputTextArea($inputField);

        } else if ($type == self::INPUT_TYPE_SELECT) {
            $html = $html . self::generateInputSelect($inputField);

        } else if ($type == self::INPUT_TYPE_DATE) {
            $html = $html . self::generateInputDate($inputField);

        } else if ($type == self::INPUT_TYPE_MONTH) {
            $html = $html . self::generateInputMonth($inputField);

        } else if ($type == self::INPUT_TYPE_TIME) {
            $html = $html . self::generateInputTime($inputField);

        } else if ($type == self::INPUT_TYPE_DATE_TIME) {
            $html = $html . self::generateInputDateTime($inputField);

        } else if ($type == self::INPUT_TYPE_NUMBER) {
            $html = $html . self::generateInputNumber($inputField);

        } else if ($type == self::INPUT_TYPE_TEXT_MULTIPLE) {
            $result = self::generateInputTextMultiple($inputField);
            $html = $html . $result['html'];
            $js = $js . $result['js'];

        } else if ($type == self::INPUT_TYPE_FILE) {
            $result = self::generateInputFile($inputField);
            $html = $html . $result['html'];
            $js = $js . $result['js'];

        } else if ($type == self::INPUT_TYPE_FILE_MULTIPLE) {
            $result = self::generateInputFileMultiple($inputField);
            $html = $html . $result['html'];
            $js = $js . $result['js'];

        } else if ($type == self::INPUT_TYPE_IMAGE) {
            $result = self::generateInputImage($inputField);
            $html = $html . $result['html'];
            $js = $js . $result['js'];

        } else if ($type == self::INPUT_TYPE_HEADER) {
            $html = $html . self::generateHeader($inputField);

        } else if ($type == self::INPUT_TYPE_HIDDEN) {
            $html = $html . self::generateInputHidden($inputField);

        } else if ($type == self::INPUT_TYPE_RAW_HTML) {
            $html = $html . $inputField[self::KEY_HTML];

        } else if ($type == self::INPUT_TYPE_TEXT_AREA_CK_EDITOR) {
            $result = self::generateInputTextAreaCkEditor($inputField);
            $html = $html . $result['html'];
            $js = $js . $result['js'];
            $isUsingCkEditor = true;

        } else if ($type == self::INPUT_TYPE_CURRENCY) {
            $result = self::generateInputCurrency($inputField);
            $html = $html . $result['html'];
            $js = $js . $result['js'];
            $isUsingIMask = true;

        } else if ($type == self::INPUT_TYPE_SELECT2) {
            $result = self::generateInputSelect2($inputField);
            $html = $html . $result['html'];
            $js = $js . $result['js'];
            $isUsingSelect2 = true;
        }

        return [
            'html' => $html,
            'js' => $js,
            'isUsingCkEditor' => $isUsingCkEditor,
            'isUsingIMask' => $isUsingIMask,
            'isUsingSelect2' => $isUsingSelect2,
        ];
    }

    private static function generateButton($label = "Save")
    {
        $html = "<button id='btn_submit' class='btn btn-primary mt-3'>
            <i class='fas fa-check'></i>
            <span class='text'>$label</span>
        </button>";
        return $html;
    }

    private static function generateOnSubmitFormListener()
    {
        $js = "<script>
        function onSubmitForm(){
            $('#btn_submit').text('Loading...');
            $('#btn_submit').prop('disabled', true);
        }
        </script>";

        return $js;
    }
}
