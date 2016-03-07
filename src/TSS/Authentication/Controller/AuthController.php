<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 26/08/2015
 * Time: 14:35
 */

namespace TSS\Authentication\Controller;


use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Entity\AbstractCredential;
use TSS\Authentication\Form\SigninForm;
use TSS\Authentication\Form\SignupForm;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authentication;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * AuthController constructor.
     * @param array $config
     * @param AuthenticationServiceInterface $authentication
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(array $config, AuthenticationServiceInterface $authentication, TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        $this->config = $config;
        $this->authentication = $authentication;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return AuthenticationServiceInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param AuthenticationServiceInterface $authentication
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function signinAction()
    {

        if ($this->identity()) {
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
        }

        $form = new SigninForm();
        $form->setAttribute('action', $this->url()->fromRoute($this->config['tss']['authentication']['routes']['authenticate']['name']));
        $form->prepare();

        $viewModel = new ViewModel(array(
            'form' => $form,
            'authRoutes' => $this->config['tss']['authentication']['routes']
        ));

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
                $authenticationAdapter = $this->getAuthentication()->getAdapter();
                $authenticationAdapter->setIdentityValue($form->get('username')->getValue());
                $authenticationAdapter->setCredentialValue(sha1(sha1($form->get('password')->getValue())));

                $authResult = $this->getAuthentication()->authenticate();

                if ($authResult->isValid()) {
                    $identity = $authResult->getIdentity();

                    $authenticationStorage = $this->getAuthentication()->getStorage();
                    if ($form->get('remember-me')->getValue() == 1) {
                        $authenticationStorage->setRememberMe(1);
                    }
                    $authenticationStorage->write($identity);

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

        $this->getAuthentication()->getStorage()->forgetMe();
        $this->getAuthentication()->clearIdentity();
        $this->flashMessenger()->addErrorMessage(_('You\'re disconected!'));
        return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
    }

    public function signupAction()
    {
        if ($this->identity()) {
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
        }

        $form = new SignupForm($this->getEntityManager(), $this->config);
        $form->setAttribute('action', $this->url()->fromRoute($this->config['tss']['authentication']['routes']['signup']['name']));
        $userClass = $this->config['tss']['authentication']['config']['identityClass'];
        $user = new $userClass;
        $form->bind($user);
        $form->prepare();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $credentialClass = $this->config['tss']['authentication']['config']['credentialClass'];
                $credential = new $credentialClass;
                $credential->setType(AbstractCredential::TYPE_PASSWORD);
                $credential->setValue(sha1(sha1($form->get('password')->getValue())));
                $credential->setUser($user);
                $user->addCredential($credential);

                $role = $this->getEntityManager()->find($this->config['tss']['authentication']['config']['roleClass'], $this->config['tss']['authentication']['config']['roleDefault']);
                $user->setRole($role);

                $user->setAvatar($this->imageThumb()->getDefaultImageThumb());
                $user->setActive($this->config['tss']['authentication']['config']['identityActive']);
                $user->setToken(sha1(uniqid(mt_rand(), true)));

                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();


                $fullLink = $this->url()->fromRoute(
                    $this->config['tss']['authentication']['routes']['confirm-email']['name'],
                    array(
                        'token' => $user->getToken(),
                    ),
                    array('force_canonical' => true)
                );
                $to = $user->getEmail();
                $subject = $this->getTranslator()->translate("Please, confirm your registration!");
                $body = $this->getTranslator()->translate("Please, click the link to confirm your registration => ") . $fullLink;

                $this->email()->send($to, $subject, $body);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->getTranslator()->translate('An email has been sent to %s. Please, check your inbox and confirm your registration!'), $user->getEmail()));

                return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['signin']['name']);
            } else {
                $this->flashMessenger()->addErrorMessage(_('Form with errors!'));
            }
        }

        $viewModel = new ViewModel(array(
            'form' => $form,
            'authRoutes' => $this->config['tss']['authentication']['routes']
        ));

        $viewModel->setTemplate($this->config['tss']['authentication']['template']['signup']);

        $this->layout($this->config['tss']['authentication']['layout']);
        return $viewModel;
    }

    public function confirmEmailAction()
    {
        $identityRepo = $this->getEntityManager()->getRepository($this->config['tss']['authentication']['config']['identityClass']);

        $token = $this->params()->fromRoute('token', 0);

        if ($this->identity()) {
            $this->getAuthentication()->getStorage()->forgetMe();
            $this->getAuthentication()->clearIdentity();
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
            $this->getEntityManager()->flush();
            $this->getAuthentication()->getStorage()->write($identity);
            $this->flashMessenger()->addSuccessMessage(_('Email confirmed.'));
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['redirect']['name']);
        } else {
            $identity->setToken(sha1(uniqid(mt_rand(), true))); // change immediately taken to prevent multiple requests to db
            $this->getEntityManager()->flush();
            $this->flashMessenger()->addInfoMessage(_('Email already verified. Please login!'));
            return $this->redirect()->toRoute($this->config['tss']['authentication']['routes']['signin']['name']);
        }
    }
}