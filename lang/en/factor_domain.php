<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Domain bypass';
$string['privacy:metadata'] = 'The Domain bypass factor plugin does not store any personal data.';
$string['settings:enabled'] = 'Enable domain factor';
$string['settings:enabled_help'] = 'Enable or disable the domain bypass factor. When enabled, users from configured domains can bypass MFA requirements.';
$string['settings:weight'] = 'Factor weight';
$string['settings:weight_help'] = 'The weight this factor contributes towards the total required for login (typically 100). Set to 100 for complete bypass, or lower values to combine with other factors.';
$string['settings:alloweddomains'] = 'Allowed domains';
$string['settings:alloweddomains_help'] = 'Enter one domain per line. Users with email addresses from these domains will be able to bypass MFA. Example: example.com, university.edu';
$string['summarycondition'] = 'User email domain is in allowed list';
$string['info'] = 'Domain bypass factor allows users from specified email domains to bypass multi-factor authentication.';
$string['error:nodomainsconfigured'] = 'No domains have been configured for domain bypass.';
$string['error:invaliddomain'] = 'Invalid domain format detected in configuration.';
