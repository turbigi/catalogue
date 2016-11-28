<?php

namespace Anton\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Length(min=1)
     * @Assert\Length(max=30)
     * @Assert\Regex("/^[a-zA-Z\s]+$/")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent",cascade={"remove"})
     */
    private $children;

    /**
     * @ORM\Column(name="is_active")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category",cascade={"remove"})
     */
    private $products;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setParent(\Anton\ShopBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addChild(\Anton\ShopBundle\Entity\Category $child)
    {
        $this->children[] = $child;

        return $this;
    }

    public function removeChild(\Anton\ShopBundle\Entity\Category $child)
    {
        $this->children->removeElement($child);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addProduct(\Anton\ShopBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    public function removeProduct(\Anton\ShopBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    public function getProducts()
    {
        return $this->products;
    }
}
