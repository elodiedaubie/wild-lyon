<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    public const DISTRICTS = [
        "Bachut",
        "Bellecombe",
        "Bellecour",
        "Brotteaux",
        "Confluence",
        "Croix-Rousse",
        "Etats-Unis",
        "Fourvière",
        "Gerland",
        "Grand Trou",
        "La Duchère",
        "Les pentes",
        "Mermoz",
        "Montchat",
        "Montplaisir",
        "Part-Dieu",
        "Perrache",
        "Point-du-jour",
        "Saint-Just",
        "Saint-Rambert",
        "Sans souci",
        "Vaise",
        "Vieux Lyon",
        "Villette"
    ];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $picture;

    /**
     * @ORM\ManyToOne(targetEntity=District::class, inversedBy="articles")
     */
    private ?District $district;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     */
    private User $author;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="Favorites")
     */
    private Collection $favoriteOwners;

    public function __construct()
    {
        $this->favoriteOwners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(?District $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFavoriteOwners(): Collection
    {
        return $this->favoriteOwners;
    }

    public function addFavoriteOwner(User $favoriteOwner): self
    {
        if (!$this->favoriteOwners->contains($favoriteOwner)) {
            $this->favoriteOwners[] = $favoriteOwner;
            $favoriteOwner->addFavorite($this);
        }

        return $this;
    }

    public function removeFavoriteOwner(User $favoriteOwner): self
    {
        if ($this->favoriteOwners->removeElement($favoriteOwner)) {
            $favoriteOwner->removeFavorite($this);
        }

        return $this;
    }

    //solve proxy error message at user connexion
    public function __sleep()
    {
        return [];
    }
}
