<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/api/products', name: 'api_products', methods:['GET'])]
    #[IsGranted('FULLY_AUTHENTICATED')]
    public function showProductsList(ProductRepository $productRepository): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        if(!$user->isApiEnable()){
            return $this->json(['error' => 'API access not enabled for this user'],403);
        }
        $products = $productRepository->findAll();

        return $this->json($products,200);
    }
}
