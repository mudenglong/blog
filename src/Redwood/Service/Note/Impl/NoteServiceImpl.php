<?php
namespace Redwood\Service\Note\Impl;

use Redwood\Service\Common\BaseService;
use Redwood\Service\Note\NoteService;

class NoteServiceImpl extends BaseService implements NoteService
{
    public function createNote($note)
    {
        $note['userId'] = $this->getCurrentUser()->id;
        $note['status'] = 'published';
        $note['createdTime'] = time();
        $note = $this->getNoteDao()->addNote($note);

    }

    public function findNoteById($id)
    {
        # code...
    }

    private function getNoteDao()
    {
        return $this->createDao('Note.NoteDao');
    }

}