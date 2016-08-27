<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Controller;

use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Entity\UserInterface;
use TSS\Authentication\Filter\ProfileFilter;
use TSS\Authentication\Form\PasswordChangeForm;
use TSS\Authentication\Form\UserForm;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $routes;

    /**
     * AccountController constructor.
     * @param EntityManagerInterface $entityManager
     * @param array $config
     */
    public function __construct(EntityManagerInterface $entityManager, array $config)
    {
        $this->entityManager = $entityManager;
        $this->setConfig($config);
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

        $this->routes = $config['tss']['authentication']['routes'];
        $this->config = $config['tss']['authentication']['config'];
    }

    public function indexAction()
    {
        /** @var UserInterface $user */
        $user = $this->identity();

        $form = new UserForm($this->entityManager, 'user', $this->config);
        $form->setInputFilter(new ProfileFilter($this->entityManager, $this->config));
        $form->setAttribute('action', $this->url()->fromRoute($this->routes['account']['name'], $this->routes['account']['params'], $this->routes['account']['options'], $this->routes['account']['reuseMatchedParams']));
        $form->bind($user);
        $form->get('submit')->setValue(_('Update'));

        $avatar = $user->getAvatar();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            $form->setData($post);

            if ($form->isValid()) {
                if ($user->getAvatar()['error'] == 0) {
                    $user->setAvatar($this->tssImageThumb()->process($user->getAvatar()));
                } else {
                    $user->setAvatar($avatar);
                }
                $this->entityManager->flush();
                $this->flashMessenger()->addInfoMessage(_('Profile updated with success!'));
                return $this->redirect()->toRoute($this->routes['account']['name'], $this->routes['account']['params'], $this->routes['account']['options'], $this->routes['account']['reuseMatchedParams']);
            } else {
                $this->flashMessenger()->addErrorMessage(_('Form with errors!'));
            }
        }

        $form->prepare();
        $viewModel = new ViewModel(array(
            'form' => $form,
            'user' => $user,
            'routes' => $this->routes
        ));

        return $viewModel;
    }

    public function passwordChangeAction()
    {
        $credentialRepo = $this->entityManager->getRepository($this->config['credentialClass']);
        /** @var UserInterface $user */
        $user = $this->identity();

        $form = new PasswordChangeForm();
        $form->setAttribute('action', $this->url()->fromRoute($this->routes['password-change']['name'], $this->routes['password-change']['params'], $this->routes['password-change']['options'], $this->routes['password-change']['reuseMatchedParams']));

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);

            if ($form->isValid()) {
                $data = $form->getData();
                $credential = $credentialRepo->findOneBy(array($this->config['credentialIdentityProperty'] => $user, 'type' => $this->config['credentialType']));
                $passwordOld = sha1(sha1($data['password-old']));
                $passwordNew = sha1(sha1($data['password-new']));
                $password = $credential->getValue();

                if ($password == $passwordOld) {
                    $credential->setValue($passwordNew);
                    $this->entityManager->flush();
                    $this->flashMessenger()->addSuccessMessage(_('Your password has been changed successfully!'));
                    return $this->redirect()->toRoute('tssAuthentication/default', array('controller' => 'account'));
                } else {
                    $this->flashMessenger()->addErrorMessage(_('Your current password is incorrect.'));
                }
            } else {
                $this->flashMessenger()->addErrorMessage(_('Form with errors!'));
            }
        }

        $form->prepare();
        $viewModel = new ViewModel(array(
            'form' => $form,
            'user' => $user,
            'routes' => $this->routes
        ));

        return $viewModel;
    }
}
