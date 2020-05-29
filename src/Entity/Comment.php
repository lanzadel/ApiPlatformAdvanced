<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     attributes={"order"={"published":"DESC"}},
 *     itemOperations={
 *          "get",
 *          "put"={
 *              "access_control"="(is_granted('ROLE_EDITOR') or is_granted('ROLE_COMMENTATOR')) and object.getAuthor() == user "
 *          }
 *      },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *                  "access_control"="is_granted('ROLE_COMMENTATOR')"
 *          },
 *          "api_blog_posts_comments_get_subresource"={
 *                  "normalization_context"={
 *                      "groups"={"get-comment"}
 *                  }
 *          }
 *    },
 *     denormalizationContext= {
 *          "groups"={"post"}
 *     }
 * )
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment implements AuthoredEntityInterface, PublishDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-comment"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post","get","get-comment"})
     * @Assert\NotBlank()
     * @Assert\Length(min = 5, max =3000)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get","get-comment"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @JoinColumn(nullable=false)
     * @Groups({"get-comment"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $blogPost;

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    /**
     * @param mixed $blogPost
     */
    public function setBlogPost($blogPost)
    {
        $this->blogPost = $blogPost;
    }

    /**
     * @param User $author
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }
    public function __toString(): string
    {
        return substr($this->content, 0, 20) . '...';
    }
}
