<?php

namespace My\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('image_path')
            ->add('pref_area')
            ->add('limit_member')
            ->add('tags', new \My\BlogBundle\Form\TagType())
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
//        $resolver->setDefaults(array(
//            'data_class' => 'My\BlogBundle\Entity\Post'
//        ));

        $resolver->setDefaults(array(
            'data_class'      => 'My\BlogBundle\Entity\Post',
            'csrf_protection' => false,
            'csrf_field_name' => '_token'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'my_blogbundle_post';
    }
}
