<?php

/*
 * @copyright C UAB NFQ Technologies
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 */

declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Task.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskRepository")
 */
class Task implements ResourceInterface
{
    public const STATUS_NEW = 'new';
    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_WAITING_FOR_APPROVAL = 'waiting_for_approval';
    public const STATUS_WAITING_FOR_COMMENT = 'waiting_for_comment';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    public const TRANSITION_CREATE = 'create';
    public const TRANSITION_CLOSE = 'close';
    public const TRANSITION_START = 'start';
    public const TRANSITION_STOP = 'stop';
    public const TRANSITION_FINISH = 'finish';
    public const TRANSITION_APPROVE = 'approve';
    public const TRANSITION_REJECT = 'reject';

    /**
     * @var int|null
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $status = self::STATUS_NEW;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(groups={"in_progress"})
     */
    private $timeSpent;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $commentNeeded;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(groups={"waiting_for_comment"})
     */
    private $comment;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     *
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimeSpent(): ?int
    {
        return $this->timeSpent;
    }

    /**
     * @param int|null $timeSpent
     *
     * @return $this
     */
    public function setTimeSpent(?int $timeSpent): self
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isCommentNeeded(): ?bool
    {
        return $this->commentNeeded;
    }

    /**
     * @param bool|null $commentNeeded
     *
     * @return $this
     */
    public function setCommentNeeded(?bool $commentNeeded): self
    {
        $this->commentNeeded = $commentNeeded;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param null|string $comment
     *
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
