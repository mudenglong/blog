<?php
namespace Redwood\Service\Note;

interface NoteService
{
	public function createNote($note);

    public function findNoteById($id);

}