<?php

namespace Anton\ShopBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 * @UniqueEntity("sku")
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Length(min=1)
     * @Assert\Length(max=30)
     * @Assert\Regex("/^[a-zA-Z\s]+$/")
     */
    private $name;

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Length(min=1)
     * @Assert\Length(max=100)
     */
    private $description;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column()
     */
    private $sku;

    /**
     * @ORM\Column(name="is_active")
     */
    private $isActive;

    /**
     * @ORM\Column()
     * @Assert\NotBlank(message="Please, upload the product picture as a JPEG file.")
     * @Assert\File(mimeTypes={ "image/jpeg" }, mimeTypesMessage = "Please upload a valid image!")
     * @Assert\Image(
     *     maxWidth = 600,
     *     maxHeight = 600
     * )
     */
    private $picture;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="relatedProducts")
     */
    private $relatedProductsWithThis;

    /**
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="relatedProductsWithThis")
     * @ORM\JoinTable(name="related_products",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="related_product_id", referencedColumnName="id")}
     *      )
     */
    private $relatedProducts;

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
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

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
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

    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function __construct()
    {
        $this->relatedProductsWithThis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relatedProducts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addRelatedProductsWithThi(Product $relatedProductsWithThi)
    {
        $this->relatedProductsWithThis[] = $relatedProductsWithThi;

        return $this;
    }

    public function removeRelatedProductsWithThi(Product $relatedProductsWithThi)
    {
        $this->relatedProductsWithThis->removeElement($relatedProductsWithThi);
    }

    public function getRelatedProductsWithThis()
    {
        return $this->relatedProductsWithThis;
    }

    public function addRelatedProduct(Product $relatedProduct)
    {
        $this->relatedProducts[] = $relatedProduct;

        return $this;
    }

    public function removeRelatedProduct(Product $relatedProduct)
    {
        $this->relatedProducts->removeElement($relatedProduct);
    }

    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }
}
