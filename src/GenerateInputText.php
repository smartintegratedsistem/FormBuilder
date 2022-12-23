<?php

namespace Sis\FormBuilder;

trait GenerateInputText
{
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

}
