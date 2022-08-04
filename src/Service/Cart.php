<?php

namespace App\Service;

use App\Entity\Cart as CartEntity;
use App\Entity\CartProduct;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CartProductRepository
     */
    private $cartProductRepository;
    /**
     * @var CartEntity
     */
    private $cart;
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * Cart constructor.
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     * @param CartProductRepository $cartProductRepository
     * @param CartRepository $cartRepository
     */
    public function __construct(EntityManagerInterface $em, SessionInterface $session, CartProductRepository $cartProductRepository, CartRepository $cartRepository)
    {
        $this->em = $em;
        $this->session = $session;
        $this->cartProductRepository = $cartProductRepository;

        if ( $this->session->get('cart') === null )
        {
            $this->newCart();
        }
        else
        {
            $cart = $cartRepository->find( $this->session->get('cart') );

            if ( $cart )
            {
                $this->setCart( $cart );
            }
            else
            {
                $this->newCart();
            }
        }
        $this->cartRepository = $cartRepository;
    }

    private function newCart()
    {
        $cart = new CartEntity();
        $cart->setDate(new \DateTime('now'));
        $this->em->persist( $cart );
        $this->em->flush();

        $this->session->set('cart', $cart->getId());
        $this->setCart( $cart );
    }

    /**
     * @return CartProduct[]|null
     */
    public function getProducts()
    {
        return $this->cart->getCartProducts();
    }

    /**
     * @return CartEntity
     */
    public function getCart(): CartEntity
    {
        return $this->cart;
    }

    /**
     * @param CartEntity $cart
     */
    public function setCart(CartEntity $cart): void
    {
        $this->cart = $cart;
    }
}