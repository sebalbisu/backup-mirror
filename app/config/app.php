<?php

$logDisk = '/common/sys/backup/log/mirror.log';
$inoutData = require('archivos-disk.php');

return 
[
    'archivos' => 
    [
        'src' => '/mnt/archivos',
        'dest' => 
        [
            'in' => 
            [
                'disk'   => '/dev/disk/by-label/backup',
                'mount'  => '/mnt/backup',
                'to'     => '/mirror/archivos',
                'adapter'=> 'disk',
                'log'    => $logDisk,
                'data'   => $inoutData,
            ],
            'out' => 
            [
                'disk'   => '/dev/disk/by-label/backup-seba', 
                'mount'  => '/mnt/backup-external',
                'to'     => '/mirror/archivos',
                'adapter'=> 'disk',
                'log'    => $logDisk,            
                'data'   => $inoutData,
            ],
            'web' => 
            [
                'adapter'=> 'web',
                'data'   => [],
            ],
        ],
    ],
];
