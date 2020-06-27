<?php


function getAnswer($command = '')
{

    $arResult = array(
        'title' => 'You said: ',
        'report' => $command,
    );

    return $arResult;
}
