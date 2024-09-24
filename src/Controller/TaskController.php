<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/', name: 'task_index')]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/create', name: 'task_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $titre = $request->request->get('titre');
        $description = $request->request->get('description');
    
        if (!$titre) {
            return new Response('Titre obligatoire', 400);
        }
    
        if (!$description) {
            return new Response('Description obligatoire', 400);
        }
    
        $task = new Task();
        $task->setTitre($titre);
        $task->setDescription($description);
        $task->setFini(false);
    
        $em->persist($task);
        $em->flush();
    
        return $this->redirectToRoute('task_index');
    }
    

    #[Route('/delete/{id}', name: 'task_delete', methods: ['POST'])]
    public function delete(Task $task, EntityManagerInterface $em): Response
    {
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('task_index');
    }

    #[Route('/terminer/{id}', name: 'terminer', methods: ['POST'])]
    public function terminer(Task $task, EntityManagerInterface $em): Response
    {
        $task->setFini(!$task->isFini());
        $em->flush();

        return $this->redirectToRoute('task_index');
    }
}
