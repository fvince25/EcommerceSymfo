<?php

namespace App\Form\Type;


use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{

    // Sert à appliquer des transformations et passer des options.
    // Ces options sont présentes dans le troisième paramètre du add

    //->add('price', PriceType::class, [
    //'label' => 'Prix du produit ',
    //'attr' => [
    //'placeholder' => 'Tapez le prix du produit en €'
    //],
    //'divide' => true
    //])


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if( $options['divide'] === false) {
            return;
        }

        $builder->addModelTransformer(new CentimesTransformer);
    }

    public function getParent()
    {
        // J'hérite de toutes les options possibles d'un numbertype !!!
        return NumberType::class;
    }

    // Options par défaut.
    public function configureOptions(OptionsResolver $resolver) {

        $resolver->setDefaults([
            'divide' => true
        ]);
    }

}