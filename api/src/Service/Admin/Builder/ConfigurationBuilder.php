<?php

namespace App\Service\Admin\Builder;

use App\Entity\Configuration\Config;
use Doctrine\ORM\EntityManagerInterface;

/**
 * ConfigurationBuilder
 */
class ConfigurationBuilder
{
    public $textType = 'text';
    public $choiceType = 'choice';
    public $booleanType = 'bool';
    public $textEditorType = 'editor';
    public $textareaType = 'textarea';
    public $integerType = 'int';

    /**
     * em
     *
     * @var mixed
     */
    protected $em;

    /**
     * __construct
     *
     * @param  mixed $config
     * @return void
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * get
     *
     * @param  mixed $name
     * @return string
     */
    public function get($name): ?string
    {
        if (!$config = $this->em->getRepository(Config::class)->findOneBy(['name' => $name, 'is_active' => 1])) {
            return null;
        }

        return $config->getValue();
    }

    /**
     * update
     *
     * @param  mixed $name
     * @param  mixed $value
     * @return bool
     */
    public function update($name, $value = null): ?bool
    {
        if (!$config = $this->em->getRepository(Config::class)->findOneBy(['name' => $name])) {
            return false;
        }

        $config->setValue($value);
        $config->setUpdatedAt(new \DateTime());

        $this->em->persist($config);
        $this->em->flush();

        return true;
    }

    /**
     * getType
     *
     * @param  mixed $typing
     * @return string|null
     */
    public function getType($typing = null): ?string
    {
        switch ($typing) {
            case 0:
                // Use yield TextField::new('value');
                return $this->textType;
                break;
            case 1:
                // Use yield ChoiceField::new('value')->setChoices(['false' => 0, 'true' => 1]);
                return $this->booleanType;
                break;
            case 2:
                // Use yield ChoiceField::new('value')->setChoices(['false' => 0, 'true' => 1]);
                return $this->choiceType;
                break;
            case 3:
                // Use yield TextEditorField::new('value');
                return $this->textEditorType;
                break;
            case 4:
                // Use yield TextAreaField::new('value');
                return $this->textareaType;
                break;
            case 5:
                // Use yield IntegerField::new('value');
                return $this->integerType;
                break;
            default:
                // Use yield TextField::new('value');
                return $this->textType;
                break;
        }
    }

    /**
     * getGeneralConfigValues
     *
     * @return array
     */
    public function getGeneralConfigValues(): array
    {
        return [
            'CONF_DASHBOARD_TITLE',
            'CONF_DEFAULT_PAGINATOR'
        ];
    }
}
