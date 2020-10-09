<?php

namespace App\Form\Type\Client;

use Symfony\Component\Form\AbstractType;
use App\Service\Traits\Entity\ShopHourTrait;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class ShopHourType extends AbstractType
{
    use ShopHourTrait;

    /**
     * buildForm
     *
     * @param  mixed $builder
     * @param  mixed $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', CheckboxType::class, [
                'label'    => 'admin.shop.field.open',
                'required' => false,
            ])
            ->add('break', CheckboxType::class, [
                'label'    => 'admin.shop.field.break',
                'attr' => [
                    'class' => 'target-click-shop',
                ],
                'required' => false,
            ])
            ->add('day', ChoiceType::class, [
                'choices' => $this->choiceDay(),
                'label' => false,
                'attr' => [
                    'class' => 'flex',
                    'style' => 'width:100px'
                ],
                'disabled' => true,
            ])
            ->add('startTime', TimeType::class, [
                'input'  => 'string',
                'widget' => 'choice',
                'label' => false,
                'minutes' => range(0, 60, 15),
                'attr' => [
                    'class' => 'flex',
                    'style' => 'width:100px'
                ],
            ])
            ->add('endTime', TimeType::class, [
                'input'  => 'string',
                'widget' => 'choice',
                'label' => false,
                'minutes' => range(0, 60, 15),
                'attr' => [
                    'class' => 'flex',
                    'style' => 'width:100px'
                ],
            ])
            ->add('startBreakTime', TimeType::class, [
                'input'  => 'string',
                'widget' => 'choice',
                'label' => false,
                'minutes' => range(0, 60, 15),
                'attr' => [
                    'class' => 'flex target-start-break-shop',
                    'style' => 'width:100px; display:none'
                ],
            ])
            ->add('endBreakTime', TimeType::class, [
                'input'  => 'string',
                'widget' => 'choice',
                'label' => false,
                'minutes' => range(0, 60, 15),
                'attr' => [
                    'class' => 'flex target-end-break-shop',
                    'style' => 'width:100px; display:none'
                ],
            ]);
    }

    /**
     * configureOptions
     *
     * @param  mixed $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'empty_data' => [],
        ]);
    }
}
