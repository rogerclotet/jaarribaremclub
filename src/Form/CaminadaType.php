<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaminadaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', IntegerType::class)
            ->add('year', IntegerType::class)
            ->add('path', TextType::class)
            ->add('description', TextareaType::class)
            ->add('notes', TextareaType::class, [
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'required'     => false,
                'data_class'   => File::class,
            ])
            ->add('map', FileType::class, [
                'required'     => false,
                'data_class'   => File::class,
            ])
            ->add('elevation', FileType::class, [
                'required'     => false,
                'data_class'   => File::class,
            ])
            ->add('leaflet', FileType::class, [
                'required'     => false,
                'data_class'   => File::class,
            ])
            ->add('gpsTrack', FileType::class, [
                'required'     => false,
                'data_class'   => File::class,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-primary',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Caminada',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'historybundle_caminada';
    }
}
