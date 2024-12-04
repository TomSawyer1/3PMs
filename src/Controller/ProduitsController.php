<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Entity\Categories;
use App\Repository\ProduitsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/produits', name: 'produits_')]
class ProduitsController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ProduitsRepository $repository): JsonResponse
    {
        $produits = $repository->findAll();
        return $this->json($produits);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CategoriesRepository $categoriesRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['description']) || empty($data['price']) || empty($data['category_id'])) {
            return $this->json(['error' => 'All fields are required'], 400);
        }

        $category = $categoriesRepository->find($data['category_id']);
        if (!$category) {
            return $this->json(['error' => 'Category not found'], 404);
        }

        $produit = new Produits();
        $produit->setName($data['name']);
        $produit->setDescription($data['description']);
        $produit->setPrice($data['price']);
        $produit->setCreatedAt(new \DateTimeImmutable());
        $produit->setCategories($category);

        $this->em->persist($produit);
        $this->em->flush();

        return $this->json($produit, 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Produits $produit, CategoriesRepository $categoriesRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['description']) || empty($data['price']) || empty($data['category_id'])) {
            return $this->json(['error' => 'All fields are required'], 400);
        }

        $category = $categoriesRepository->find($data['category_id']);
        if (!$category) {
            return $this->json(['error' => 'Category not found'], 404);
        }

        $produit->setName($data['name']);
        $produit->setDescription($data['description']);
        $produit->setPrice($data['price']);
        $produit->setCategories($category);

        $this->em->flush();

        return $this->json($produit);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Produits $produit): JsonResponse
    {
        $this->em->remove($produit);
        $this->em->flush();

        return $this->json(null, 204);
    }
}