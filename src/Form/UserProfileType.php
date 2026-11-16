<?php

namespace App\Form;

use App\Entity\UserProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
