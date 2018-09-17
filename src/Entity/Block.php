<?php

namespace App\Entity;

use App\Utils\BlockDataInterface;
use App\Utils\ProofOfWork;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Filipe Fernandes <filipefernandes007@gmail.com>
 */
class Block
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $id;

    /**
     * @Assert\IsNull()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $idx;

    /**
     * @Assert\IsNull()
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Assert\NotNull()
     * @ORM\Column(type="string", length=2048, nullable=true)
     */
    private $data;

    /**
     * @Assert\IsNull()
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @Assert\IsNull()
     * @ORM\Column(type="string", length=255)
     */
    private $previousBlockHash;

    /**
     * @var Blockchain
     * @ORM\ManyToOne(targetEntity="App\Entity\Blockchain", inversedBy="blocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $blockChain;

    /**
     * @var Block
     * @ORM\OneToOne(targetEntity="App\Entity\Block", mappedBy="previousBlock")
     * @ORM\JoinColumn(name="previous_block_id", referencedColumnName="id")
     */
    private $previousBlock;

    /** @var int */
    private $nonce;

    /**
     * Block constructor.
     * @param BlockDataInterface|null $data
     * @param Block              $previousBlock
     */
    public function __construct(BlockDataInterface $data = null, Block $previousBlock = null)
    {
        $this->createdAt = new \DateTime();
        $this->data      = $data !== null ? $data->getData() : '';

        if ($previousBlock !== null) {
            $this->idx               = $previousBlock->getIdx() + 1;
            $this->previousBlockHash = $previousBlock->getHash();
            $this->previousBlock     = $previousBlock;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdx() : ?int
    {
        return $this->idx;
    }

    public function setIdx(int $idx) : self
    {
        $this->idx = $idx;

        return $this;
    }

    public function getCreatedAt() : \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getData() : ?string
    {
        return $this->data;
    }

    public function setData(?string $data) : self
    {
        $this->data = $data;

        return $this;
    }

    public function getHash() : string
    {
        return $this->hash;
    }

    public function setHash(string $hash) : self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getPreviousBlockHash() : string
    {
        return $this->previousBlockHash;
    }

    public function setPreviousBlockHash(string $previousBlockHash) : self
    {
        $this->previousBlockHash = $previousBlockHash;

        return $this;
    }

    public function getBlockChain() : ?Blockchain
    {
        return $this->blockChain;
    }

    public function setBlockChain(?Blockchain $blockChain) : self
    {
        $this->blockChain = $blockChain;

        return $this;
    }

    public function getPreviousBlock() : ?self
    {
        return $this->previousBlock;
    }

    public function setPreviousBlock(?self $previousBlock) : self
    {
        $this->previousBlock = $previousBlock;

        // set (or unset) the owning side of the relation if necessary
        $newPreviousBlock = $previousBlock === null ? null : $this;

        if ($newPreviousBlock !== $previousBlock->getPreviousBlock()) {
            $previousBlock->setPreviousBlock($newPreviousBlock);
        }

        return $this;
    }

    /**
     * Just a way to verify if the new block is valid.
     *
     * @return bool
     */
    public function isValid() : bool {
        $pow         = new ProofOfWork($this->dataToHash());
        $this->nonce = (int) $pow->getNonce();

        return $pow->isNonceValid();
    }

    /**
     * @return string
     */
    public function mine() : string {
        $this->hash = (new ProofOfWork($this->dataToHash()))->getHash();

        return $this->hash;
    }

    /**
     * @return string
     */
    public function dataToHash() : string {
        return $this->idx .
               $this->data .
               $this->previousBlockHash .
               $this->createdAt->getTimestamp()
               ;
    }

    /**
     * @ORM\PrePersist()
     * @param LifecycleEventArgs $args
     */
    public function setDefaultValues(LifecycleEventArgs $args) : void
    {
        // Is genesis block?
        if ($this->previousBlockHash === null) {
            $this->idx               = 1;
            $this->previousBlockHash = 0;
        }

        $this->mine();
    }

    /**
     * Choose the properties you want to be visible. Maybe you don't want to see previous block, just his hash.
     *
     * @return array
     */
    public function toArray() : array {
        return [
            'id'            => $this->id,
            'idx'           => $this->idx,
            'data'          => $this->data,
            'hash'          => $this->hash,
            'previousHash'  => $this->previousBlockHash,
            'createdAt'     => $this->createdAt->format('Y-m-d H:i:s'),
            'timestamp'     => $this->createdAt->getTimestamp(),
            'valid'         => $this->isValid(),
            'nonce'         => $this->nonce,

            // This is for dev purposes, but, if you want, keep it this way, but will slowdown the result
            //'previousBlock' => $this->previousBlock !== null ? $this->previousBlock->toArray() : null
        ];
    }

    public function __toString() : string
    {
        return json_encode((object) $this->toArray());
    }
}
