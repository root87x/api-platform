<?php

namespace App\EventListener;

use App\Entity\Storage;
use Vich\UploaderBundle\Event\Event;

class PostUploaderListener
{
    public function onVichUploaderPostUpload(Event $event): void
    {
        if (!$event->getObject() instanceof Storage) {
            return;
        }

        $this->addStorageAttributes($event);
    }

    private function addStorageAttributes(Event $event): void
    {
        $ext = $event->getObject()->getFile()->getExtension();
        $event->getObject()->setExtension($ext);
    }
}
