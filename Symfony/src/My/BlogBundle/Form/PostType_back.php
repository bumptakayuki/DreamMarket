<?php
namespace My\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('image_path')
            ->add('limit_member')
            ->add('pref_area')
            ->add('tag', new TagType())
            ->getForm();
    }

    public function getName()
    {
        return 'app_task';
    }


}