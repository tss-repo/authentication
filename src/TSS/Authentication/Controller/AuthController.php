<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 26/08/2015
 * Time: 14:35
 */

namespace TSS\Authentication\Controller;


use TSS\Authentication\Form\SigninForm;
use TSS\Authentication\Form\SignupForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    public function signinAction()
    {
        $config = $this->getServiceLocator()->get('config');

        if ($this->identity()) {
            return $this->redirect()->toRoute($config['tss']['authentication']['routes']['redirect']['name']);
        }

        $form = new SigninForm();
        $form->setAttribute('action', $this->url()->fromRoute($config['tss']['authentication']['routes']['authenticate']['name']));
        $form->prepare();

        $viewModel = new ViewModel(array(
            'form' => $form,
            'authRoutes' => $config['tss']['authentication']['routes']
        ));

        $viewModel->setTemplate($config['tss']['authentication']['template']['signin']);

        $this->layout($config['tss']['authentication']['layout']);

        return $viewModel;
    }

    public function authenticateAction()
    {
        $config = $this->getServiceLocator()->get('config');

        if ($this->identity()) {
            return $this->redirect()->toRoute($config['tss']['authentication']['routes']['redirect']['name']);
        }

        $form = new SigninForm();
        $form->setAttribute('action', $this->url()->fromRoute($config['tss']['authentication']['routes']['authenticate']['name']));
        $form->prepare();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {
                $authService = $this->getServiceLocator()->get('authentication');
                $authAdapter = $authService->getAdapter();
                $authAdapter->setIdentityValue($form->get('username')->getValue());
                $authAdapter->setCredentialValue(sha1(sha1($form->get('password')->getValue())));

                $authResult = $authService->authenticate();

                if ($authResult->isValid()) {
                    $identity = $authResult->getIdentity();

                    $authStorage = $authService->getStorage();
                    if ($form->get('remember-me')->getValue() == 1) {
                        $authStorage->setRememberMe(1);
                    }
                    $authStorage->write($identity);

                    $this->flashMessenger()->addSuccessMessage(_('Sign in with success!'));

                    return $this->redirect()->toRoute($config['tss']['authentication']['routes']['redirect']['name']);
                } else {
                    $this->flashMessenger()->addErrorMessage(_('Username or password is invalid.'));
                }
            }
        }

        return $this->redirect()->toRoute($config['tss']['authentication']['routes']['signin']['name']);
    }

    public function signoutAction()
    {
        $config = $this->getServiceLocator()->get('config');
        if (!$this->identity()) {
            return $this->redirect()->toRoute($config['tss']['authentication']['routes']['signin']['name']);
        }

        $authService = $this->getServiceLocator()->get('authentication');
        $authService->getStorage()->forgetMe();
        $authService->clearIdentity();
        $this->flashMessenger()->addErrorMessage(_('You\'re disconected!'));
        return $this->redirect()->toRoute($config['tss']['authentication']['routes']['redirect']['name']);
    }

    public function signupAction()
    {
        $config = $this->getServiceLocator()->get('config');
        $tranlator = $this->getServiceLocator()->get('translator');

        if ($this->identity()) {
            return $this->redirect()->toRoute($config['tss']['authentication']['routes']['redirect']['name']);
        }
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $form = new SignupForm($entityManager, $config);
        $form->setAttribute('action', $this->url()->fromRoute($config['tss']['authentication']['routes']['signup']['name']));
        $user = new $config['tss']['authentication']['config']['identityClass'];
        $form->bind($user);
        $form->prepare();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $credential = new $config['tss']['authentication']['config']['credentialClass']();
                $credential->setType($config['tss']['authentication']['config']['credentialType']);
                $credential->setValue(sha1(sha1($form->get('password')->getValue())));
                $credential->setUser($user);
                $user->addCredential($credential);

                $role = $entityManager->find($config['tss']['authentication']['config']['roleClass'], $config['tss']['authentication']['config']['roleDefault']);
                $user->setRole($role);

                $user->setAvatar($this->imageThumb()->getDefaultImageThumb());
                $user->setActive($config['tss']['authentication']['config']['identityActive']);
                $user->setToken(sha1(uniqid(mt_rand(), true)));

                $entityManager->persist($user);
                $entityManager->flush();


                $fullLink = $this->url()->fromRoute(
                    $config['tss']['authentication']['routes']['confirm-email']['name'],
                    array(
                        'token' => $user->getToken(),
                    ),
                    array('force_canonical' => true)
                );
                $to = $user->getEmail();
                $subject = $tranlator->translate("Please, confirm your registration!");
                $body = $tranlator->translate("Please, click the link to confirm your registration => ") . $fullLink;

                $this->email()->send($to, $subject, $body);

                $this->flashMessenger()->addSuccessMessage(sprintf($tranlator->translate('An email has been sent to %s. Please, check your inbox and confirm your registration!'), $user->getEmail()));

                return $this->redirect()->toRoute($config['tss']['authentication']['routes']['signin']['name']);
            } else {
                $this->flashMessenger()->addErrorMessage(_('Form with errors!'));
            }
        }

        $viewModel = new ViewModel(array(
            'form' => $form,
            'authRoutes' => $config['tss']['authentication']['routes']
        ));

        $viewModel->setTemplate($config['tss']['authentication']['template']['signup']);

        $this->layout($config['tss']['authentication']['layout']);
        return $viewModel;
    }

    public function confirmEmailAction()
    {
        $config = $this->getServiceLocator()->get('config');
        $authService = $this->getServiceLocator()->get('authentication');

        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $identityRepo = $entityManager->getRepository($config['tss']['authentication']['config']['identityClass']);

        $token = $this->params()->fromRoute('token', 0);

        if ($this->identity()) {
            $authService->getStorage()->forgetMe();
            $authService->clearIdentity();
        }

        $qb = $identityRepo->createQueryBuilder('i');
        $qb->where('i.token = :token');
        $qb->setParameter('token', $token);
        $identity = $qb->getQuery()->getOneOrNullResult();

        if ($identity == null) {
            $this->flashMessenger()->addErrorMessage(_('Token invalid or you already confirmed this link.'));
            return $this->redirect()->toRoute($config['tss']['authentication']['routes']['signin']['name']);
        }

        if (!$identity->isConfirmedEmail()) {
            $identity->setToken(sha1(uniqid(mt_rand(), true))); // change immediately taken to prevent multiple requests to db
            $identity->setActive(true);
            $identity->setConfirmedEmail(true);
            $entityManager->flush();
            $authService->getStorage()->write($identity);
            $this->flashMessenger()->addSuccessMessage(_('Email confirmed.'));
            return $this->redirect()->toRoute($config['tss']['authentication']['routes']['redirect']['name']);
        } else {
            $identity->setToken(sha1(uniqid(mt_rand(), true))); // change immediately taken to prevent multiple requests to db
            $entityManager->flush();
            $this->flashMessenger()->addInfoMessage(_('Email already verified. Please login!'));
            return $this->redirect()->toRoute($config['tss']['authentication']['routes']['signin']['name']);
        }
    }
}