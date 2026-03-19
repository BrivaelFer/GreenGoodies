<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(ProductRepository $productRepository): Response
    {
        /** @var User */
        $user = $this->getUser();
        $porducts = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'user' => $user,
            'products' => $porducts
        ]);
    }

    #[Route('/product/{id}', name: 'app_product')]
    public function showProduct(Product $product): Response
    {
        /** @var User */
        $user = $this->getUser();

        return $this->render('product/index.html.twig', [
            'user' => $user,
            'product' => $product
        ]);
    }

    #[Route('/api/products', name: 'api_procucts', methods:['GET'])]
    public function showProductsList(ProductRepository $productRepository): JsonResponse
    {
        return $this->json([],200);
    }
}
