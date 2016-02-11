<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/1/2016
 * Time: 11:22 AM
 */

namespace lillockey\Utilities\App\File;
use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;
use lillockey\Utilities\App\Containers\FileStats;
use lillockey\Utilities\App\Containers\PathInfo;

/**
 * <strong>Class File</strong>
 * <p> This class is a wrapper for the regularly used file functions in php </p>
 *
 * @package lillockey\Utilities\App\File
 */
class File
{

    /** @var string $path */
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Tests, Stats, and Administration
    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Given a string containing the path to a file or directory, this function will return the trailing name component.
     * @param string $suffix
     * @return string
     * @see \basename
     * http://php.net/manual/en/function.basename.php
     */
    public function basename($suffix = null)
    {
        return basename($this->path, $suffix);
    }

    /**
     * Attempts to change the group of the file filename to group.<br/>
     * Only the superuser may change the group of a file arbitrarily; other users may change the group of a file to any group of which that user is a member.
     *
     * @param $group
     * @return bool
     * @see \chgrp
     * http://php.net/manual/en/function.chgrp.php
     */
    public function chgrp($group)
    {
        return chgrp($this->path, $group);
    }

    /**
     * Attempts to change the mode of the specified file to that given in mode.
     *
     * @param $mode
     * @return bool
     * @see \chmod
     * http://php.net/manual/en/function.chmod.php
     */
    public function chmod($mode)
    {
        return chmod($this->path, $mode);
    }

    /**
     * Attempts to change the owner of the file filename to user user. Only the superuser may change the owner of a file.
     *
     * @param $user
     * @return bool
     * @see \chown
     * http://php.net/manual/en/function.chown.php
     */
    public function chown($user)
    {
        return chown($this->path, $user);
    }

    /**
     * Makes a copy of the file source to dest.
     *
     * @param File $dest
     * @param resource $context - A valid context resource created with stream_context_create().
     * @return bool
     * @see \copy
     * http://php.net/manual/en/function.copy.php
     */
    public function copy(File &$dest, $context = null)
    {
        if($dest == null) return false;
        return copy($this->getPath(), $dest->getPath(), $context);
    }

    /**
     * @param File $dest
     * @return bool
     */
    public function rename(File $dest)
    {
        if($dest == null) return false;
        return rename($this->path, $dest->getPath());
    }

    /**
     * @param File $target
     * @param bool|true $replace_current_symlink
     * @return bool
     */
    public function make_symlink(File $target, $replace_current_symlink = true)
    {
        if($replace_current_symlink === true && $this->is_link())
            $this->delete();

        return symlink($target->getPath(), $this->path);
    }

    /**
     * Attempts to create a file via touch.  If a file already exists, it is deleted.
     * @param int $time     The touch time
     * @param int $atime    The access time
     * @return bool
     * @see http://php.net/manual/en/function.touch.php
     */
    public function make_file($time = null, $atime = null)
    {
        if($this->is_dir())
            return false;

        if($this->is_file() && $this->is_writable())
            $this->delete();

        return $this->touch($time, $atime);
    }

    /**
     * Attempts to set the access and modification times of the file named in the filename parameter to the value given in time. Note that the access time is always modified, regardless of the number of parameters.
     * @param int $time     The touch time
     * @param int $atime    The access time
     * @return bool
     * @see http://php.net/manual/en/function.touch.php
     */
    public function touch($time = null, $atime = null)
    {
        return touch($this->path, $time, $atime);
    }

    /**
     * Given a string containing the path of a file or directory, this function will return the parent directory's path that is levels up from the current directory.
     *
     * @return string
     *
     * @see \dirname
     * http://php.net/manual/en/function.dirname.php
     */
    public function dirname()
    {
        return dirname($this->path);
    }

    /**
     * This function will return the total number of bytes on the corresponding filesystem or disk partition.
     * @return float
     * @see \disk_total_space
     * http://php.net/manual/en/function.disk-total-space.php
     */
    public function disk_total_space()
    {
        if($this->is_file()){
            return disk_total_space($this->dirname());
        }else{
            return disk_total_space($this->path);
        }
    }

    /**
     * This function will return the number of bytes available on the corresponding filesystem or disk partition.
     * @return float
     * @see \disk_free_space
     * http://php.net/manual/en/function.disk-free-space.php
     */
    public function disk_free_space()
    {
        if($this->is_file()){
            return disk_free_space($this->dirname());
        }else{
            return disk_free_space($this->path);
        }
    }

    /**
     * Attempts to create the directory specified by pathname.
     * @param int $mode
     * @param bool $recursive
     * @return bool
     * @see http://php.net/manual/en/function.mkdir.php
     */
    public function mkdir($mode = 0777, $recursive = false)
    {
        return mkdir($this->path, $mode, $recursive);
    }

    /**
     * Returns information about a file path
     * @return PathInfo
     * @see http://php.net/manual/en/function.pathinfo.php
     */
    public function pathinfo()
    {
        return new PathInfo(new AccessibleArray(pathinfo($this->path)));
    }

