<?php
namespace Redwood\Service\Note;

interface JswidgetService
{
	public function createJswidget($jswidget);

    public function updateJswidget($jswidget);

    public function deleteJswidget($id);

    public function getJswidget($id);

    public function findJswidgetById($id);

    public function searchJswidgetCount($conditions);
    
    public function searchJswidget(array $conditions, $oderBy, $start, $limit);

}