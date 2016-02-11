<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/3/2016
 * Time: 12:17 PM
 */

namespace lillockey\Utilities\App\Access\DelimitedAccess;


use lillockey\Utilities\App\File\File;

class DelimitedFileWriter
{
    public static function saveAsCsv(DelimitedDocument $document, $file_path)
    {
        $rows = $document->get_full_array();

        $str = "";
        $first = true;

        foreach($rows as $row){
            $row_text = "\"" . implode("\",\"", $row) . "\"";
            if($first){
                $first = false;
                $str = $row_text;
            }else{
                $str .= "\n$row_text";
            }
        }

        $f = self::get_writable_file($file_path);
        if($f == null) return false;
        return $f->put_contents($str);
    }

    public static function saveAsPipe(DelimitedDocument $document, $file_path)
    {
        return self::saveAsRawDelimited($document, "|", $file_path);
    }

    public static function saveAsTab(DelimitedDocument $document, $file_path)
    {
        return self::saveAsRawDelimited($document, "\t", $file_path);
    }

    public static function saveAsRawDelimited(DelimitedDocument $document, $glue, $file_path)
    {
        $rows = $document->get_full_array();

        $str = "";
        $first = true;

        foreach($rows as $row){
            $row_text = implode($glue, $row);
            if($first){
                $first = false;
                $str = $row_text;
            }else{
                $str .= "\n$row_text";
            }
        }

        $f = self::get_writable_file($file_path);
        if($f == null) return false;
        return $f->put_contents($str);
    }

    private static function get_writable_file($file_path)
    {
        $f = new File($file_path);

        //Ensure that all symlinks are followed to the end
        while($f->is_link()) $f = $f->readlink();

        if(!$f->exists()){
            //Just to make sure
            $real_path = $f->realPath();
            $real_file = new File($real_path);

            //Grab the path information
            $pinfo = $real_file->pathinfo();
            $directory = $pinfo->dirname;

            //Ensure the directory exists
            $dfile = new File($directory);
            if(!$dfile->exists()) $dfile->mkdir(0777, true);

            //Try to touch the file
            $real_file->touch();
        }

        if($f->is_dir()) return null;

        if($f->is_writable()) return $f;

        return null;
    }

}