<?php
namespace Redwood\Common;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileToolkit
{
    //@todo 
    public static function isImageFile(File $file) 
    {
        $ext = self::getFileExtension($file);
        return in_array(strtolower($ext), explode(' ', self::getImageExtensions()));
    }

    public static function getFileExtension(File $file)
    {
        return $file instanceof UploadedFile ? $file->getClientOriginalExtension() : $file->getExtension();
    }

    public static function getSecureFileExtensions()
    {
        return 'jpg jpeg gif png txt doc docx xls xlsx pdf ppt pptx pps ods odp mp4 mp3 avi flv wmv wma mov zip rar gz tar 7z swf';
    }

    public static function getImageExtensions()
    {
        return 'bmp jpg jpeg gif png';
    }

    public static function validateFileExtension(File $file, $extensions = array())
    {
        if (empty($extensions)) {
            $extensions = self::getSecureFileExtensions();
        }

        if ($file instanceof UploadedFile) {
            $filename = $file->getClientOriginalName();
        } else {
            $filename = $file->getFilename();
        }

        $errors = array();
        $regex = '/\.(' . preg_replace('/ +/', '|', preg_quote($extensions)) . ')$/i';
        if (!preg_match($regex, $filename)) {
            $errors[] = "只允许上传以下扩展名的文件：" . $extensions;
        }
        return $errors;
    }

}