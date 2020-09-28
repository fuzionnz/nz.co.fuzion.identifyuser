<?php

/**
 * Settings used by nz.co.fuzion.identifyuser.
 */
return [
  'enable_lookup' => [
    'group_name' => 'Identify User',
    'group' => 'enable_lookup',
    'name' => 'enable_lookup',
    'type' => 'Array',
    'default' => [],
    'add' => '4.6',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Enable User Lookup on the live Page?',
    'help_text' => 'Enabling this will allow user to enter their details on the dedupe rule fields before filling the complete form.',
  ],
];
