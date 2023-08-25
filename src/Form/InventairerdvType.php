<?php

namespace App\Form;

use App\Entity\Inventairerdv;
use App\Entity\RendezVous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


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
            // ->add('datetime', DateTimeType::class, [
            //     'label' => 'Date et heure',
            //     'required' => true,
            // ])
            // ->add('IdRdv', EntityType::class, [
            //     'class' => RendezVous::class,
            //     // 'choice_label' => 'name',
            //     'label' => 'Rendez-vous',
            //     'required' => true,
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inventairerdv::class,
        ]);
    }
}
