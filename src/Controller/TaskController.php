<?php

namespace App\Controller;

use App\Entity\Task;
use App\Event\TaskEvent;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/", name="task_index", methods="GET")
     */
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findBy([
            'owner' => $this->getUser(),
        ], [
            'createdAt' => 'ASC',
        ]);

        return $this->render('task/index.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/new", name="task_new", methods="GET|POST")
     */
    public function new(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        $task = new Task();
        $task->setOwner($this->getUser());

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $dispatcher->dispatch('task.created', new TaskEvent($task));
            $this->addFlash('positive', 'Tarea creada con éxito');

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods="GET")
     */
    public function show(Task $task): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $task);

        return $this->render('task/show.html.twig', ['task' => $task]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods="GET|POST")
     */
    public function edit(Request $request, Task $task, EventDispatcherInterface $dispatcher): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $task);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $dispatcher->dispatch('task.updated', new TaskEvent($task));
            $this->addFlash('positive', 'Tarea actualizada con éxito');

            return $this->redirectToRoute('task_edit', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_delete", methods="DELETE")
     */
    public function delete(Request $request, Task $task, EventDispatcherInterface $dispatcher): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $task);

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            $dispatcher->dispatch('task.deleted', new TaskEvent($task));
            $this->addFlash('positive', 'Tarea borrada con éxito');
        }

        return $this->redirectToRoute('task_index');
    }
}
