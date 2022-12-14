<?php

namespace App\Helpers;

use Carbon\Carbon;

/*
| Last Updated 2022-12-07
| New: Limit Upload Size
 */

class FormBuilderHelper
{
    // *** CK EDITOR CONFIG ***
    const VER_CK_EDITOR = 4; //4 or 5
    const CDN_CK_EDITOR = true;
    const CDN_CK_EDITOR_4 = "<script src='//cdn.ckeditor.com/4.19.1/full/ckeditor.js'></script>";
    const CDN_CK_EDITOR_5 = "<script src='https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js'></script>";
    const LOCAL_CK_EDITOR = 'vendor/ckeditor/ckeditor.js';
    const ROUTE_UPLOAD_FILE_CK_EDITOR = '';
    // const ROUTE_UPLOAD_FILE_CK_EDITOR = 'ckeditor.image';

    // *** SELECT2 CONFIG ***
    const CDN_SELECT2_INCLUDED = true;
    const CDN_SELECT2_JS = '<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>';
    const CDN_SELECT2_CSS = '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';

    // *** IMASK CONFIG ***
    const CDN_IMASK = "<script src='https://unpkg.com/imask'></script>";

    const INPUT_TYPE_CURRENCY = "currency";
    const INPUT_TYPE_COLOR = "color";
    const INPUT_TYPE_DATE = "date";
    const INPUT_TYPE_TIME = "time";
    const INPUT_TYPE_DATE_TIME = "datetime-local";
    const INPUT_TYPE_FILE = "file";
    const INPUT_TYPE_FILE_MULTIPLE = "files";
    const INPUT_TYPE_HEADER = "header";
    const INPUT_TYPE_HIDDEN = "hidden";
    const INPUT_TYPE_IMAGE = "image";
    const INPUT_TYPE_NUMBER = "number";
    const INPUT_TYPE_PASSWORD = "password";
    const INPUT_TYPE_PHONE = "phone";
    const INPUT_TYPE_SELECT = "select";
    const INPUT_TYPE_SELECT2 = "select2";
    const INPUT_TYPE_TEXT = "text";
    const INPUT_TYPE_TEXT_MULTIPLE = "text-multiple";
    const INPUT_TYPE_TEXT_AREA = "text-area";
    const INPUT_TYPE_TEXT_AREA_CK_EDITOR = "text-area-ck-editor";
    const INPUT_TYPE_RAW_HTML = "raw-html";

    const KEY_ACCEPT = "accept";
    const KEY_DEFAULT_VALUE = "defaultValue";
    const KEY_HEIGHT = "height";
    const KEY_LABEL = "label";
    const KEY_LABEL_OLD = "label_old";
    const KEY_SUB_LABEL = "sub_label";
    const KEY_NAME = "name";
    const KEY_OPTIONS = "options";
    const KEY_OTHER_ATTRIBUTE = "otherAttribute";
    const KEY_PREVIEW_IMAGE = "isPreviewImage";
    const KEY_PREVIEW_BUTTON_TEXT = "btnText";
    const KEY_PREVIEW_FILE = "isPreviewFile";
    const KEY_ROWS = "rows";
    const KEY_STEP = "step";
    const KEY_TYPE = "type";
    const KEY_URL = "url";
    const KEY_WIDTH = "width";
    const KEY_HTML = "html";
    const KEY_SIZE_LIMIT = "sizeLimit";

