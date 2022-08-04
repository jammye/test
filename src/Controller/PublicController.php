<?php

namespace App\Controller;

use App\Entity\CartProduct;
use App\Repository\CartProductRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PublicController
 * @package App\Controller
 */
class PublicController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Cart
     */
    private $cart;

    /**
     * PublicController constructor.
     * @param EntityManagerInterface $em
     * @param Cart $cart
     */
    public function __construct(EntityManagerInterface $em, Cart $cart)
    {
        $this->em = $em;
        $this->cart = $cart->getCart();
    }

    /**
     * @Route("/" , name="public.index")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository):Response
    {
        return $this->render('public/index.twig' , [
            'products' => $productRepository->findAll(),
            'cart' => $this->cart
        ]);
    }

    /**
     * @Route("/add/{id}" , name="cart.add", requirements={"id": "[0-9]*"})
     * @param int $id
     * @param ProductRepository $productRepository
     * @param CartProductRepository $cartProductRepository
     * @return Response
     */
    public function cart_add(int $id, ProductRepository $productRepository, CartProductRepository $cartProductRepository):Response
    {
        $product = $productRepository->find( $id );

        if ( $product )
        {
            /**
             * @var $cartProduct CartProduct
             */
            $cartProduct = $cartProductRepository->findOneBy(['cart' => $this->cart->getId(), 'product' => $id]);

            if ( $cartProduct )
            {
                if ( $product->getStock() > 0 )
                {
                    if ( $product->getStock() > $cartProduct->getQuantity() )
                    {
                        $cartProduct->setQuantity( $cartProduct->getQuantity() + 1 );
                        $this->em->flush();

                        $this->addFlash('success', 'Article ajouté');
                    }
                    else
                    {
                        $this->addFlash('danger', 'Le stock n\'est pas suffisant');
                    }
                }
                else
                {
                    $this->em->remove( $cartProduct );
                    $this->em->flush();

                    $this->addFlash('danger', 'Le stock n\'est pas suffisant');
                }
            }
            else if ( $product->getStock() == 0 )
            {
                $this->addFlash('danger', 'Le stock n\'est pas suffisant');
            }
            else
            {
                $cartProduct = new CartProduct();
                $cartProduct->setCart( $this->cart )
                    ->setQuantity(1)
                    ->setProduct( $product );
                $this->em->persist( $cartProduct );
                $this->em->flush();

                $this->addFlash('success', 'Article ajouté');
            }
        }
        else
        {
            $this->addFlash('danger', 'Article introuvable');
        }

        return $this->redirectToRoute('public.index');
    }

    /**
     * @Route("/remove/{id}" , name="cart.remove", requirements={"id": "[0-9]*"})
     * @param int $id
     * @param ProductRepository $productRepository
     * @param CartProductRepository $cartProductRepository
     * @return Response
     */
    public function cart_remove(int $id, ProductRepository $productRepository, CartProductRepository $cartProductRepository):Response
    {
        $product = $productRepository->find( $id );

        if ( $product )
        {
            /**
             * @var $cartProduct CartProduct
             */
            $cartProduct = $cartProductRepository->findOneBy(['cart' => $this->cart->getId(), 'product' => $id]);

            if ( $cartProduct )
            {
                if ( $cartProduct->getQuantity() > 1 )
                {
                    $cartProduct->setQuantity( $cartProduct->getQuantity() - 1 );
                    $this->em->flush();

                    $this->addFlash('success', 'Quantité modifiée');
                }
                else
                {
                    $this->em->remove( $cartProduct );
                    $this->em->flush();

                    $this->addFlash('success', 'Article retiré');
                }
            }
            else
            {
                $this->addFlash('danger', 'Produit introuvable dans votre panier');
            }
        }
        else
        {
            $this->addFlash('danger', 'Article introuvable');
        }

        return $this->redirectToRoute('public.index');
    }

    /**
     * @Route("/trash/{id}" , name="cart.trash", requirements={"id": "[0-9]*"})
     * @param int $id
     * @param ProductRepository $productRepository
     * @param CartProductRepository $cartProductRepository
     * @return Response
     */
    public function cart_trash(int $id, ProductRepository $productRepository, CartProductRepository $cartProductRepository):Response
    {
        $product = $productRepository->find( $id );

        if ( $product )
        {
            /**
             * @var $cartProduct CartProduct
             */
            $cartProduct = $cartProductRepository->findOneBy(['cart' => $this->cart->getId(), 'product' => $id]);

            if ( $cartProduct )
            {
                $this->em->remove( $cartProduct );
                $this->em->flush();

                $this->addFlash('success', 'Article retiré');
            }
            else
            {
                $this->addFlash('danger', 'Produit introuvable dans votre panier');
            }
        }
        else
        {
            $this->addFlash('danger', 'Article introuvable');
        }

        return $this->redirectToRoute('public.index');
    }
}