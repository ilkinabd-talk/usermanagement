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

namespace Vokuro\Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\QueryBuilder as Paginator;
use TeamTNT\TNTSearch\TNTSearch;
use Vokuro\Forms\ChangePasswordForm;
use Vokuro\Forms\UsersForm;
use Vokuro\Models\PasswordChanges;
use Vokuro\Models\Users;

/**
 * Vokuro\Controllers\UsersController
 * CRUD to manage users
 */
class UsersController extends ControllerBase
{
    public function initialize(): void
    {
        $this->view->setTemplateBefore('private');
        $this->view->setVar('bodyClass', 'main');
    }

    /**
     * Default action, shows the search form
     */
    public function indexAction(): void
    {
        $q       = $this->request->getQuery('q', null, 0);
        $ids     = [0];
        $limit   = $this->request->getQuery('size', 'int', 1);
        $page    = $this->request->getQuery('page', 'int', 1);
        $builder = $this->modelsManager->createBuilder();
        $builder->addFrom(Users::class)->columns('*');

        // Search action
        if ($q && $q !== '') {
            /** @var TNTSearch $search */
            $search = $this->di->get('search');
            $search->selectIndex("name.index");
            $res = $search->search($q);

            if ($res['hits'] > 0) {
                $ids = $res['ids'];
            }

            $builder->andWhere('id IN({ids:array})');
            $builder->setBindParams([
                'ids' => $ids
            ]);
        }

        $paginator = new Paginator([
            'builder' => $builder,
            'limit'   => $limit,
            'page'    => $page,
        ]);

        $page = $paginator->paginate();
        $this->view->setVar('page', $page);
    }

    /**
     * Creates a User
     */
    public function createAction(): void
    {
        $form = new UsersForm();

        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }
            } else {
                $user = new Users([
                    'name'       => $this->request->getPost('name', 'striptags'),
                    'profilesId' => $this->request->getPost('profilesId', 'int'),
                    'email'      => $this->request->getPost('email', 'email'),
                ]);

                if (!$user->save()) {
                    foreach ($user->getMessages() as $message) {
                        $this->flash->error((string)$message);
                    }
                } else {
                    $this->flash->success("User was created successfully");
                }
            }
        }

        $this->view->setVar('form', $form);
    }

    /**
     * Saves the user from the 'edit' action
     *
     * @param int $id
     */
    public function editAction($id)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error('User was not found.');

            return $this->dispatcher->forward([
                'action' => 'index',
            ]);
        }

        $form = new UsersForm($user, [
            'edit' => true,
        ]);

        if ($this->request->isPost()) {
            $user->assign([
                'name'       => $this->request->getPost('name', 'striptags'),
                'profilesId' => $this->request->getPost('profilesId', 'int'),
                'email'      => $this->request->getPost('email', 'email'),
                'banned'     => $this->request->getPost('banned'),
                'suspended'  => $this->request->getPost('suspended'),
                'active'     => $this->request->getPost('active'),
            ]);

            if (!$form->isValid($this->request->getPost())) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }
            } else {
                if (!$user->save()) {
                    foreach ($user->getMessages() as $message) {
                        $this->flash->error((string)$message);
                    }
                } else {
                    $this->flash->success('User was updated successfully.');
                }
            }
        }

        $this->view->setVars([
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Deletes a User
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error('User was not found.');

            return $this->dispatcher->forward([
                'action' => 'index',
            ]);
        }

        if (!$user->delete()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error((string)$message);
            }
        } else {
            $this->flash->success('User was deleted.');
        }

        return $this->dispatcher->forward([
            'action' => 'index',
        ]);
    }

    /**
     * Users must use this action to change its password
     */
    public function changePasswordAction(): void
    {
        $form = new ChangePasswordForm();

        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }
            } else {
                $user = $this->auth->getUser();

                $user->password           = $this->security->hash($this->request->getPost('password'));
                $user->mustChangePassword = 'N';

                $passwordChange            = new PasswordChanges();
                $passwordChange->user      = $user;
                $passwordChange->ipAddress = $this->request->getClientAddress();
                $passwordChange->userAgent = $this->request->getUserAgent();

                if (!$passwordChange->save()) {
                    foreach ($passwordChange->getMessages() as $message) {
                        $this->flash->error((string)$message);
                    }
                } else {
                    $this->flash->success('Your password was successfully changed');
                }
            }
        }

        $this->view->setVar('form', $form);
    }
}
