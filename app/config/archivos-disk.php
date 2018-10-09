<?php

return    
[             
    [
        'name' => 'archivos',
        'type' => 'rsync',
        'content' => 
        [
            'include' => 
            [
                "/*/home/.profile",
                "/*/home/.bashrc",
                "/*/home/.bash_history",
                "/*/home/.gitconfig",       
            ],
            'exclude' => 
            [
                "/*/home/VirtualBox\ VMs",
                "/*/home/.*",
                "/**/.cache",
                "/**/.metadata",
                "/**/vendor",
                "/**/vendors",
                "/**/bower_components",
                "/**/node_modules",
                "/*/project/**/storage/framework",
                "/*/project/**/storage/debugbar",
            ],
        ],
    ],
    [
        'name' => 'system',
        'type' => 'rsync',
        'content' => 
        [
           'include' => 
            [
                "/etc",
                "/etc/**",
                "/usr/",
                "/usr/local/",
                "/usr/local/src/",
                "/usr/local/src/**",
            ],
            'exclude' => 
            [
                "/**",
            ],   
        ]
    ],
    [
        'name' => 'system-other',
        'type' => 'command',
        'content' => 
        [
            ["php -m" 
                => "/common/sys/other/php_modules_list.txt"],

            ["ls -1lh /usr/local/bin/" 
                => "/common/sys/other/usr_local_bin.txt"],

            ["tree -L 2 /opt/" 
                => "/common/sys/other/opt.txt"], 
        ]
    ],
];  

