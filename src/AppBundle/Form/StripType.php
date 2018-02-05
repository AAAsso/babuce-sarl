<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class StripType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('creationDate')
                ->add('publicationDate')
                ->add('title')
                ->add('stripElements', FileType::class, array(
                    'label' => 'File to upload',
                    'multiple' => 'multiple'))
                ->add('contentWarnings', EntityType::class, [
                    'class' => 'AppBundle:ContentWarning',
                    'choice_label' => 'label',
                    'expanded' => 'True',
                    'multiple' => 'True',
                ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Strip'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_strip';
    }


}
