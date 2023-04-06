<?php

namespace App\Entity;

use App\Repository\WorkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: WorkRepository::class)]
class Work
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;
    
    #[ORM\ManyToMany(targetEntity: WorkTag::class,cascade:['persist'])]
    protected $work_tags;

    public function __construct()
    {
        $this->work_tags = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    
    public function getWorkTags(): Collection
    {
        return $this->work_tags;
    }
    
    public function addWorkTag(WorkTag $work_tag): void
    {
        // for a many-to-many association:
        $work_tag->addWork($this);

        // for a many-to-one association:
        // $work_tag->setWork($this);

        $this->work_tags->add($work_tag);
    }

    public function removeWorkTag(WorkTag $work_tag): void
    {
        $this->work_tags->removeElement($work_tag);
    }

}
