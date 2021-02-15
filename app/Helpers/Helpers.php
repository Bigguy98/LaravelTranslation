<?php

function clean($text){
    $search = [
        'amp;',
        '&lt;',
        '&gt;',
        '&nbsp;'
    ];

    $replace = [
        '',
        '<',
        '>',
        ' '
    ];

    return str_replace($search, $replace, $text);
}