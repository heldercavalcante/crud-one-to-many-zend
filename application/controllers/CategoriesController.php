<?php
/**
 * controllers/PostsController.php  
 */  
class CategoriesController extends Zend_Controller_Action  
{  

  public function init() // called always before actions  
  {  
      $this->Categories = new Categories(); // DbTable  
  }
  public function addAction()  
  {  
      $form = $this->getForm(); // getting the post form
      if ($this->getRequest()->isPost()) { //is it a post request ?  
          $postData = $this->getRequest()->getPost(); // getting the $_POST data  
          if ($form->isValid($postData)) {  
              $formData = $form->getValues(); // data filtered  
              // created and updated fields  
              $formData += array('cat_created_at' => date('Y-m-d H:i:s'), 'cat_updated_at' => date('Y-m-d H:i:s'));  
              $this->Categories->insert($formData); // database insertion
              $this->_redirect('/categories/index');   
          }  
          else $form->populate($postData); // show errors and populate form with $postData  
      }
      $this->view->form = $form; // assigning the form to view
  }
  public function indexAction()  
  {  
      // get all posts - the newer first  
      $this->view->categories = $this->Categories->fetchAll(null, 'cat_id desc');  
  }
///////////////////////////
  public function showAction()  
  {  
      $id = $this->getRequest()->getParam('id');  
      if ($id > 0) {  
          $category = $this->Categories->find($id)->current(); // or $this->Products->fetchRow("id = $id");  
          $this->view->category = $category;  
      }  
      else $this->view->message = 'The category ID does not exist';  
  }
  public function editAction()  
  {  
      $form = $this->getForm();  
      $id = $this->getRequest()->getParam('id');
      if ($id > 0) {  
          if ($this->getRequest()->isPost()) { // update form submit  
              $postData = $this->getRequest()->getPost();  
              if ($form->isValid($postData)) {  
                  $formData = $form->getValues();  
                  $formData += array('cat_updated_at' => date('Y-m-d H:i:s'));  
                  $this->Categories->update($formData, "cat_id = $id"); // update  
                  $this->_redirect('/categories/index');  
              }  
              else $form->populate($postData);  
          }  
          else {  
              $post = $this->Categories->find($id)->current();  
              $form->populate($post->toArray()); // populate method parameter has to be an array
              // add the id hidden field in the form  
              $hidden = new Zend_Form_Element_Hidden('id');  
              $hidden->setValue($id);
              $form->addElement($hidden);  
          }  
      }  
      else $this->view->message = 'The category ID does not exist';
      $this->view->form = $form;  
  }
  public function delAction()  
  {  
      $id = $this->getRequest()->getParam('id');  
      if ($id > 0) {  
          // option 1  
          /*$post = $this->Posts->find($id)->current();  
          $post->delete();*/
          // option 2  
          $this->Categories->delete("cat_id = $id");
          $this->_redirect('/categories/index');  
      }  
  }


  //////////////////////////////
  public function getForm()  
  {  
      $name = new Zend_Form_Element_Text('cat_name');  
      $name->setLabel('Name')  
          ->setDescription('Just put the category name here')  
          ->setRequired(true) // required field  
          ->addValidator('StringLength', false, array(5, 120)) // min 10 max 120  
          ->addFilters(array('StringTrim'));

      $submit = new Zend_Form_Element_Submit('submit');  
      $submit->setLabel('submit') // the button's value  
          ->setIgnore(true); // very usefull -> it will be ignored before insertion
      $form = new Zend_Form();  
      $form->addElements(array($name, $submit));  
          // ->setAction('') // you can set your action. We will let blank, to send the request to the same action
      return $form; // return the form  
  }  
}