<?php
namespace Redwood\Service\Note;

interface JswidgetService
{
	public function createJswidget($jswidget);

    public function updateJswidget($jswidget);

    public function getJswidget($id);

    public function findJswidgetById($id);

}