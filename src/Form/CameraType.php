<?php

namespace App\Form;

use App\Form\DataTransformer\BrandToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Camera;

class CameraType extends AbstractType
{
    private BrandToStringTransformer $brandToStringTransformer;

    public function __construct(BrandToStringTransformer $brandToStringTransformer)
    {
        $this->brandToStringTransformer = $brandToStringTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modelName', TextType::class, [
                'label' => 'Nom du Modèle',
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Année',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('filmFormat', ChoiceType::class, [
                'label' => 'Format du Film',
                'choices' => [
                    '35mm' => '35mm',
                    '120' => '120',
                ],
                'placeholder' => 'Sélectionnez un format',
            ])
            ->add('brand', TextType::class, [
                'label' => 'Marque',
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de l\'appareil',
                'mapped' => false,
                'required' => false,
            ])
            ->add('manual', FileType::class, [
                'label' => 'Manuel de l\'appareil',
                'mapped' => false,
                'required' => false,
            ]);

        $builder->get('brand')
            ->addModelTransformer($this->brandToStringTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Camera::class,
        ]);
    }
}
