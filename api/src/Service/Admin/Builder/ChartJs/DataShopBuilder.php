<?php

namespace App\Service\Admin\Builder\ChartJs;

use App\Entity\Client\Shop;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataShopBuilder
{
    protected $em;

    protected $security;

    protected $translator;

    protected $pms;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        TranslatorInterface $translator,
        PermissionsAdmin $pms
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function getMonthsLabels()
    {
        return [
            $this->translator->trans('admin.field.january', [], 'admin'),
            $this->translator->trans('admin.field.february', [], 'admin'),
            $this->translator->trans('admin.field.march', [], 'admin'),
            $this->translator->trans('admin.field.april', [], 'admin'),
            $this->translator->trans('admin.field.may', [], 'admin'),
            $this->translator->trans('admin.field.june', [], 'admin'),
            $this->translator->trans('admin.field.july', [], 'admin'),
            $this->translator->trans('admin.field.august', [], 'admin'),
            $this->translator->trans('admin.field.september', [], 'admin'),
            $this->translator->trans('admin.field.october', [], 'admin'),
            $this->translator->trans('admin.field.november', [], 'admin'),
            $this->translator->trans('admin.field.december', [], 'admin'),
        ];
    }

    public function getNumberOfShop()
    {
        $repository = $this->em->getRepository(Shop::class);

        if ($this->pms->isAdmin($this->security->getUser())) {
            return $repository->createQueryBuilder('s')
                ->select('count(s.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }
    }
}
