<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryApiController extends AbstractController
{
    /**
     * @Route("/category-list", format="json", name="app_category_list")
     */
    public function list(EntityManagerInterface $em): JsonResponse
    {
        /** @var Category[] $categories */
        $categories = $em->getRepository(Category::class)->findAll();
        $response = [];

        foreach ($categories as $category) {
            $response[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }

        return $this->json($response);
    }

    /**
     * @Route("/category-read/{id}", format="json", name="app_category_read")
     */
    public function read(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var Category $category */
        $category = $em->getRepository(Category::class)->find($id);

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
        ]);
    }

    /**
     * @Route("/category-create", methods={"POST"}, format="json", name="app_category_create")
     */
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $category = new Category();
        $form = $this->container->get('form.factory')->createNamed('', CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->json([
                'success' => true,
                'id' => $category->getId(),
            ]);
        }

        return $this->json([
            'error' => "Form is not valid",
        ]);
    }

    /**
     * @Route("/category-edit/{id}", methods={"POST"}, format="json", name="app_category_edit")
     */
    public function edit(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var EntityRepository $repository */
        $repository = $em->getRepository(Category::class);
        $category = $repository->find($id);

        if (empty($category)) {
            return $this->json([
                'error' => 'category.not_found',
            ]);
        }

        $form = $this->container->get('form.factory')->createNamed('', CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->json([
                'success' => true,
            ]);
        }

        return $this->json([
            'error' => "Form is not valid",
        ]);
    }


    /**
     * @Route("/category-delete/{id}", methods={"POST"}, format="json", name="app_category_delete")
     */
    public function delete(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var EntityRepository $repository */
        $repository = $em->getRepository(Category::class);
        $category = $repository->find($id);

        if (empty($category)) {
            return $this->json([
                'error' => 'category.not_found',
            ]);
        }

        $em->remove($category);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }

}
