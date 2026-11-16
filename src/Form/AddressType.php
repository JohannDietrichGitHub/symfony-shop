<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, ['label' => 'Address label', 'help' => 'For example: Home or Office'])
            ->add('recipientName', TextType::class, ['label' => 'Recipient name'])
            ->add('company', TextType::class, ['label' => 'Company', 'required' => false])
            ->add('addressLine1', TextType::class, ['label' => 'Address'])
            ->add('addressLine2', TextType::class, ['label' => 'Apartment, suite, etc.', 'required' => false])
            ->add('postalCode', TextType::class, ['label' => 'Postal code'])
            ->add('city', TextType::class, ['label' => 'City'])
            ->add('countryCode', TextType::class, [
                'label' => 'Country',
                'help' => 'Use the two-letter ISO code, for example FR.',
                'attr' => ['maxlength' => 2, 'style' => 'text-transform: uppercase'],
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
                    '🇨🇳 +86' => '+86',
                    '🇮🇳 +91' => '+91',
                ],
            ])
            ->add('phone', TextType::class, ['label' => 'Phone number', 'required' => false])
            ->add('isDefault', CheckboxType::class, [
                'label' => 'Use this as my default delivery address',
                'required' => false,
            ]);
    }
}
