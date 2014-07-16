<?php
namespace Redwood\Service\Content;

use Symfony\Component\HttpFoundation\File\File;

interface FileService
{
	public function uploadAvatar($filePath, $options);

    public function sqlUriConvertAbsolutUri($sqlUri);

    public function uploadHtmlPic($filePath, array $options);

    /*
    * 工具函数，根据分割线，获得要分割div的坐标及宽度高度
    */
    public function getCropDivCoordsByLines($lines, $totalHeight);
    
    public function writeFile($includePathFilename, $content);
    
    public function zipFolder($includePathFilename);

    public function downloadZip($secret);



}