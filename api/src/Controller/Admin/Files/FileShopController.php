<?php

namespace App\Controller\Admin\Files;

use App\Entity\Client\Shop;
use App\Entity\Client\ShopFile;
use Symfony\Component\Mime\MimeTypes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/uploads/clients/shop")]
#[IsGranted('ROLE_ADMIN')]
class FileShopController extends AbstractController
{    
    /**
     * pms
     *
     * @var mixed
     */
    protected $pms;
    
    /**
     * __construct
     *
     * @param  mixed $pms
     * @param  mixed $em
     * @return void
     */
    public function __construct(PermissionsAdmin $pms, EntityManagerInterface $em)
    {
        $this->pms = $pms;
        $this->em = $em;
    }

    /**
     * shop
     *
     * @param  mixed $directory
     * @param  mixed $file
     * @return Response
     */
    #[Route('/{directory}/{file}', name: 'admin_file_shop_upload')]
    public function shop($directory, $file): Response
    {
        $shopFile = $this->em->getRepository(ShopFile::class)->findOneBy(['image_name' => $file]);
        $shop = $this->em->getRepository(Shop::class)->findOneBy(['uuid' => $directory]);

        // $shopFile = new ShopFile();
        // $filename = $request->files->get('file');
        // $shopFile->setImageFile($filename);
        // $shopFile->setShop($shop);

        // $this->em->persist($shopFile);
        // $this->em->flush();

        if (!$shopFile) {
            throw $this->createNotFoundException();
        }

        if (!$shop) {
            throw $this->createNotFoundException();
        }

        if ($this->pms->isAdmin($this->getUser())) {
            return $this->imageResponse($directory, $file);
        }

        if ($this->pms->canUseOwners($this->getUser(), 'SHOP', 'EDIT')) {
            return $this->imageResponse($directory, $file);
        }

        if ($this->pms->canUseActions($this->getUser(), 'SHOP', 'EDIT')) {

            $shops = $this->getUser()->getShops();

            if (0 !== $shops) {
                foreach ($shops as $shop) {
                    if (0 !== $shop->getShopFiles()) {
                        foreach ($shop->getShopFiles() as $f) {
                            if ($f->getImageName() === $file) {
                                return $this->imageResponse($directory, $file);
                            }
                        }
                    }
                }
            }
        }

        throw $this->createAccessDeniedException();
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

        return $response;
    }
}
