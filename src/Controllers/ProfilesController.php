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

use Phalcon\Filter;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder;
use TeamTNT\TNTSearch\TNTSearch;
use Vokuro\Forms\ProfilesForm;
use Vokuro\Models\Profiles;
use Vokuro\Models\Users;

/**
 * Vokuro\Controllers\ProfilesController
 * CRUD to manage profiles
 */
class ProfilesController extends ControllerBase
{
    const PAGE_SIZE = 10;

    /**
     * Default action. Set the private (authenticated) layout
     * (layouts/private.volt)
     */
    public function initialize(): void
    {
        $this->view->setLayout('private');
    }

    /**
     * Default action, shows the search form
     */
    public function indexAction(): void
    {
        $q     = $this->request->getQuery('q', null, '');
        $ids   = [0];
        $limit = $this->request->getQuery(
            'size',
            [Filter::FILTER_REPLACE => ['all', 99999], Filter::FILTER_ABSINT],
            self::PAGE_SIZE
        );
        $page  = $this->request->getQuery('page', 'absint', 1);

        $builder = $this->modelsManager->createBuilder();
        $builder->addFrom(Profiles::class)->columns('*');

        // Search action
        if ($q && $q !== '') {
            /** @var TNTSearch $search */
            $search                 = $this->di->get('search');
            $search->fuzziness      = true;
            $search->fuzzy_distance = 10;
            $search->selectIndex("name.index");
            $res = $search->search($q);

            if ($res['hits'] > 0) {
                $ids = $res['ids'];
            }

            $builder->andWhere('id IN({ids:array})');
            $builder->setBindParams(
                [
                    'ids' => $ids
                ]
            );
        }

        $paginator = new QueryBuilder(
            [
                'builder' => $builder,
                'limit'   => $limit === 0 ? self::PAGE_SIZE : $limit,
                'page'    => $page,
            ]
        );
        $pager     = $paginator->paginate();

        if (count($pager->getItems()) === 0 && $page > 1 && $limit > 1) {
            $this->response->redirect('/profiles?page=1&size='.self::PAGE_SIZE);

            return;
        }

        $this->view->setVar('page', $pager);
    }

    /**
     * Creates a new Profile
     */
    public function createAction(): void
    {
        $this->view->setTemplateBefore('form');
        if ($this->request->isPost()) {
            $profile = new Profiles(
                [
                    'name'   => $this->request->getPost('name', 'striptags'),
                    'active' => $this->request->getPost('active', null, 'N'),
                ]
            );

            if (! $profile->save()) {
                foreach ($profile->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }
            } else {
                $this->flash->success("Profile was created successfully");
            }
        }

        $this->view->setVar('form', new ProfilesForm(null));
    }

    /**
     * Edits an existing Profile
     *
     * @param  int  $id
     */
    public function editAction($id)
    {
        $this->view->setTemplateBefore('form');
        $profile = Profiles::findFirstById($id);
        if (! $profile) {
            $this->flash->error("Profile was not found");

            return $this->dispatcher->forward(
                [
                    'action' => 'index',
                ]
            );
        }

        if ($this->request->isPost()) {
            $profile->assign(
                [
                    'name'   => $this->request->getPost('name', 'striptags'),
                    'active' => $this->request->getPost('active', null, 'N'),
                ]
            );

            if (! $profile->save()) {
                foreach ($profile->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }
            } else {
                $this->flash->success("Profile was updated successfully");
            }
        }

        $this->view->setVars(
            [
                'form' => new ProfilesForm($profile, ['edit' => true]),
            ]
        );
    }

    /**
     * Deletes a Profile
     *
     * @param  int  $id
     */
    public function deleteAction($id)
    {
        $profile = Profiles::findFirstById($id);
        if (! $profile) {
            $this->flash->error("Profile was not found");

            return $this->dispatcher->forward(
                [
                    'action' => 'index',
                ]
            );
        }

        if (! $profile->delete()) {
            foreach ($profile->getMessages() as $message) {
                $this->flash->error((string)$message);
            }
        } else {
            $this->flash->success("Profile was deleted");
        }

        return $this->dispatcher->forward(
            [
                'action' => 'index',
            ]
        );
    }
}
