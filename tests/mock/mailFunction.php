<?php namespace m8rge;

function mail ($to, $subject, $message, $additional_headers = null, $additional_parameters = null) {
    return array($to, $subject, $message, $additional_headers);
}
