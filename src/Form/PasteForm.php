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
                'label' => 'Заголовок',
            ])
            ->add('content', TextareaType::Class,[
                'label' => 'Контент',
                'attr' => [
                    'id' => 'contentInput',
                ]
            ])
            ->add('expirationTime', ChoiceType::class, [
                'label' => 'Время жизни',
                'choices' => [
                    '10 Минут' => new \DateTime("+10 minutes"),
                    '1 Час' => new \DateTime("+1 hour"),
                    '3 Часа' => new \DateTime("+3 hours"),
                    '1 День' => new \DateTime("+1 day"),
                    '1 Неделя' => new \DateTime("+1 week"),
                    '1 Месяц' => new \DateTime("+1 month"),
                    'Без срока' => null,
                ],
                'placeholder' => 'Выберите вариант',
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'Язык программирования',
                'choices' => [
                    'JavaScript' => 'javascript',
                    // 'Python' => 'python',
                    // 'PHP' => 'php',
                    // 'Java' => 'java',
                    // 'C#' => 'csharp',
                    // 'Ruby' => 'ruby',
                    'HTML' => 'html',
                    'CSS' => 'css',
                    'Нет' => 'none'
                ],
                'placeholder' => 'Выберите вариант',
                'data' => 'none',
            ])
            ->add('accessLevel', ChoiceType::class, [
                'label' => 'Уровень доступа',
                'choices' => [
                    'Публичный' => 'public',
                    'По ссылке' => 'unlisted',
                    'Приватный' => 'private',
                ],
                'placeholder' => 'Выберите вариант',
            ])
            ->add('submit', SubmitType::Class,[
                'label' => 'Созадать пасту',
            ]);
        }
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Paste::class,
            ]);
        }
}