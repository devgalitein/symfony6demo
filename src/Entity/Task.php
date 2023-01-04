<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message:"The task field is required.")]
    #[ORM\Column(length: 255)]
    private ?string $task = null;
    
    #[Assert\NotBlank(message:"The description field is required.")]
    #[ORM\Column(type: 'text')]
    private ?string $description = null;
    
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\ManyToOne(inversedBy: 'Task')]
    private ?TaskCategory $TaskCategory = null;

    public function getCategory(): ?TaskCategory
    {
        return $this->TaskCategory;
    }

    public function setCategory(?TaskCategory $taskCategory): self
    {
        $this->TaskCategory = $taskCategory;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?string
    {
        return $this->task;
    }

    public function setTask(string $task): self
    {
        $this->task = $task;

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

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }
}
