<?php
/**
 * Rating entity.
 */

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rating class.
 */
#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Rating value.
     */
    #[ORM\Column]
    #[Assert\Type('float')]
    #[Assert\NotBlank]
    #[Assert\Range(
        notInRangeMessage: 'message.value not in range',
        min: 0,
        max: 5,
    )]
    private ?float $value = null;

    /**
     * User who gave the rating.
     */
    #[Assert\Valid]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Photo being rated.
     */
    #[Assert\Valid]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Photo $photo = null;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for value.
     *
     * @return int|null Rating value
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * Setter for value.
     *
     * @param int $value Rating value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * Getter for User.
     *
     * @return User|null User who gave the rating
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Setter for User.
     *
     * @param User|null $user User who gave the rating
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * Getter for Photo.
     *
     * @return Photo|null Photo being rated
     */
    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    /**
     * Setter for Photo.
     *
     * @param Photo|null $photo Photo being rated
     */
    public function setPhoto(?Photo $photo): void
    {
        $this->photo = $photo;
    }
}
