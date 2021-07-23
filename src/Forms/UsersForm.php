<?php

declare(strict_types=1);

/**
 * This file is part of the Vökuró.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Vokuro\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Vokuro\Models\Profiles;

class UsersForm extends Form
{
    /**
     * @param  null   $entity
     * @param  array  $options
     */
    public function initialize($entity = null, array $options = [])
    {
        $name = new Text(
            'name',
            [
                'placeholder' => 'Name',
            ]
        );
        $name->setLabel('Name');
        $name->addValidators(
            [
                new PresenceOf(
                    [
                        'message' => 'The name is required',
                    ]
                ),
            ]
        );

        $this->add($name);

        $email = new Text(
            'email',
            [
                'placeholder' => 'Email',
            ]
        );

        $email->setLabel('E-mail');
        $email->addValidators(
            [
                new PresenceOf(
                    [
                        'message' => 'The e-mail is required',
                    ]
                ),
                new Email(
                    [
                        'message' => 'The e-mail is not valid',
                    ]
                ),
            ]
        );

        $password = new Password(
            'password',
            [
                'placeholder' => 'Password',
            ]
        );
        $password->setLabel('Password');
        $this->add($password);


        $this->add($email);

        $biography = new TextArea('biography');
        $biography->setLabel('Biography');
        $this->add($biography);


        $profiles = Profiles::find(
            [
                'active = :active:',
                'bind' => [
                    'active' => 'Y',
                ],
            ]
        );
        $this->add(
            (new Select(
                'profilesId',
                $profiles,
                [
                    'using'      => [
                        'id',
                        'name',
                    ],
                    'useEmpty'   => true,
                    'emptyText'  => 'Select profiles',
                    'emptyValue' => '',
                ]
            ))->setLabel('Profiles')
        );

        $this->add(
            (new SwitchCheck(
                'banned'
            ))->setLabel('Banned')
        );

        $this->add(
            (new SwitchCheck(
                'suspended'
            ))->setLabel('Suspended')
        );

        $this->add(
            (new SwitchCheck(
                'active'
            ))->setLabel('Active')
        );
    }
}
