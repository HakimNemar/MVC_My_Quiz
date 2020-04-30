<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateQuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Categorie', ChoiceType::class, [
                'choices'  => [
                    'Harry Potter' => 'Harry Potter',
                    'Sigles Français' => 'Sigles Français',
                    'Définitions de mots' => 'Définitions de mots',
                    'Les spécialités culinaires' => 'Les spécialités culinaires',
                    'Séries TV : les simpson - partie 1' => 'Séries TV : les simpson - partie 1',
                    'Séries TV : les simpson - partie 2' => 'Séries TV : les simpson - partie 2',
                    'Séries TV : Stargate SG1' => 'Séries TV : Stargate SG1',
                    'Séries TV : Stargate NCIS' => 'Séries TV : Stargate NCIS',
                    'Jeux de société' => 'Jeux de société',
                    'Programmation' => 'Programmation',
                    'Sigles informatiques' => 'Sigles informatiques',
                ]])
            ->add('Question')
            ->add('ReponseCorrect')
            ->add('ReponseFausse_1')
            ->add('ReponseFausse_2');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
