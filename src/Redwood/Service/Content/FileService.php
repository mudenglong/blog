<?php
namespace Redwood\Service\Content;

use Symfony\Component\HttpFoundation\File\File;

interface FileService
{
	public function uploadAvatar($filePath, $options);

    public function sqlUriConvertAbsolutUri($sqlUri);

    public function uploadFile($group, File $file);
    
    public function writeFile($includePathFilename, $content);
    
    public function zipFolder($includePathFilename, $definedFileName=null);


}