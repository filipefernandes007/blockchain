<?php

namespace App\Controller;

use App\Entity\Block;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BlockApiController
 * @package App\Controller
 * @Route("/api/block")
 */
class BlockApiController extends AbstractController
{
    /**
     * @Route("/{id}", methods={"GET"}, name="block_api_get")
     * @param string $id
     * @return JsonResponse
     */
    public function get(string $id) : JsonResponse {
        $block = $this->getDoctrine()
                      ->getRepository(Block::class)
                      ->find((int) $id);

        return new JsonResponse(
            $block !== null ? $block->toArray() : null
        );
    }

    /**
     * @Route("/all/{blockchainId}", methods={"GET"}, name="block_api_all")
     * @param int $blockchainId
     * @return JsonResponse
     */
    public function all(int $blockchainId) : JsonResponse
    {
        /** @var Block[]|[] $blocks */
        $blocks = $this->getDoctrine()
                       ->getRepository(Block::class)
                       ->findBy(['blockChain' => $blockchainId]);

        /** @var [][] $serialize */
        $serialize = [];

        foreach ($blocks as $block) {
            $serialize[] = $block->toArray();
        }

        return new JsonResponse($serialize);
    }
}
