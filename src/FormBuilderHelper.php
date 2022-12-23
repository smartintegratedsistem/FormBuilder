<?php

namespace Sis\FormBuilder;

/*
| Last Updated 2022-12-23
| New: Seperate File and Input Agreement
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
    const INPUT_TYPE_MONTH = "month";
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
    const INPUT_TYPE_AGREEMENT = "agreement";

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
    const KEY_PLACEHOLDER = "placeholder";
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

    use GenerateForm, GenerateInputDateTime, GenerateInputFile, GenerateInputNumber, GenerateInputOther, GenerateInputSelect, GenerateInputText;
}
