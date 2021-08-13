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

use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;

class ProfilesForm extends Form
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
        $name->addValidators(
            [
                new PresenceOf(
                    [
                        'message' => 'The name is required',
                    ]
                ),
            ]
        );

        $name->setLabel('Name');

        $this->add($name);

        $active = new SwitchCheck('active');
        $active->setLabel('Active');
        $this->add($active);
    }
}
