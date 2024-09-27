<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'article_id' => SearchFilter::STRATEGY_EXACT
        ]
    )
]
#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $insertDate = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?User $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Articles $article_id = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'comments')]
    private Collection $comment_id;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'comment_id')]
    private Collection $comments;

      

    public function __construct()
    {
        $this->comment_id = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getInsertDate(): ?\DateTimeInterface
    {
        return $this->insertDate;
    }

    public function setInsertDate(\DateTimeInterface $insertDate): static
    {
        $this->insertDate = $insertDate;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getArticleId(): ?Articles
    {
        return $this->article_id;
    }

    public function setArticleId(?Articles $article_id): static
    {
        $this->article_id = $article_id;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCommentId(): Collection
    {
        return $this->comment_id;
    }

    public function addCommentId(self $commentId): static
    {
        if (!$this->comment_id->contains($commentId)) {
            $this->comment_id->add($commentId);
        }

        return $this;
    }

    public function removeCommentId(self $commentId): static
    {
        $this->comment_id->removeElement($commentId);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->addCommentId($this);
        }

        return $this;
    }

    public function removeComment(self $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            $comment->removeCommentId($this);
        }

        return $this;
    }


}
