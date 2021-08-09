<?php
/**
 * MYMO CMS - The Best Laravel CMS
 *
 * @package    juzawebcms/juzawebcms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://github.com/juzawebcms/juzawebcms
 * @license    MIT
 *
 * Created by The Anh.
 * Date: 5/27/2021
 * Time: 8:37 PM
*/

return [
    'admin_prefix' => env('ADMIN_PREFIX', 'admin-cp'),

    'filemanager' => [
        'disk' => 'public',
        'types' => [
            'file'  => [
                'max_size'     => 5, // size in MB
                'valid_mime'   => [
                    'image/jpeg',
                    'image/pjpeg',
                    'image/png',
                    'image/gif',
                    'image/svg+xml',
                    'application/pdf',
                    'text/plain',
                ],
            ],
            'image' => [
                'max_size'     => 50, // size in MB
                'valid_mime'   => [
                    'image/jpeg',
                    'image/pjpeg',
                    'image/png',
                    'image/gif',
                    'image/svg+xml',
                ],
            ],
        ],
    ]
];
