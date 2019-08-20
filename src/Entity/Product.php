<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @UniqueEntity("name", message="This name is already used.")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_products",
 *          parameters = {"id" = "expr(object.getId())"},
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(groups = {"list", "details"})
 * )
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list", "details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"list", "details"})
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups({"details"})
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"details"})
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"details"})
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"details"})
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
