<?php

namespace App\EventSubscriber;

use App\Entity\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\TaskEvent;

class TaskMailerSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onTaskCreated(TaskEvent $event)
    {
        $this->sendEmail("[TODO] Nueva tarea", $event);
    }

    public function onTaskDeleted(TaskEvent $event)
    {
        $this->sendEmail("[TODO] Tarea actualizada", $event);
    }

    public function onTaskUpdated(TaskEvent $event)
    {
        $this->sendEmail("[TODO] Tarea eliminada", $event);
    }

    public static function getSubscribedEvents()
    {
        return [
           'task.created' => 'onTaskCreated',
           'task.updated' => 'onTaskUpdated',
           'task.deleted' => 'onTaskDeleted',
        ];
    }

    protected function sendEmail(string $subject, TaskEvent $event)
    {
        $task = $event->getTask();

        $message = (new \Swift_Message($subject))
            ->setFrom('tasker@localhost.localdomain')
            ->setTo($task->getOwner()->getEmail())
            ->setBody($task->getDescription())
        ;

        $this->mailer->send($message);
    }
}
