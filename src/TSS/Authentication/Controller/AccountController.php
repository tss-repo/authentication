<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 02/12/2015
 * Time: 12:26
 */

namespace TSS\Authentication\Controller;


use TSS\Authentication\Filter\ProfileFilter;
use TSS\Authentication\Form\UserForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('config');
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $this->identity();

        $form = new UserForm($entityManager, 'user', $config);
        $form->setInputFilter(new ProfileFilter($entityManager, $config));
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
                $entityManager->flush();
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