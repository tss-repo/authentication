<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 02/12/2015
 * Time: 12:26
 */

namespace TSS\Authentication\Controller;


use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Filter\ProfileFilter;
use TSS\Authentication\Form\UserForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * AccountController constructor.
     * @param array $config
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(array $config, EntityManagerInterface $entityManager)
    {
        $this->config = $config;
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

    public function indexAction()
    {
        $user = $this->identity();

        $form = new UserForm($this->getEntityManager(), 'user', $this->getConfig());
        $form->setInputFilter(new ProfileFilter($this->getEntityManager(), $this->getConfig()));
        $form->setAttribute('action', $this->url()->fromRoute('tssAuthentication/default', array('controller' => 'account')));
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
                if($user->getAvatar()['error'] == 0){
                    $user->setAvatar($this->imageThumb()->process($user->getAvatar()));
                } else {
                    $user->setAvatar($avatar);
                }
                $this->getEntityManager()->flush();
                $this->flashMessenger()->addInfoMessage(_('Profile updated with success!'));
                return $this->redirect()->toRoute('tssAuthentication/default', array('controller' => 'account'));
            } else {
                $this->flashMessenger()->addErrorMessage(_('Form with errors!'));
            }
        }

        $viewModel = new ViewModel(array(
            'form' => $form,
            'user' => $user
        ));

        return $viewModel;
    }
}