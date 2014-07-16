<?php
namespace Redwood\Common;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileToolkit
{

    public static function isImageFile(File $file) 
    {
        $ext = self::getFileExtension($file);
        // @todo
        // return in_array(strtolower($ext), explode(' ', self::getImageExtensions()));
    }

    public static function getFileExtension(File $file)
    {
        return $file instanceof UploadedFile ? $file->getClientOriginalExtension() : $file->getExtension();
    }

}