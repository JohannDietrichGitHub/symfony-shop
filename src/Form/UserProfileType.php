<?php

namespace App\Form;

use App\Entity\UserProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

final class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First name',
                'required' => false,
                'constraints' => [new Length(max: 100)],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last name',
                'required' => false,
                'constraints' => [new Length(max: 100)],
            ])
            ->add('phoneCountryCode', ChoiceType::class, [
                'label' => 'Phone country code',
                'required' => false,
                'placeholder' => 'Code',
                'choices' => [
                    '🇫🇷 +33' => '+33',
                    '🇨🇱 +56' => '+56',
                    '🇩🇪 +49' => '+49',
                    '🇪🇸 +34' => '+34',
                    '🇮🇹 +39' => '+39',
                    '🇧🇪 +32' => '+32',
                    '🇨🇭 +41' => '+41',
                    '🇬🇧 +44' => '+44',
                    '🇺🇸 +1' => '+1',
                    '🇲🇽 +52' => '+52',
                    '🇧🇷 +55' => '+55',
                    '🇦🇺 +61' => '+61',
                    '🇳🇿 +64' => '+64',
                    '🇯🇵 +81' => '+81',
                    '🇮🇳 +91' => '+91',
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone number',
                'required' => false,
                'constraints' => [new Length(max: 30)],
            ])
            ->add('marketingEmailOptIn', CheckboxType::class, [
                'label' => 'Send me occasional product and market updates',
                'required' => false,
            ]);
    }
}
