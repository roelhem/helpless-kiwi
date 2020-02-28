<?php

namespace App\Form\Document;

use App\Entity\Document\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('slug', TextType::class, [
                'label' => 'Expressie naam',
                'required' => false,
            ])
            ->add('userEditOnly', CheckboxType::class, [
                'label' => 'Alleen aanpassen door gebruiker?',
                'required' => false,
            ])
            ->add('valueType', ChoiceType::class, [
                'choice_loader' => new CallbackChoiceLoader(function () {
                    $vals = array_keys(DocumentType::TYPES());

                    return array_combine($vals, $vals);
                }),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Field::class,
        ]);
    }
}