<?php

namespace App\Entity;

use App\Repository\TypeNewsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeNewsRepository::class)
 */
class TypeNews
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $ADClasses;

    /**
     * @ORM\Column(type="text")
     */
    private $BodyClassAfterBodyClass;

    /**
     * @ORM\Column(type="text")
     */
    private $PreviewItemClass;

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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getADClasses(): ?string
    {
        return $this->ADClasses;
    }

    public function setADClasses(?string $ADClasses): self
    {
        $this->ADClasses = $ADClasses;

        return $this;
    }

    public function getBodyClassAfterBodyClass(): ?string
    {
        return $this->BodyClassAfterBodyClass;
    }

    public function setBodyClassAfterBodyClass(string $BodyClassAfterBodyClass): self
    {
        $this->BodyClassAfterBodyClass = $BodyClassAfterBodyClass;

        return $this;
    }

    public function getPreviewItemClass(): ?string
    {
        return $this->PreviewItemClass;
    }

    public function setPreviewItemClass(string $PreviewItemClass): self
    {
        $this->PreviewItemClass = $PreviewItemClass;

        return $this;
    }
}
