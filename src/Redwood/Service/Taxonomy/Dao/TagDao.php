<?php

namespace Redwood\Service\Taxonomy\Dao;

interface TagDao
{
    public function addTag($tag);
    
	public function updateTag($id, $tag);

    public function deleteTag($id);

    public function getTag($id);

    public function getTagByName($name);

    public function getTagByLikeName($name);

    public function getTagsByIds(array $ids);

    public function getTagsByNames(array $names);

    public function searchTagCount(array $conditions);

    public function searchTags($conditions, $orderBy, $start, $limit);
}