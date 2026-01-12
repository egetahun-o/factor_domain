<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configcheckbox(
    'factor_domain/enabled',
    new lang_string('settings:enabled', 'factor_domain'),
    new lang_string('settings:enabled_help', 'factor_domain'),
    0,
    '1',
    '0'
));


$settings->add(new admin_setting_configtext(
    'factor_domain/weight',
    new lang_string('settings:weight', 'factor_domain'),
    new lang_string('settings:weight_help', 'factor_domain'),
    100,
    PARAM_INT
));

$settings->add(new admin_setting_configtextarea(
    'factor_domain/alloweddomains',
    new lang_string('settings:alloweddomains', 'factor_domain'),
    new lang_string('settings:alloweddomains_help', 'factor_domain'),
    '',
    PARAM_TEXT
));