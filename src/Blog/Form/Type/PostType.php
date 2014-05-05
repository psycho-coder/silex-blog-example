<?php

namespace Blog\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'app.form.type.posttype.title.label',
                'constraints' => array(
                    new Assert\NotBlank()
                )
            ))
            ->add('content', 'textarea', array(
                'label' => 'app.form.type.posttype.content.label',
                'constraints' => array(
                    new Assert\NotBlank()
                )
            ))
            ->add('slug', 'text', array(
                'label' => 'app.form.type.posttype.slug.label',
                'required' => false
            ))
            ->add('created_at', 'date', array(
                'label'    => 'app.form.type.posttype.created_at.label',
                'required' => false,
                'widget'   => 'single_text',
                'format'   => 'dd.MM.yyyy',
                'constraints' => array(
                     new Assert\Date()
                )
            ))
            ->add('meta_title', 'text', array(
                'label' => 'app.form.type.posttype.meta_title.label',
                'required' => false
            ))
            ->add('meta_description', 'text', array(
                'label' => 'app.form.type.posttype.meta_description.label',
                'required' => false
            ))
            ->add('meta_keywords', 'text', array(
                'label' => 'app.form.type.posttype.meta_keywords.label',
                'required' => false
            ))
            ->add('save', 'submit', array(
                'label' => 'app.form.type.posttype.save.label'
            ));
    }

    public function getName()
    {
        return 'posts';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Blog\Entity\Post',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'post_item'
        ));
    }     
}