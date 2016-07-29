<?php

namespace Redwood\Service\Note\Dao;

interface JswidgetDao
{
    public function addJswidget($widget);
    
	public function updateJswidget($id, $jswidget);

    public function getJswidget($id);

}