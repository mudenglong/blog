<?php
namespace Redwood\Service\Content\Impl;

use Redwood\Service\Common\BaseService;
use Redwood\Service\Content\FileService;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Redwood\Common\FileToolkit;

class FileServiceImpl extends BaseService implements FileService
{
    private $zip;

    private function newTempAvatar($image, $imageTarget)
    {
        $image->resize(new Box($imageTarget['width'], $imageTarget['height']));
        $image->save($imageTarget['filePath'], array('quality' => 90));

        return new File($imageTarget['filePath']);
    }

    public function uploadAvatar($filePath, $options)
    {
        
        $imagine = new Imagine();
        $basicImage = $imagine->open($filePath)->copy();
        $basicImage->crop(new Point($options['x'], $options['y']), new Box($options['width'], $options['height']));

        $pathinfo = pathinfo($filePath);

        $largeImageTarget = array(
                'width' => 200,
                'height' => 200,
                'filePath' => "{$pathinfo['dirname']}/{$pathinfo['filename']}_large.{$pathinfo['extension']}",
        );

        $mediumImageTarget = array(
                'width' => 120,
                'height' => 120,
                'filePath' => "{$pathinfo['dirname']}/{$pathinfo['filename']}_medium.{$pathinfo['extension']}",
        );

        $smallImageTarget = array(
                'width' => 48,
                'height' => 48,
                'filePath' => "{$pathinfo['dirname']}/{$pathinfo['filename']}_small.{$pathinfo['extension']}",
        );

        $tempLargeImage = $this->newTempAvatar($basicImage, $largeImageTarget);
        $largeImageInfo = $this->generateUri('userAvatar', $tempLargeImage);
        $largeAvatar = $this->saveFile($largeImageInfo, $tempLargeImage);

        $tempMediumImage = $this->newTempAvatar($basicImage, $mediumImageTarget);
        $mediumImageInfo = $this->generateUri('userAvatar', $tempMediumImage);
        $mediumAvatar = $this->saveFile($mediumImageInfo, $tempMediumImage);

        $tempSmallImage = $this->newTempAvatar($basicImage, $smallImageTarget);
        $smallImageInfo = $this->generateUri('userAvatar', $tempSmallImage);
        $smallAvatar = $this->saveFile($smallImageInfo, $tempSmallImage);

        return array(
            'largeAvatar' => $largeAvatar, 
            'mediumAvatar' => $mediumAvatar, 
            'smallAvatar' => $smallAvatar,
            'largeImageInfo' => $largeImageInfo,
            'mediumImageInfo' => $mediumImageInfo,
            'smallImageInfo' => $smallImageInfo,
        );

    }

    public function sqlUriConvertAbsolutUri($sqlUri)
    {
        return $this->getKernel()->getParameter('redwood.upload.public_directory') . '/' . str_replace('public://', '', $sqlUri);
    }

    private function saveFile($newImageInfo, $file)
    {
        $finalPath = $this->sqlUriConvertAbsolutUri($newImageInfo['directory']);
        // if (!is_writable($finalPath)) {
        //     throw $this->createServiceException("上传路径{$finalPath}不可写，文件上传失败。");
        // }
        return $file->move($finalPath, $newImageInfo['filename']);
    }
	

    private function generateUri($group, File $file)
    {

        if ($file instanceof UploadedFile) {
            $filename = $file->getClientOriginalName();
        } else {
            $filename = $file->getFilename();
        }

        $filenameParts = explode('.', $filename);
        $ext = array_pop($filenameParts);
        if (empty($ext)) {
            throw $this->createServiceException('获取文件扩展名失败！');
        }

        $newImage["directory"] = 'public://' . $group .'/'.date('Y') . '/' . date('m-d') . '/';
       
        $newImage["filename"] = date('His') . substr(uniqid(), - 6) . substr(uniqid('', true), - 6) . '.' . $ext;
        
        return $newImage;
    }

    public function uploadFile($group, File $file)
    {
        $errors = FileToolkit::validateFileExtension($file);
        if ($errors) {
            @unlink($file->getRealPath());
            throw $this->createServiceException("该文件格式，不允许上传。");
        }

        $newImage = $this->generateUri($group, $file);

        $user = $this->getCurrentUser();
        $record = array();
        $record['userId'] = $user['id'];
        $record['groupName'] = $group;
        $record['size'] = $file->getSize();
        $record['uri'] = $newImage['directory'] . $newImage['filename'];
        $record['createdTime'] = time();
        $record = $this->getFileDao()->addFile($record);

        $record['file'] = $this->saveFile($newImage, $file);

        return $record;
    }


    /**
     * write a html File
     * @param  string $cropDirPath example: public://cropHtml/...
     * @param  string $content     
     * @return boolen 
     */
    public function writeFile($cropDirPath, $content)
    {
        $includePathFilename = $cropDirPath .'/index.html';
        $path = $this->sqlUriConvertAbsolutUri($includePathFilename);
       
        $filesystem = new Filesystem();
        return $filesystem->dumpFile($path, $content);

    }

    public function zipFolder($cropDirPath)
    {
        $path = $cropDirPath.'/';
        $path = $this->sqlUriConvertAbsolutUri($path);
        $zip_file = '';
        $pathParts = explode('/', $cropDirPath);
        $zipName = array_pop($pathParts).'.zip';

        $this->zip = new \ZipArchive();
        $canOpen = $this->zip->open($path . $zipName, \ZipArchive::CREATE);
        if ($canOpen) {
            $this->getFilesFromFolder($path, $zip_file, $this->zip);
            $this->zip->close();
        }

    }

    private function getFilesFromFolder($directory, $destination, $zip) 
    {
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if (is_file($directory.$file)) {
                    $zip->addFile($directory.$file, $destination.$file);
                } elseif ($file != '.' and $file != '..' and is_dir($directory.$file)) {
                    $zip->addEmptyDir($destination.$file);
                    $this->getFilesFromFolder($directory.$file.'/', $destination.$file.'/', $zip);
                }
            }
        }
        closedir($handle);
    }


    private function getFileDao()
    {
        return $this->createDao('Content.FileDao');
    }

    /*
     *生成js文件夹
     */
    public function GenerateJsFile(){
        $path = $_SERVER['DOCUMENT_ROOT'];
        $cmd = "sh ../release.sh";
        $result = shell_exec($cmd);
    }


}
