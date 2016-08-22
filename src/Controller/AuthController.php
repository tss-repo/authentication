<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Controller;

use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Form\SigninForm;
use TSS\Authentication\Form\SignupForm;
use Zend\Authentication\AuthenticationService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $config;

    /**
     * AuthController constructor.
     * @param EntityManagerInterface $entityManager
     * @param AuthenticationService $authenticationService
     * @param TranslatorInterface $translator
     * @param array $config
     */
    public function __construct(EntityManagerInterface $entityManager, AuthenticationService $authenticationService, TranslatorInterface $translator, array $config)
    {
        $this->entityManager = $entityManager;
        $this->authenticationService = $authenticationService;
        $this->translator = $translator;
        $this->config = $config;
    }

    public function signinAction()
    {
        if ($this->identity()) {
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
        }

        $form = new SigninForm();
        $form->setAttribute('action', $this->url()->fromRoute($this->config['tss']['authentication']['routes']['authenticate']['name']));
        $form->prepare();

        $viewModel = new ViewModel([
            'form' => $form,
            'authRoutes' => $this->config['tss']['authentication']['routes']
        ]);

        $viewModel->setTemplate($this->config['tss']['authentication']['template']['signin']);

        $this->layout($this->config['tss']['authentication']['layout']);

        return $viewModel;
    }

    public function authenticateAction()
    {
        if ($this->identity()) {
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
        }

        $form = new SigninForm();
        $form->setAttribute('action', $this->url()->fromRoute($this->config['tss']['authentication']['routes']['authenticate']['name']));
        $form->prepare();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()) {

                $authAdapter = $this->authenticationService->getAdapter();
                $authAdapter->setIdentityValue($form->get('username')->getValue());
                $authAdapter->setCredentialValue(sha1(sha1($form->get('password')->getValue())));

                $authResult = $this->authenticationService->authenticate();

                if ($authResult->isValid()) {
                    $identity = $authResult->getIdentity();

                    $authStorage = $this->authenticationService->getStorage();
                    if ($form->get('remember-me')->getValue() == 1) {
                        $authStorage->setRememberMe(1);
                    }
                    $authStorage->write($identity);

                    $this->flashMessenger()->addSuccessMessage(_('Sign in with success!'));

                    return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
                } else {
                    $this->flashMessenger()->addErrorMessage(_('Username or password is invalid.'));
                }
            }
        }

        return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['signin']['name']);
    }

    public function signoutAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['signin']['name']);
        }

        $this->authenticationService->getStorage()->forgetMe();
        $this->authenticationService->clearIdentity();
        $this->flashMessenger()->addErrorMessage(_('You\'re disconected!'));
        return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
    }

    public function signupAction()
    {
        if ($this->identity()) {
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
        }

        $form = new SignupForm($this->entityManager, 'signup', ['config' => $this->config['tss']['authentication']['config']]);
        $form->setAttribute('action', $this->url()->fromRoute($this->config['tss']['authentication']['routes']['signup']['name']));
        $identityClass = $this->config['tss']['authentication']['config']['identityClass'];
        $user = new $identityClass();
        $form->bind($user);
        $form->prepare();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $credentialClass = $this->config['tss']['authentication']['config']['credentialClass'];
                $credential = new $credentialClass();
                $credential->setType($this->config['tss']['authentication']['config']['credentialType']);
                $credential->setValue(sha1(sha1($form->get('password')->getValue())));
                $credential->setUser($user);
                $user->addCredential($credential);

                $role = $this->entityManager->find($this->config['tss']['authentication']['config']['roleClass'], $this->config['tss']['authentication']['config']['roleDefault']);
                $user->setRole($role);

                $user->setAvatar($this->imageThumb()->getDefaultImageThumb());
                $user->setActive($this->config['tss']['authentication']['config']['identityActive']);
                $user->setToken(sha1(uniqid(mt_rand(), true)));

                $this->entityManager->persist($user);
                $this->entityManager->flush();


                $fullLink = $this->url()->fromRoute(
                    $this->config['tss']['authentication']['routes']['confirm-email']['name'],
                    [
                        'token' => $user->getToken(),
                    ],
                    ['force_canonical' => true]
                );
                $to = $user->getEmail();
                $subject = $this->tranlator->translate("Please, confirm your registration!");
                $body = $this->tranlator->translate("Please, click the link to confirm your registration => ") . $fullLink;

                $this->email()->send($to, $subject, $body);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->tranlator->translate('An email has been sent to %s. Please, check your inbox and confirm your registration!'), $user->getEmail()));

                return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['signin']['name']);
            } else {
                $this->flashMessenger()->addErrorMessage(_('Form with errors!'));
            }
        }

        $viewModel = new ViewModel([
            'form' => $form,
            'authRoutes' => $this->config['tss']['authentication']['routes']
        ]);

        $viewModel->setTemplate($this->config['tss']['authentication']['template']['signup']);

        $this->layout($this->config['tss']['authentication']['layout']);
        return $viewModel;
    }

    public function confirmEmailAction()
    {
        $identityRepo = $this->entityManager->getRepository($this->config['tss']['authentication']['config']['identityClass']);

        $token = $this->params()->fromRoute('token', 0);

        if ($this->identity()) {
            $this->authenticationService->getStorage()->forgetMe();
            $this->authenticationService->clearIdentity();
        }

        $qb = $identityRepo->createQueryBuilder('i');
        $qb->where('i.token = :token');
        $qb->setParameter('token', $token);
        $identity = $qb->getQuery()->getOneOrNullResult();

        if ($identity == null) {
            $this->flashMessenger()->addErrorMessage(_('Token invalid or you already confirmed this link.'));
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['signin']['name']);
        }

        if (!$identity->isConfirmedEmail()) {
            $identity->setToken(sha1(uniqid(mt_rand(), true))); // change immediately taken to prevent multiple requests to db
            $identity->setActive(true);
            $identity->setConfirmedEmail(true);
            $this->entityManager->flush();
            $this->authenticationService->getStorage()->write($identity);
            $this->flashMessenger()->addSuccessMessage(_('Email confirmed.'));
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
        } else {
            $identity->setToken(sha1(uniqid(mt_rand(), true))); // change immediately taken to prevent multiple requests to db
            $this->entityManager->flush();
            $this->flashMessenger()->addInfoMessage(_('Email already verified. Please login!'));
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['signin']['name']);
        }
    }
}