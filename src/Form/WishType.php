<?php

namespace App\Form;

use App\Entity\Wish;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class WishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,['label'=>'Your idea'])
            ->add('description', TextareaType::class,['label'=>'Please describe it !', 'required'=>false ])
            ->add('author',TextType::class,['label'=>'Your username'])
            ->add('isPublished',CheckboxType::class,['label'=>'Published', 'required' => false ])
            ->add('image',FileType::class,
                [
                    'mapped'=>false,
                    'required'=>false,
                    'constraints' => [
                        new Image([
                            'maxSize'=>'1024k',
                            'mimeTypes'=>[
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage'=>'Please upload a valid images',
                        ])
                    ],
                    'label'=>'Upload images'
                ]
            );
        //Ajout du listener pour ajouter dynamiquement une case à cocher si le souhait possède déjà une images.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $wish = $event->getData();
            if($wish && $wish->getFilename()) {
                //Cas où on est en modification et qu'une image est déjà présente,
                //On ajoute un checkbox pour permettre de demander la suppression de l'image.
                $form = $event->getForm();
                $form->add('deleteImage',CheckboxType::class,[
                    'mapped'=>false,
                    'required'=>false,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
        ]);
    }
}
