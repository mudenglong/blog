<?php

namespace Redwood\Service\Note\Dao;

interface JswidgetDao
{
    public function addJswidget($jswidget);
    
	public function updateJswidget($id, $jswidget);

    public function deleteJswidget($id);

    public function getJswidget($id);

    public function searchJswidgetCount(array $conditions);

    public function searchJswidget($conditions, $orderBy, $start, $limit);

    public function waveJswidget($id, $field, $diff); 
}