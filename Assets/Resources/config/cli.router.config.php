<?php

return [
    // Route for assets
    '/^(assets)\:([A-Za-z-_]+)$/i' => [
        'type'       => 'RegExp',
        'important'  => true,
        'module'     => 'Assets',
        'controller' => 'assets',
        'action'     => '',
        'matches'    => ['', '', 'action'],
    ]
];