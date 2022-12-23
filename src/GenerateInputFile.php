<?php

namespace Sis\FormBuilder;

trait GenerateInputFile
{
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
}
