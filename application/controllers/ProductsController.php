<?php
/**
 * controllers/PostsController.php  
 */  
class ProductsController extends Zend_Controller_Action  
{  

  public function init() // called always before actions  
  {  
      $this->Products = new Products(); // DbTable  
  }
  public function addAction()  
  {  
      $form = $this->getForm(); // getting the post form
      if ($this->getRequest()->isPost()) { //is it a post request ?  
          $postData = $this->getRequest()->getPost(); // getting the $_POST data  
          if ($form->isValid($postData)) {  
              $formData = $form->getValues(); // data filtered  
              // created and updated fields  
              $formData += array('pro_created_at' => date('Y-m-d H:i:s'), 'pro_updated_at' => date('Y-m-d H:i:s'));  
              $this->Products->insert($formData); // database insertion
              $this->_redirect('/products/index');  
          }  
          else $form->populate($postData); // show errors and populate form with $postData  
      }
      $this->view->form = $form; // assigning the form to view  
  }
  public function indexAction()  
  {  
      // get all posts - the newer first  
      $select = $this->Products->select()
                               ->from('products',['pro_id','pro_name','pro_price','pro_description','pro_image','pro_created_at','pro_updated_at'])
                               ->join('categories','products.pro_cat_id = categories.cat_id', 'cat_name')
                               ->order('pro_id desc')
                               ->setIntegrityCheck(false);

         
    $this->view->products = $this->Products->fetchAll($select);
    // $stmt = $select->query();
    // $result = $stmt->fetchAll();
      //$this->view->products = $this->Products->fetchAll(null, 'pro_id desc');
  }
///////////////////////////
  public function showAction()  
  {  
      $id = $this->getRequest()->getParam('id');  
      if ($id > 0) {  
          $product = $this->Products->find($id)->current(); // or $this->Products->fetchRow("id = $id");  
          $this->view->product = $product;  
      }  
      else $this->view->message = 'The post ID does not exist';  
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
                  $formData += array('pro_updated_at' => date('Y-m-d H:i:s'));  
                  $this->Products->update($formData, "pro_id = $id"); // update  
                  $this->_redirect('/products/index');  
              }  
              else $form->populate($postData);  
          }  
          else {  
              $post = $this->Products->find($id)->current();  
              $form->populate($post->toArray()); // populate method parameter has to be an array
              // add the id hidden field in the form  
              $hidden = new Zend_Form_Element_Hidden('id');  
              $hidden->setValue($id);
              $form->addElement($hidden);  
          }  
      }  
      else $this->view->message = 'The post ID does not exist';
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
          $this->Products->delete("pro_id = $id");
          $this->_redirect('/products/index');  
      }  
  }


  //////////////////////////////
  public function getForm()  
  {  
      $Categories = new Categories();
      $getCategories = $Categories->fetchAll(null,'cat_id desc');
      $categories= [];
      $categories[''] = ":: Select a category";
      foreach($getCategories as $getCategory) {
        $categories[$getCategory->cat_id] = $getCategory->cat_name;
      }
      $name = new Zend_Form_Element_Text('pro_name');  
      $name->setLabel('Name')  
          ->setDescription('Just put the product name here')  
          ->setRequired(true) // required field  
          ->addValidator('StringLength', false, array(10, 120)) // min 10 max 120  
          ->addFilters(array('StringTrim'));
      $category = new Zend_Form_Element_Select('pro_cat_id');  
      $category->setLabel('Category')  
          ->setDescription('Select the post category')  
          ->setRequired(true)  
        //   ->setMultiOptions(array(  
        //       '' => ':: Select a category',  
        //       'php' => 'PHP',  
        //       'database' => 'Database',  
        //       'zf' => 'Zend Framework'  
        //       // ... more categories if you want  
        //   ))  
        ->setMultiOptions($categories)   
          ->addFilters(array('StringToLower', 'StringTrim')); // force to lowercase and trim
      $price = new Zend_Form_Element_Text('pro_price');  
      $price->setLabel('Price')
          ->setDescription('Just put the product price here')  
          ->setRequired(true);  

      $description = new Zend_Form_Element_Textarea('pro_description');  
      $description->setLabel('Description')  
          ->setRequired(true)  
          ->setDescription('Product Description')
          ->addFilters(array('HtmlEntities')); // remove HTML tags

      $image = new Zend_Form_Element_File('pro_image');  
      $image->setLabel('Image')  
          //->setRequired(true)  
          ->setDescription('Product Image')
          ->addFilters(array('HtmlEntities'))
          ->setDestination(PUBLIC_PATH.'/image'); // remove HTML tags

      $submit = new Zend_Form_Element_Submit('submit');  
      $submit->setLabel('submit') // the button's value  
          ->setIgnore(true); // very usefull -> it will be ignored before insertion
      $form = new Zend_Form();  
      $form->addElements(array($name,$category, $price, $description, $image, $submit))
            ->setMethod('post')
            ->setAction('');  
          // ->setAction('') // you can set your action. We will let blank, to send the request to the same action
      return $form; // return the form  
  }
  
}