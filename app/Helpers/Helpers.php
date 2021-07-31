<?php

function clean($text){
    $search = [
        'amp;',
        '&lt;',
        '&gt;',
        '&nbsp;',
        '<span style="font-size: 14.4px;">',
        '</span>'
    ];

    $replace = [
        '',
        '<',
        '>',
        ' ',
        '',
        ''
    ];

    return str_replace($search, $replace, $text);
}