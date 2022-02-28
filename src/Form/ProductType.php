<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Tapez le nom du produit']
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Tapez la description du produit'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit ',
                'attr' => [
                    'placeholder' => 'Tapez le prix du produit en €'
                ]
            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => ['placeholder' => 'Tapez une URLd\'image !']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'attr' => [],
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => 'name'
            ]);


        // Exemple d'utilisation des eventlisters sur les forms :
        // C'est à titre d'exemple : L'utilisation dans le cas de transformation de données est dévoyée.

//        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
//            $product = $event->getData();
//        });
//
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $product = $event->getData();
//        });

        // $builder->addEventListener('form.pre_set_data');
        // EN dur ce n'est pas conseillé : Si le nom change d'une version de symfony à l'autre on sera embêté.

        // Exemple d'addeventlistener (EXEMPLE si on ne veut pas ajouter category si on est en edit)

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//
//            $form = $event->getForm();
//            /**
//             * @var Product
//             */
//            $product = $event->getData();
//            // Normalement avec un bon éditeur,
//            // ce qui n'est pas le cas ici, on devrait avoir la connaissance des méthodes de products
//
//            if($product->getId() === null) {
//                $form->add('category', EntityType::class, [
//                    'label' => 'Catégorie',
//                    'attr' => [],
//                    'placeholder' => '-- Choisir une catégorie --',
//                    'class' => Category::class,
//                    'choice_label' => 'name'
//                ]);
//            }
//        });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
