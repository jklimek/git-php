<?php

namespace GitPhpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MergeRequest
 *
 * @ORM\Table(name="merge_request")
 * @ORM\Entity(repositoryClass="GitPhpBundle\Repository\MergeRequestRepository")
 */
class MergeRequest
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="source_branch", type="string", length=255)
     */
    private $sourceBranch;

    /**
     * @var string
     *
     * @ORM\Column(name="destination_branch", type="string", length=255)
     */
    private $destinationBranch;

    /**
     * @var string
     *
     * @ORM\Column(name="commit_hash", type="string", length=255)
     */
    private $commitHash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sourceBranch
     *
     * @param string $sourceBranch
     *
     * @return MergeRequest
     */
    public function setSourceBranch($sourceBranch)
    {
        $this->sourceBranch = $sourceBranch;

        return $this;
    }

    /**
     * Get sourceBranch
     *
     * @return string
     */
    public function getSourceBranch()
    {
        return $this->sourceBranch;
    }

    /**
     * Set commitHash
     *
     * @param string $commitHash
     *
     * @return MergeRequest
     */
    public function setCommitHash($commitHash)
    {
        $this->commitHash = $commitHash;

        return $this;
    }

    /**
     * Get commitHash
     *
     * @return string
     */
    public function getCommitHash()
    {
        return $this->commitHash;
    }

    /**
     * Set timestamp
     *
     * @return MergeRequest
     */
    public function setTimestamp()
    {
        $this->timestamp = new \DateTime("now");

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get destination branch
     *
     * @return string
     */
    public function getDestinationBranch()
    {
        return $this->destinationBranch;
    }

    /**
     * Set destinationBranch
     *
     * @param string $destinationBranch
     *
     * @return MergeRequest
     *
     */
    public function setDestinationBranch($destinationBranch)
    {
        $this->destinationBranch = $destinationBranch;
    }

}

