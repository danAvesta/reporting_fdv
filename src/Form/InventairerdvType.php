<?php

namespace App\Form;

use App\Entity\Inventairerdv;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;



class InventairerdvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Reference', TextType::class, [
                'label' => 'Référence',
                'required' => true,
            ])
            ->add('Quantite', IntegerType::class, [
                'label' => 'Quantité',
                'required' => true,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inventairerdv::class,
        ]);
    }
}
