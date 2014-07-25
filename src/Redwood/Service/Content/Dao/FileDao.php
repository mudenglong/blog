<?php

namespace Redwood\Service\Content\Dao;

interface FileDao
{

	public function getFile($id);

	public function findFiles($start, $limit);

	public function findFileCount();

	public function addFile($file);

	public function deleteFile($id);

}