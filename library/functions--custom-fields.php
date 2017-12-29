<?php

function prepareHomepage()
{
    $home = array(
        'intro'   => get_field('field_5a46b822d4a25'),
        'content' => get_field('field_5a46b833d4a26'),
    );
    return $home;
}
