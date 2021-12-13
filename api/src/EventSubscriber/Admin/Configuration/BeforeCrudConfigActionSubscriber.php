<?php

namespace App\EventSubscriber\Admin\Configuration;

use App\Entity\Configuration\Config;
use function symfony\component\string\u;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Service\Admin\Builder\ConfigurationBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;

class BeforeCrudConfigActionSubscriber implements EventSubscriberInterface
{
    public const CONFIG_GENERAL = 'ConfigGeneralCrudController';
    /**
     * conf
     *
     * @var mixed
     */
    protected $conf;

    /**
     * __construct
     *
     * @param  mixed $conf
     * @return void
     */
    public function __construct(ConfigurationBuilder $conf)
    {
        $this->conf = $conf;
    }
    
    /**
     * onBeforeGetOrEditOrDeleteEntity
     *
     * @param  mixed $event
     * @return void
     */
    public function onBeforeGetOrEditOrDeleteEntity(BeforeCrudActionEvent $event): void
    {
        $context = $event->getAdminContext();

        if (!($context->getEntity()->getInstance() instanceof Config)) {
            return;
        }

        //DETAIL OR EDIT PAGE
        if (
            $context->getCrud()->getCurrentPage() === Crud::PAGE_DETAIL
            || $context->getCrud()->getCurrentPage() === Crud::PAGE_EDIT
        ) {
            // General configurations
            if (u($context->getCrud()->getControllerFqcn())->containsAny(self::CONFIG_GENERAL)) {
                if (\in_array($context->getEntity()->getInstance()->getName(), $this->conf->getGeneralConfigValues())) {
                    return;
                }
            }
        }

        throw new ForbiddenActionException($context);
    }
    
    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCrudActionEvent::class => 'onBeforeGetOrEditOrDeleteEntity',
        ];
    }
}
