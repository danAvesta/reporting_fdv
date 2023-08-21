<?php
namespace App\Form;

use App\Entity\Inventairerdv;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventairerdvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', CollectionType::class, [
                'entry_type' => InventoryItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Items',
            ])
            // Si vous avez d'autres champs dans votre entité Inventairerdv, vous pouvez les ajouter ici.
            // Par exemple :
            // ->add('Reference')
            // ->add('Quantite')
            // ->add('datetime')
            // ->add('IdRdv')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inventairerdv::class,
        ]);
    }
}
