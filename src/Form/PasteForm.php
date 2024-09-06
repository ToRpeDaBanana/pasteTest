<?php

namespace App\Form;

use App\Entity\Paste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasteForm extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
        )
        {
            $builder
            ->add('title', TextType::Class,[
                'label' => 'Title',
            ])
            ->add('content', TextType::Class,[
                'label' => 'Content',
            ])
            ->add('expirationTime', ChoiceType::class, [
                'label' => 'Expiration Time',
                'choices' => [
                    '10 Minutes' => new \DateTime("+10 minutes"),
                    '1 Hour' => new \DateTime("+1 hour"),
                    '3 Hours' => new \DateTime("+3 hours"),
                    '1 Day' => new \DateTime("+1 day"),
                    '1 Week' => new \DateTime("+1 week"),
                    '1 Month' => new \DateTime("+1 month"),
                    'No Expiration' => null,
                ],
                'placeholder' => 'Choose an option',
            ])
            ->add('accessLevel', ChoiceType::class, [
                'label' => 'Access Level',
                'choices' => [
                    'Public' => 'public',
                    'Unlisted' => 'unlisted',
                    'Private' => 'private',
                ],
                'placeholder' => 'Choose an option',
            ])
            ->add('submit', SubmitType::Class,[
                'label' => 'Create Paste',
            ]);
        }
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Paste::class,
            ]);
        }
}