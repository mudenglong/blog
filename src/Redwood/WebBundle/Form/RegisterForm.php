<?php 

namespace Redwood\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text');
        $builder->add('email', 'text');
        $builder->add('password', 'password');

    }
    
    //form id, name de zu ming 
    public function getName()
    {
        return 'register';
    }
}