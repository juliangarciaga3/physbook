<?php

// src/PJM/AppBundle/Entity/Commentaires/Comment.php

namespace PJM\AppBundle\Entity\Commentaires;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment implements VotableCommentInterface, SignedCommentInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment.
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="PJM\AppBundle\Entity\Commentaires\Thread")
     */
    protected $thread;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $score = 0;

    /**
     * Author of the comment.
     *
     * @ORM\ManyToOne(targetEntity="PJM\AppBundle\Entity\User")
     *
     * @var User
     */
    protected $author;

    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getAuthorName()
    {
        if (null === $this->getAuthor()) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getUsername();
    }

    /**
     * Sets the score of the comment.
     *
     * @param int $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * Returns the current score of the comment.
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Increments the comment score by the provided
     * value.
     *
     * @param int value
     *
     * @return int The new comment score
     */
    public function incrementScore($by = 1)
    {
        $this->score += $by;
    }
}
