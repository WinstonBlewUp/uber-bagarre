<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;

class BecomeFighterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $options['user']; // On récupère l'utilisateur ici

        $builder
            ->add('motivation', TextareaType::class, [
                'label' => 'Pourquoi voulez-vous devenir bagarreur ?',
                'attr' => ['class' => 'w-full border p-2 rounded']
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse (non enregistrée, juste pour l’admin)',
                'required' => true,
                'attr' => ['class' => 'w-full border p-2 rounded']
            ]);

        if ($user && !$user->getHeight()) {
            $builder->add('height', NumberType::class, [
                'label' => 'Taille (en cm)',
                'required' => true,
                'attr' => ['class' => 'w-full border p-2 rounded']
            ]);
        }

        if ($user && !$user->getWeight()) {
            $builder->add('weight', NumberType::class, [
                'label' => 'Poids (en kg)',
                'required' => true,
                'attr' => ['class' => 'w-full border p-2 rounded']
            ]);
        }

        $builder->add('submit', SubmitType::class, [
            'label' => 'Envoyer la demande',
            'attr' => ['class' => 'bg-yellow-500 text-black px-4 py-2 rounded cursor-pointer']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => null, // Définition de l'option user
        ]);

        $resolver->setAllowedTypes('user', ['null', User::class]); // Autorise null et l'entité User
    }
}