    /**
     * Deletes a file
     * @return bool
     * @see http://php.net/manual/en/function.touch.php
     */
    public function unlink()
    {
        return @unlink($this->path);
    }

    /**
     * Alias of unlink
     * @return bool
     */
    public function delete()
    {
        return $this->unlink();
    }

    /**
     * Gives information about a file
     * @return FileStats|null
     * @see http://php.net/manual/en/function.stat.php
     */
    public function stat()
    {
        if($this->exists()){
            if($this->is_file()){
                return new FileStats(new AccessibleArray(stat($this->path)));
            }else{
                $stat = new FileStats(new AccessibleArray(stat($this->path)));
                $stat->size = $this->dir_size($this->path);
                return $stat;
            }
        }
        return null;
    }

    /**
     * Returns canonicalized absolute pathname
     * @return string | FALSE
     * @see http://php.net/manual/en/function.realpath.php
     */
    public function realPath()
    {
        return realpath($this->path);
    }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Identifiers
    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Does this file or directory exist?
     * @return bool
     * @see \file_exists
     * http://php.net/manual/en/function.file-exists.php
     */
    public function exists()
    {
        return @file_exists($this->path);
    }

    /**
     * Tells whether the given filename is a directory.
     * @return bool
     * @see http://php.net/manual/en/function.is-dir.php
     */
    public function is_dir()
    {
        if(!$this->exists()) return false;
        return @is_dir($this->path);
    }

    /**
     * Tells whether the filename is executable
     * @return bool
     * @see http://php.net/manual/en/function.is-executable.php
     */
    public function is_executable()
    {
        if(!$this->exists()) return false;
        return @is_executable($this->path);
    }

    /**
     * Tells whether the given file is a regular file.
     * @return bool
     * @see http://php.net/manual/en/function.is-file.php
     */
    public function is_file()
    {
        if(!$this->exists()) return false;
        return @is_file($this->path);
    }

    /**
     * Tells whether the given file is a symbolic link.
     * @return bool
     * @see http://php.net/manual/en/function.is-link.php
     */
    public function is_link()
    {
        if(!$this->exists()) return false;
        return @is_link($this->path);
    }

    /**
     * Tells whether the filename is writable
     * @return bool
     */
    public function is_writable()
    {
        if(!$this->exists()) return false;
        return @is_writable($this->path);
    }

    /**
     * Tells whether a file exists and is readable.
     * @return bool
     * @see http://php.net/manual/en/function.is-readable.php
     */
    public function is_readable()
    {
        if(!$this->exists()) return false;
        return @is_readable($this->path);
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Basic Reading & Writing
    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * This function is similar to file(), except that file_get_contents() returns the file in a string, starting at the specified offset up to maxlen bytes. On failure, file_get_contents() will return FALSE.
     * file_get_contents() is the preferred way to read the contents of a file into a string. It will use memory mapping techniques if supported by your OS to enhance performance.
     *
     * @param int $offset
     * @param int $maxlen
     * @return string
     * @see http://php.net/manual/en/function.file-get-contents.php
     */
    public function get_contents($offset = null, $maxlen = null)
    {
        return @file_get_contents($this->path, null, null, $offset, $maxlen);
    }

    /**
     * This function is identical to calling fopen(), fwrite() and fclose() successively to write data to a file.
     * If filename does not exist, the file is created. Otherwise, the existing file is overwritten, unless the FILE_APPEND flag is set.
     * @param $data
     * @return int
     * @see http://php.net/manual/en/function.file-put-contents.php
     */
    public function put_contents($data)
    {
        return @file_put_contents($this->path, $data);
    }

    /**
     * Reads an entire file into an array.
     * @return array
     * @see http://php.net/manual/en/function.file.php
     */
    public function lines()
    {
        return @file($this->path);
    }

    /**
     * Reads a file and writes it to the output buffer.
     * @return int
     * @see http://php.net/manual/en/function.readfile.php
     */
    public function readfile()
    {
        return @readfile($this->path);
    }

    /**
     * Returns the target of a symbolic link
     * @param bool $null_on_non_link - [optional] When set to false, will return $this if the path is not a symlink.
     *                                            When set to true, will return null if the path is not a symlink.
     * @return File|null
     */
    public function readlink($null_on_non_link = false)
    {
        if($this->is_link()){
            return new File(readlink($this->path));
        }else{
            if($null_on_non_link)
                return null;
            else
                return $this;
        }
    }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Utilities
    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Used in runing the stat function against a directory
     * @param $dir
     * @return int
     * @see http://php.net/manual/en/function.stat.php
     * @author marting.dc AT gmail.com
     */
    private function dir_size($dir)
    {
        $handle = opendir($dir);
        $mas = 0;

        while ($file = readdir($handle)) {
            if ($file != '..' && $file != '.' && !is_dir($dir.'/'.$file)) {
                $mas += filesize($dir.'/'.$file);
            } else if (is_dir($dir.'/'.$file) && $file != '..' && $file != '.') {
                $mas += $this->dir_size($dir.'/'.$file);
            }
        }
        return $mas;
    }

}