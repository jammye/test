<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=CartProduct::class, mappedBy="cart", orphanRemoval=true)
     */
    private $cartProducts;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|CartProduct[]
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts[] = $cartProduct;
            $cartProduct->setCart($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartProduct->getCart() === $this) {
                $cartProduct->setCart(null);
            }
        }

        return $this;
    }

    public function getTotalHT()
    {
        $ht = 0;
        if ( $this->getCartProducts() )
        {
            foreach( $this->getCartProducts() as $row )
            {
                $ht = $row->getQuantity() * $row->getProduct()->getPrice() ;
            }
        }

        return $ht ;
    }

    public function getTotalTTC()
    {
        $ttc = 0;
        if ( $this->getCartProducts() )
        {
            foreach( $this->getCartProducts() as $row )
            {
                $ttc = $row->getQuantity() * $row->getProduct()->getPriceWithTax() ;
            }
        }

        return $ttc ;
    }

    public function getTotalTVA()
    {
        return $this->getTotalTTC() - $this->getTotalHT() ;
    }

    public function getTotalTVAFormat(): ?string
    {
        return number_format( $this->getTotalTVA() , 2 , ',' , ' ' ) . " €";
    }

    public function getTotalTTCFormat(): ?string
    {
        return number_format( $this->getTotalTTC() , 2 , ',' , ' ' ) . " €";
    }

    public function getTotalHTFormat(): ?string
    {
        return number_format( $this->getTotalHT() , 2 , ',' , ' ' ) . " €";
    }
}
