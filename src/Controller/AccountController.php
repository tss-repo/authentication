<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Controller;

use Doctrine\ORM\EntityManager;
use TSS\Authentication\Filter\ProfileFilter;
use TSS\Authentication\Form\PasswordChangeForm;
use TSS\Authentication\Form\UserForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('config');
        $routes = $config['tss']['authentication']['routes'];
        $entityManager = $this->getServiceLocator()->get(EntityManager::class);
        $user = $this->identity();

        $form = new UserForm($entityManager, 'user', $config);
        $form->setInputFilter(new ProfileFilter($entityManager, $config));
        $form->setAttribute('action', $this->url()->fromRoute($routes['account']['name'], $routes['account']['params'], $routes['account']['options'], $routes['account']['reuseMatchedParams']));
        $form->bind($user);
        $form->get('submit')->setValue(_('Update'));
        $form->prepare();

        $avatar = $user->getAvatar();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            $form->setData($post);

            if ($form->isValid()) {
                if ($user->getAvatar()['error'] == 0) {
                    $user->setAvatar($this->imageThumb()->process($user->getAvatar()));
                } else {
                    $user->setAvatar($avatar);
                }
                $entityManager->flush();
                $this->flashMessenger()->addInfoMessage(_('Profile updated with success!'));
                return $this->redirect()->toRoute($routes['account']['name'], $routes['account']['params'], $routes['account']['options'], $routes['account']['reuseMatchedParams']);
            } else {
                $this->flashMessenger()->addErrorMessage(_('Form with errors!'));
            }
        }

        $viewModel = new ViewModel(array(
            'form' => $form,
            'user' => $user,
            'routes' => $routes
        ));

        return $viewModel;
    }

    public function passwordChangeAction()
    {
        $config = $this->getServiceLocator()->get('config');
        $routes = $config['tss']['authentication']['routes'];
        $auth = $config['tss']['authentication']['config'];

        $entityManager = $this->getServiceLocator()->get(EntityManager::class);
        $credentialRepo = $entityManager->getRepository($auth['credentialClass']);
        $user = $this->identity();

        $form = new PasswordChangeForm();
        $form->setAttribute('action', $this->url()->fromRoute($routes['password-change']['name'], $routes['password-change']['params'], $routes['password-change']['options'], $routes['password-change']['reuseMatchedParams']));
        $form->prepare();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);

            if ($form->isValid()) {
                $data = $form->getData();
                $credential = $credentialRepo->findOneBy(array($auth['credentialIdentityProperty'] => $user, 'type' => $auth['credentialType']));
                $passwordOld = sha1(sha1($data['password-old']));
                $passwordNew = sha1(sha1($data['password-new']));
                $password = $credential->getValue();

                if ($password == $passwordOld) {
                    $credential->setValue($passwordNew);
                    $entityManager->flush();
                    $this->flashMessenger()->addSuccessMessage(_('Your password has been changed successfully!'));
                    return $this->redirect()->toRoute('tssAuthentication/default', array('controller' => 'account'));
                } else {
                    $this->flashMessenger()->addErrorMessage(_('Your current password is incorrect.'));
                }
            } else {
                $this->flashMessenger()->addErrorMessage(_('Form with errors!'));
            }
        }

        $viewModel = new ViewModel(array(
            'form' => $form,
            'user' => $user,
            'routes' => $routes
        ));

        return $viewModel;
    }
}