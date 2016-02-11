<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/1/2016
 * Time: 11:17 AM
 */

namespace lillockey\Utilities\App\Access\DelimitedAccess;

use lillockey\Utilities\App\File\File;

class TabDelimitedFileDocument extends TabDelimitedDocument implements DelimitedSavable
{
    private $file;

    public function __construct(File $file, $first_row_headers = true)
    {
        if($file == null) throw new \Exception("The file may not be null");
        $this->file = $file;

        if($file == null || !$file->exists() || !$file->is_file() || !$file->is_readable()){
            parent::__construct('', false);
            return;
        }

        parent::__construct($file->get_contents(), $first_row_headers);
    }

    /**
     * Saves to the stored or given path
     * @param string $save_path
     * @return mixed
     */
    public function save($save_path = null)
    {
        if($save_path == null) $save_path = $this->file->getPath();
        return DelimitedFileWriter::saveAsTab($this, $save_path);
    }
}