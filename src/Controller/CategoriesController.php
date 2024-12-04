<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CategoriesRepository $repository): JsonResponse
    {
        $categories = $repository->findAll();
        return $this->json($categories);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return $this->json(['error' => 'Name is required'], 400);
        }

        $category = new Categories();
        $category->setName($data['name']);

        $this->em->persist($category);
        $this->em->flush();

        return $this->json($category, 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Categories $category): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return $this->json(['error' => 'Name is required'], 400);
        }

        $category->setName($data['name']);
        $this->em->flush();

        return $this->json($category);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Categories $category): JsonResponse
    {
        $this->em->remove($category);
        $this->em->flush();

        return $this->json(null, 204);
    }
}