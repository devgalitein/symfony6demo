<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'cities')]
    #[ORM\JoinColumn(nullable: false,onDelete:"CASCADE")]
    private ?State $state = null;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Neighborhood::class)]
    private Collection $neighborhoods;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Person::class)]
    private Collection $people;

    public function __construct()
    {
        $this->neighborhoods = new ArrayCollection();
        $this->people = new ArrayCollection();
    }

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

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Neighborhood>
     */
    public function getNeighborhoods(): Collection
    {
        return $this->neighborhoods;
    }

    public function addNeighborhood(Neighborhood $neighborhood): self
    {
        if (!$this->neighborhoods->contains($neighborhood)) {
            $this->neighborhoods->add($neighborhood);
            $neighborhood->setCity($this);
        }

        return $this;
    }

    public function removeNeighborhood(Neighborhood $neighborhood): self
    {
        if ($this->neighborhoods->removeElement($neighborhood)) {
            // set the owning side to null (unless already changed)
            if ($neighborhood->getCity() === $this) {
                $neighborhood->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            $person->setCity($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getCity() === $this) {
                $person->setCity(null);
            }
        }

        return $this;
    }
}
