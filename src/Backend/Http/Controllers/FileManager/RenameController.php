<?php

namespace Juzaweb\Cms\Backend\Http\Controllers\FileManager;

use Juzaweb\Cms\Core\Models\File;
use Juzaweb\Cms\Core\Models\Folder;

class RenameController extends FileManagerController
{
    public function getRename()
    {
        $file = request()->input('file');
        $new_name = request()->input('new_name');
        
        $is_directory = $this->isDirectory($file);

        if (empty($new_name)) {
            if ($is_directory) {
                return parent::error('folder-name');
            } else {
                return parent::error('file-name');
            }
        }

        if ($is_directory) {
            Folder::where('id', '=', $file)
                ->update([
                    'name' => $new_name
                ]);
        } else {
            $file_path = explode('uploads/', $file)[1];
            
            File::where('path', '=', $file_path)
                ->update([
                    'name' => $new_name
                ]);
        }
        
        return parent::$success_response;
    }
}
