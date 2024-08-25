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
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Camera;

class CameraType extends AbstractType
{
    private $brandToStringTransformer;

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
                'label' => 'Marque (sélectionnez ou saisissez une nouvelle)',
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
                'constraints' => [
                    new File([
                        'maxSize' => '10M', // Limite de taille augmentée à 10 MiB
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF valide',
                    ])
                ],
            ]);

        // Appliquer le DataTransformer au champ 'brand'
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
