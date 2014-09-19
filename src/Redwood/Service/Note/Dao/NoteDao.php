<?php

namespace Redwood\Service\Note\Dao;

interface NoteDao
{
	public function addNote($note);

    public function getNote($id);

}