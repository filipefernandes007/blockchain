<?php

namespace App\Controller;

use App\Entity\Block;
use App\Entity\Blockchain;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BlockchainApiController
 * @package App\Controller
 * @Route("/api/blockchain")
 */
class BlockchainApiController extends AbstractController
{
    private const LIMIT_BLOCKCHAIN_PAGINATION = 20;

    /**
     * @Route("", methods={"POST"}, name="blockchain_api_add")
     * @return JsonResponse
     * @throws \Exception
     */
    public function create() : JsonResponse
    {
        /** @var ObjectManager $om */
        $om = $this->getDoctrine()->getManager();

        /** @var Blockchain $blockchain */
        $blockchain = new Blockchain();

        $om->persist($blockchain);
        $om->persist($blockchain->lastBlock()); // the genesis block
        $om->flush();

        return new JsonResponse((object) $blockchain->toArray());
    }

    /**
     * @Route("/{id}",
     *        requirements={"id": "[1-9]\d*"},
     *        methods={"GET"},
     *        name="blockchain_api_get")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id) : JsonResponse
    {
        /** @var Blockchain $om */
        $blockchain = $this->getDoctrine()
                           ->getManager()
                           ->getRepository(Blockchain::class)
                           ->find($id);

        if (!$blockchain->isValid()) {
            return new JsonResponse((object) ['error' => 'Blockchain is not valid!']);
        }

        return new JsonResponse((object) $blockchain->toArray());
    }

    /**
     * @Route("", methods={"GET"}, name="blockchain_api_all")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, methods={"GET"}, name="blockchain_api_all_pagination")
     * @param int $page
     * @return JsonResponse
     */
    public function all(int $page = 0) : JsonResponse
    {
        $offset = $page > 0 ? $page - 1 : 0;

        /** @var Blockchain[] */
        $blockchains = $this->getDoctrine()
                            ->getManager()
                            ->getRepository(Blockchain::class)
                            ->findBy(['active' => true], null, self::LIMIT_BLOCKCHAIN_PAGINATION, $offset);

        /** @var [][] $serialize */
        $serialize = [];

        /** @var Blockchain $blockchain */
        foreach ($blockchains as $blockchain) {
            $serialize[] = $blockchain->toArray();
        }

        return new JsonResponse($serialize);
    }

    /**
     * @Route("/block", methods={"POST"}, name="blockchain_api_add_block")
     * @param Request $request
     * @return JsonResponse
     */
    public function addBlock(Request $request) : JsonResponse
    {
        /** @var object|null $data */
        $data = json_decode($request->getContent());

        if ($data === null) {
            return new JsonResponse((object) ['error' => 'Missing json!'], 401);
        }

        if (!property_exists($data, 'blockchainId') || !property_exists($data, 'data')) {
            return new JsonResponse((object) ['error' => 'Missing data or blockchainId!'], 401);
        }

        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine();

        /** @var Blockchain $blockchain */
        $blockchain = $em->getRepository(Blockchain::class)
                         ->find((int) $data->blockchainId);

        if ($blockchain === null) {
            return new JsonResponse((object) ['error' => 'Blockchain not found!'], 400);
        }

        /** @var Block $block */
        $block = new Block($data->data, $blockchain->lastBlock());
        $blockchain->addBlock($block);

        /** @var ObjectManager $objectManager */
        $objectManager = $em->getManager();

        $objectManager->persist($block);
        $objectManager->flush();

        return new JsonResponse((object) $block->toArray());
    }

    /**
     * The blocks belonging to the blockchain
     *
     * @Route("/{id}/blocks", requirements={"id": "[1-9]\d*"}, methods={"GET"}, name="blockchain_api_blocks")
     * @param int $id
     * @return JsonResponse
     */
    public function blocks(int $id) : JsonResponse
    {
        /** @var Blockchain $blockchain */
        $blockchain = $this->getDoctrine()
                           ->getRepository(Blockchain::class)
                           ->find($id);

        if (!$blockchain->isValid()) {
            return new JsonResponse((object) ['error' => 'Blockchain is not valid!']);
        }

        /** @var Block[]|[] $blocks */
        $blocks = $blockchain->getBlocks()->toArray();

        if (\count($blocks) === 0) {
            return new JsonResponse([]);
        }

        // Order descending.
        // Don't do it with @ORM\OrderBy annotation,
        // because Blockchain validates itself in ascending order!
        usort($blocks, function(Block $a, Block $b) {
            return ($a->getIdx() < $b->getIdx()) ? 1 : -1;
        });

        /** @var [][] $serialize */
        $serialize = [];

        /** @var Block $block */
        foreach ($blocks as $block) {
            $serialize[] = $block->toArray();
        }

        return new JsonResponse($serialize);
    }
}