    const KEY_SELECT2_URL = "select2_url";
    const KEY_SELECT2_PLACEHOLDER = "select2_placeholder";
    const KEY_SELECT2_MINIMUM_INPUT = "select2_minimum_input";
    const KEY_SELECT2_CACHE = "select2_cache";

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

        } else if ($type == self::INPUT_TYPE_PHONE) {
            $html = $html . self::generateInputPhone($inputField);

        } else if ($type == self::INPUT_TYPE_PASSWORD) {
            $html = $html . self::generateInputPassword($inputField);

        } else if ($type == self::INPUT_TYPE_TEXT_AREA) {
            $html = $html . self::generateInputTextArea($inputField);

        } else if ($type == self::INPUT_TYPE_SELECT) {
            $html = $html . self::generateInputSelect($inputField);

        } else if ($type == self::INPUT_TYPE_DATE) {
            $html = $html . self::generateInputDate($inputField);

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

    //-------------GENERATE INPUT TYPE-------------

    private static function generateInputHidden($inputField)
    {
        $name = $inputField[self::KEY_NAME];
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<input type='hidden' id='$name' name='$name' value='$defaultValue'>";

        return $html;
    }

    private static function generateInputText($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $subLabel = isset($inputField[self::KEY_SUB_LABEL]) ? $inputField[self::KEY_SUB_LABEL] : '';
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<div class='form-group'>
            <label>$label</label>";

        if (!empty($subLabel)) {
            $html = $html . "<p class='font-italic m-1'>$subLabel</p>";
        }

        $html = $html . "<input class='form-control' type='text' id='$name' name='$name' value='$defaultValue' $otherAttribute>
            </div>";

        return $html;
    }

    private static function generateInputPhone($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<div class='form-group'>
            <label>$label</label>
            <input class='form-control' type='phone' id='$name' name='$name' value='$defaultValue' $otherAttribute>
        </div>";

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

    private static function generateInputTextAreaCkEditor($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $rows = isset($inputField[self::KEY_ROWS]) ? $inputField[self::KEY_ROWS] : 4;
        $height = isset($inputField[self::KEY_HEIGHT]) ? $inputField[self::KEY_HEIGHT] : '25em';

        //-----------------
        //---CK EDITOR 4---
        //-----------------
        if (self::VER_CK_EDITOR == 4) {
            $html = "<div class='form-group'>
                <label>$label</label>
                <textarea class='form-control' rows='$rows' id='$name' name='$name' $otherAttribute>$defaultValue</textarea>
            </div>";

            if (!empty(self::ROUTE_UPLOAD_FILE_CK_EDITOR)) {
                $js = "<script>$(document).ready(function () {
                    CKEDITOR.replace( '$name',{
                        height : '$height',
                        filebrowserUploadMethod : 'form',
                        filebrowserUploadUrl : '" . route(self::ROUTE_UPLOAD_FILE_CK_EDITOR, ['_token' => csrf_token()]) . "'
                    } );
                });</script>";
            } else {
                $js = "<script>$(document).ready(function () {
                    CKEDITOR.replace( '$name',{
                        height : '$height',
                        removePlugins : 'image',
                    } );
                });</script>";
            }
        }

        //-----------------
        //---CK EDITOR 5---
        //-----------------
        if (self::VER_CK_EDITOR == 5) {
            $html = "<div class='form-group'>
                <label>$label</label>
                <div class='form-control' rows='$rows' id='$name' name='$name' $otherAttribute>$defaultValue</div>
            </div>";

            $js = "<script>

                ClassicEditor.create( document.querySelector( '#$name' ),{
                    alignment: {
                        options: [ 'left', 'right' ]
                    },
                    toolbar: [
                        'heading', '|', 'bulletedList', 'numberedList', 'alignment', 'undo', 'redo'
                    ]
                })
                .then( editor => {
                        console.log( editor );
                } )
                .catch( error => {
                        console.error( error );
                } );
            </script>";
        }

        return ['html' => $html, 'js' => $js];
    }

    private static function generateInputTextArea($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $rows = isset($inputField[self::KEY_ROWS]) ? $inputField[self::KEY_ROWS] : 4;

        $html = "<div class='form-group'>
            <label>$label</label>
            <textarea class='form-control' rows='$rows' id='$name' name='$name' $otherAttribute>$defaultValue</textarea>
        </div>";
        return $html;
    }

    private static function generateInputPassword($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);

        $html = "<div class='form-group'>
            <label>$label</label>
            <input class='form-control' type='password' id='$name' name='$name' value='$defaultValue' $otherAttribute>
        </div>";

        return $html;
    }

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

    private static function generateInputSelect($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $options = isset($inputField[self::KEY_OPTIONS]) ? $inputField[self::KEY_OPTIONS] : [];

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

    private static function generateInputTextMultiple($inputField)
    {
        $name = $inputField[self::KEY_NAME];
        $label = $inputField[self::KEY_LABEL];
        $subLabel = isset($inputField[self::KEY_SUB_LABEL]) ? $inputField[self::KEY_SUB_LABEL] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? "'" . implode("','", $inputField[self::KEY_DEFAULT_VALUE]) . "'" : "";
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";

        $html = "<div class='form-group'>";

        //--- Label ---
        if (!empty($label)) {
            $html = $html . "<label>$label</label>";
        }

        //--- Sub Label ---
        if (!empty($subLabel)) {
            $html = $html . "<p class='font-italic m-1'>$subLabel</p>";
        }

        //--- Input Type Hidden ---
        $html = $html . "<input type='hidden' name='$name' id='$name'>";

        //--- Input Typing Place ---
        $html = $html . "<input type='text' class='form-control' id='{$name}_typing_place'>";

        //--- Input Result ---
        $html = $html . "<div id='{$name}_result' class='row mb-3'></div>";

        $html = $html . "</div>";

        $js = "<script>
            var init_input_multiple_$name = [$defaultValue];
            var input_multiple_$name = [];
            $(()=>{
                $('#{$name}_typing_place').on('keydown', (e) => {
                    if(e.keyCode == 13){
                        e.preventDefault();

                        let typedText = $('#{$name}_typing_place').val();
                        add_$name(typedText);
                    }
                });

                init_input_multiple_$name.forEach((item)=>{
                    add_$name(item);
                });
            })

            function add_$name(text){
                input_multiple_$name.push(text);
                let id = '{$name}_' + (input_multiple_{$name}[input_multiple_{$name}.length - 1] + 1);

                let html = `<button type='button' class='btn btn-outline-primary col-auto m-2' id='\${id}' onclick=\"on_delete_$name('\${id}', '\${text}')\">
                    <i class='fas fa-times'></i>
                    \${text}
                </button>`;

                $('#{$name}_result').append(html);
                $('#{$name}_typing_place').val('');

                renew_$name();
            }

            function on_delete_$name(viewId, text){
                $(`#\${viewId}`).remove();

                var index = input_multiple_$name.indexOf(text);
                if (index !== -1) {
                    input_multiple_$name.splice(index, 1);
                    renew_$name();
                }
            }

            function renew_$name(){
                $('#$name').val(input_multiple_$name);
            }
        </script>";

        return ['html' => $html, 'js' => $js];
    }

    private static function generateInputImage($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $subLabel = isset($inputField[self::KEY_SUB_LABEL]) ? $inputField[self::KEY_SUB_LABEL] : '';
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $accept = isset($inputField[self::KEY_ACCEPT]) ? $inputField[self::KEY_ACCEPT] : "*/*";
        $isShowPreviewImage = isset($inputField[self::KEY_PREVIEW_IMAGE]) ? $inputField[self::KEY_PREVIEW_IMAGE] : false;
        $imgWidth = isset($inputField[self::KEY_WIDTH]) ? $inputField[self::KEY_WIDTH] : 300;
        $imgHeight = isset($inputField[self::KEY_HEIGHT]) ? $inputField[self::KEY_HEIGHT] : 300;
        $sizeLimit = isset($inputField[self::KEY_SIZE_LIMIT]) ? $inputField[self::KEY_SIZE_LIMIT] : false;

        $html = "<label>$label</label>";
        if (!empty($subLabel)) {
            $html = $html . "<p class='font-italic m-1'>$subLabel</p>";
        }

        $html = $html . "<div class='input-group'>
            <div class='custom-file'>
                <input class='custom-file-input' type='file' id='$name' name='$name' accept='$accept' $otherAttribute>
                <label class='custom-file-label' for='$name' id='{$name}_label'>Pilih $label</label>
            </div>
        </div>"
            . ($isShowPreviewImage ? self::generatePreviewImageHtml($name, $imgWidth, $imgHeight, $defaultValue) : '');

        $js = $isShowPreviewImage ? self::generatePreviewImageJs($name, $sizeLimit) : self::generatePreviewFileJs($name, $sizeLimit);

        return ['html' => $html, 'js' => $js];
    }

    private static function generateInputFile($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $subLabel = isset($inputField[self::KEY_SUB_LABEL]) ? $inputField[self::KEY_SUB_LABEL] : '';
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $accept = isset($inputField[self::KEY_ACCEPT]) ? $inputField[self::KEY_ACCEPT] : "*/*";
        $previewBtnText = isset($inputField[self::KEY_PREVIEW_BUTTON_TEXT]) ? $inputField[self::KEY_PREVIEW_BUTTON_TEXT] : "";
        $isShowPreviewFile = isset($inputField[self::KEY_PREVIEW_FILE]) ? $inputField[self::KEY_PREVIEW_FILE] : false;
        $sizeLimit = isset($inputField[self::KEY_SIZE_LIMIT]) ? $inputField[self::KEY_SIZE_LIMIT] : false;

        $html = "<label>$label</label>";
        if (!empty($subLabel)) {
            $html = $html . "<p class='font-italic m-1'>$subLabel</p>";
        }

        if (!$isShowPreviewFile) {
            $html = $html . "<div class='input-group'>
                <div class='custom-file'>
                    <input class='custom-file-input' type='file' id='$name' name='$name' accept='$accept' $otherAttribute>
                    <label class='custom-file-label' for='$name' id='{$name}_label'>$label</label>
                </div>
            </div>";
        } else {
            $btn = self::generatePreviewFileButton($defaultValue, $previewBtnText);
            $html = $html . "<div class='input-group'>
                <div class='custom-file'>
                    <input class='custom-file-input' type='file' id='$name' name='$name' accept='$accept' $otherAttribute>
                    <label class='custom-file-label' for='$name' id='{$name}_label'>$label</label>
                </div>
            </div>
            <div>$btn</div>";
        }

        $js = self::generatePreviewFileJs($name, $sizeLimit);

        return ['html' => $html, 'js' => $js];
    }

    private static function generateInputFileMultiple($inputField)
    {
        $label = $inputField[self::KEY_LABEL];
        $name = $inputField[self::KEY_NAME];
        $subLabel = isset($inputField[self::KEY_SUB_LABEL]) ? $inputField[self::KEY_SUB_LABEL] : '';
        $labelOld = isset($inputField[self::KEY_LABEL_OLD]) ? $inputField[self::KEY_LABEL_OLD] : "Old $label";
        $otherAttribute = isset($inputField[self::KEY_OTHER_ATTRIBUTE]) ? $inputField[self::KEY_OTHER_ATTRIBUTE] : "";
        $accept = isset($inputField[self::KEY_ACCEPT]) ? $inputField[self::KEY_ACCEPT] : "*/*";
        $defaultValue = isset($inputField[self::KEY_DEFAULT_VALUE]) ? $inputField[self::KEY_DEFAULT_VALUE] : old($inputField[self::KEY_NAME]);
        $sizeLimit = isset($inputField[self::KEY_SIZE_LIMIT]) ? $inputField[self::KEY_SIZE_LIMIT] : false;

        $html = "<label>$label</label>";
        if (!empty($subLabel)) {
            $html = $html . "<p class='font-italic m-1'>$subLabel</p>";
        }

        $html = $html . "<div class='input-group'>
            <div class='custom-file'>
                <input class='custom-file-input' type='file' id='$name' name='${name}[]' accept='$accept' multiple $otherAttribute>
                <label class='custom-file-label' for='$name' id='{$name}_label'>$label</label>
            </div>
        </div>
        <div class='row mt-2' id='div_multiple_$name'></div>";

        if (!empty($defaultValue) && count($defaultValue) > 0) {
            $html = $html . "<label>$labelOld</label>";
            $html = $html . "<div class='row mt-2' id='div_multiple_old_$name'>";
            foreach ($defaultValue as $item) {
                $btnItems = "<div class='col-auto ml-2 mr-2 mb-2' id='old_{$name}_{$item[self::KEY_NAME]}'>
                    <input type='hidden' name='old_{$name}_{$item[self::KEY_NAME]}' value='1'>
                    <button type='button' class='btn btn-sm btn-danger' onclick=\"deleteOldFile('{$item[self::KEY_NAME]}')\">
                        <i class='fas fa-times'></i>
                    </button>
                    <a target='_blank' href='{$item[self::KEY_URL]}' class='btn btn-outline-primary'>
                        <i class='fas fa-file mr-2'></i>
                        {$item[self::KEY_LABEL]}
                    </a>
                </div>";

                $html = $html . $btnItems;
            }

            $html = $html . "</div>";
        }

        $js = self::generatePreviewMultipleFileJs($name, $sizeLimit);

        return ['html' => $html, 'js' => $js];
    }

    //------------- OTHER -------------
    private static function generateHeader($inputField)
    {
        $label = $inputField[self::KEY_LABEL];

        $html = "<h4 class=' text-primary'>$label</h4><hr>";
        return $html;
    }

    private static function generateButton($label = "Save")
    {
        $html = "<button id='btn_submit' class='btn btn-primary mt-3'>
            <i class='fas fa-check'></i>
            <span class='text'>$label</span>
        </button>";
        return $html;
    }

    private static function generatePreviewFileButton($defaultValue = "", $label = "View File")
    {
        if (!empty($defaultValue)) {
            $html = "<a href='$defaultValue' type='button' target='_blank' class='btn btn-primary mt-3'>
                <i class='fas fa-file'></i>
                <span class='text'>$label</span>
            </a>";
        } else {
            $html = "<button type='button' class='btn btn-secondary mt-3'>
                <i class='fas fa-file'></i>
                <span class='text'>$label</span>
            </button>";
        }

        return $html;
    }

    private static function generatePreviewImageHtml(
        $name,
        $width = 300,
        $height = 300,
        $defaultValue = ""
    ) {
        if (empty($defaultValue)) {
            $html = "<div><img class='mb-4 mt-2' id='{$name}_preview' style='background-color:#DDDDDD; padding:8px; max-width:{$width}px; max-height:{$height}px'></div>";
        } else {
            $html = "<div><img class='mb-4 mt-2' id='{$name}_preview' style='background-color:#DDDDDD; padding:8px; max-width:{$width}px; max-height:{$height}px' src='$defaultValue'></div>";
        }
        return $html;
    }

    //-------------JAVA SCRIPT-------------
    private static function generatePreviewImageJs($name, $limit = false)
    {
        if ($limit) {
            $limitInMB = $limit / 1000000;

            $js = "<script>$('#$name').change(function() {
                let input = this;
                if (input.files.length > 0 && input.files[0]) {
                    if (this.files[0].size > $limit) {
                        alert('Maximum File Size $limitInMB MB');
                        this.value = '';
                    } else{
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#{$name}_preview').attr('src', e.target.result);
                            $('#{$name}_label').html(input.files[0].name);
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            });</script>";
        } else {
            $js = "<script>$('#$name').change(function() {
                let input = this;
                if (input.files.length > 0 && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#{$name}_preview').attr('src', e.target.result);
                        $('#{$name}_label').html(input.files[0].name);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            });</script>";
        }

        return $js;
    }

    private static function generatePreviewFileJs($name, $limit = false)
    {
        if ($limit) {
            $limitInMB = $limit / 1000000;

            $js = "<script>$('#$name').change(function() {
                let input = this;
                if (input.files.length > 0 && input.files[0]) {
                    if(this.files[0].size > $limit){
                        alert('Maximum File Size $limitInMB MB');
                        this.value = '';
                    } else{
                        $('#{$name}_label').html(input.files[0].name);
                    }
                }
            });</script>";
        } else {
            $js = "<script>$('#$name').change(function() {
                let input = this;
                if (input.files.length > 0 && input.files[0]) {
                    $('#{$name}_label').html(input.files[0].name);
                }
            });</script>";
        }

        return $js;
    }

    private static function generatePreviewMultipleFileJs($name, $limit = false)
    {
        if ($limit) {
            $limitInMB = $limit / 1000000;

            $js = "<script>
                $('#$name').change(function() {
                    let input = this;
                    if (input.files.length > 0) {
                        $('#div_multiple_$name').html('');

                        for (let i = 0; i < input.files.length; i++) {
                            item = input.files[i];

                            if(item.size > $limit){
                                alert('Maximum File Size $limitInMB MB');
                                $('#div_multiple_$name').html('');
                                this.value = '';
                                break;
                            }

                            let html = \"<button type='button' class='btn btn-outline-primary col-auto ml-2 mr-2 mb-2'><i class='fas fa-file mr-2'></i>\"+item.name+\"</button>\";
                            $('#div_multiple_$name').append(html);
                        }
                    }
                });

                function deleteOldFile(id){
                    $('#old_{$name}_'+id).remove();
                }
            </script>";
        } else {
            $js = "<script>
                $('#$name').change(function() {
                    let input = this;
                    if (input.files.length > 0) {
                        $('#div_multiple_$name').html('');

                        for (let i = 0; i < input.files.length; i++) {
                            item = input.files[i];
                            let html = \"<button type='button' class='btn btn-outline-primary col-auto ml-2 mr-2 mb-2'><i class='fas fa-file mr-2'></i>\"+item.name+\"</button>\";
                            $('#div_multiple_$name').append(html);
                        }
                    }
                });

                function deleteOldFile(id){
                    $('#old_{$name}_'+id).remove();
                }
            </script>";
        }

        return $js;
    }

    private static function generateCurrencyJs($name)
    {
        $js = "<script>
        IMask(document.getElementById('$name'), {
            mask: Number,
            thousandsSeparator: '.',
            radix: ',',
        })
        </script>";

        return $js;
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
