<?php

namespace App\Controller\Api\Files;

use App\Entity\Client\Shop;
use App\Entity\Client\ShopFile;
use Symfony\Component\Mime\MimeTypes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Api\Builder\ApiResponseBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/api/uploads/clients/shop")]
class FileShopApiController extends AbstractController
{        
    /**
     * __construct
     *
     * @param  mixed $em
     * @param  mixed $apiResponseBuilder
     * @return void
     */
    public function __construct(EntityManagerInterface $em, ApiResponseBuilder $apiResponseBuilder)
    {
        $this->em = $em;
        $this->apiResponseBuilder = $apiResponseBuilder;
    }

    /**
     * shop
     *
     * @param  mixed $directory
     * @param  mixed $file
     * @return Response
     */
    #[Route('/{directory}/{file}', name: 'api_file_shop_upload')]
    public function shopApi($directory, $file): Response
    {
        $shopFile = $this->em->getRepository(ShopFile::class)->findOneBy(['image_name' => $file]);
        $shop = $this->em->getRepository(Shop::class)->findOneBy(['uuid' => $directory]);

        if (!$shopFile) {
            return $this->apiResponseBuilder->CheckIfFileExist();
        }

        if (!$shop) {
            return $this->apiResponseBuilder->CheckIfFileExist();
        }

        // @todo: auth system for files
        // @todo openapi url to get one image and multiples images

        return $this->imageResponse($directory, $file);
    }

    /**
     * imageResponse
     *
     * @param  mixed $directory
     * @param  mixed $file
     * @return Response
     */
    public function imageResponse($directory, $file): Response
    {
        $filesystem = new Filesystem();
        $mimeTypes = new MimeTypes();

        $link = dirname(__DIR__, 4) . "/uploads/clients/shop/" . $directory . "/" . $file;
        $exist = $filesystem->exists($link);
        $mimeType = $mimeTypes->guessMimeType($link);

        $response = $exist ? new BinaryFileResponse($link) : new BinaryFileResponse(dirname(__DIR__, 4) . "/uploads/500.png");
        $response->headers->set('Content-Type', $mimeType);
        $response->setMaxAge(120);

        return $response;
    }
}
