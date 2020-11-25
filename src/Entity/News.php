<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 */
class News
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rbclink;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $datamodif;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $PreviewText;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getRbclink(): ?string
    {
        return $this->rbclink;
    }

    public function setRbclink(?string $rbclink): self
    {
        $this->rbclink = $rbclink;

        return $this;
    }

    public function getDatamodif(): ?int
    {
        return $this->datamodif;
    }

    public function setDatamodif(?int $datamodif): self
    {
        $this->datamodif = $datamodif;

        return $this;
    }

    public function getPreviewText(): ?string
    {
        return $this->PreviewText;
    }

    public function setPreviewText(?string $PreviewText): self
    {
        $this->PreviewText = $PreviewText;

        return $this;
    }
}
