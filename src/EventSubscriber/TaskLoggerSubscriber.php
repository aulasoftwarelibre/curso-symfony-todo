<?php

namespace App\EventSubscriber;

use App\Event\TaskEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskLoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
           'task.created' => 'onTaskCreated',
           'task.updated' => 'onTaskUpdated',
           'task.deleted' => 'onTaskDeleted',
        ];
    }

    public function onTaskCreated(TaskEvent $event)
    {
        $this->logEvent("TASK CREATED", $event);
    }

    public function onTaskUpdated(TaskEvent $event)
    {
        $this->logEvent("TASK UPDATED", $event);
    }

    public function onTaskDeleted(TaskEvent $event)
    {
        $this->logEvent("TASK DELETED", $event);
    }

    private function logEvent(string $description, TaskEvent $event)
    {
        $task = $event->getTask();
        $this->logger->info("[{$description}] {$task->getDescription()} by {$task->getOwner()}");
    }
}
