<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockchainRepository")
 */
class Blockchain
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=36)
     */
    private $uuid;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var Collection|Block[]
     * @ORM\OneToMany(targetEntity="App\Entity\Block",
     *                mappedBy="blockChain",
     *                cascade={"persist"},
     *                orphanRemoval=true)
     */
    protected $blocks;

    /**
     * Blockchain constructor.
     * @param Block $block
     * @throws \Exception
     */
    public function __construct(Block $block = null)
    {
        $this->blocks = new ArrayCollection();

        if ($block !== null) {
            $block->setBlockChain($this);
            $this->blocks[] = $block;
        } else {
            $this->blocks[] = (new Block(null, null))->setBlockChain($this);
        }

        $this->active    = true;
        $this->uuid      = Uuid::uuid4();
        $this->createdAt = new \DateTime();
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Blockchain
     */
    public function setActive(bool $active) : Blockchain
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid() : string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return Blockchain
     */
    public function setUuid(string $uuid) : Blockchain
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Blockchain
     */
    public function setCreatedAt(\DateTime $createdAt) : Blockchain
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Block[]
     */
    public function getBlocks() : Collection
    {
        return $this->blocks;
    }

    public function getBlock(int $i) : ?Block {
        return $this->blocks->get($i);
    }

    public function addBlock(Block $block): self
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks[] = $block;
            $block->setBlockChain($this);
        }

        return $this;
    }

    public function removeBlock(Block $block) : self
    {
        if ($this->blocks->contains($block)) {
            $this->blocks->removeElement($block);
            // set the owning side to null (unless already changed)
            if ($block->getBlockChain() === $this) {
                $block->setBlockChain(null);
            }
        }

        return $this;
    }

    /**
     * @return Block|null
     */
    public function lastBlock() : ?Block {
        $lastBlock = $this->blocks->last();

        if ($lastBlock instanceof Block) {
            return $lastBlock;
        }

        return null;
    }

    /**
     * @return Block
     */
    public function genesisBlock() : Block {
        return $this->blocks->first();
    }

    /**
     * @return bool
     */
    public function isValid() : bool {
        /**
         * @var int $key
         * @var Block $block
         */
        foreach ($this->blocks as $key => $block) {
            if (!$block->isValid()) {
                return false;
            }

            if ($key > 0) {
                if ($block->getPreviousBlockHash() === null) {
                    return false;
                }

                if ($block->getPreviousBlockHash() !== $this->blocks[$key - 1]->getHash()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray() : array {
        return [
            'id'        => $this->id,
            'uuid'      => $this->uuid,
            'valid'     => $this->isValid(),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'timestamp' => $this->createdAt->getTimestamp()
        ];
    }
}
