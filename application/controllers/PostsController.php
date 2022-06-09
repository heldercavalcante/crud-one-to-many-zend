<?php
/**
 * controllers/PostsController.php  
 */  
class PostsController extends Zend_Controller_Action  
{  

  public function init() // called always before actions  
    {  
        $this->Posts = new Posts(); // DbTable  
    }
    public function addAction()  
    {  
        $form = $this->getForm(); // getting the post form
        if ($this->getRequest()->isPost()) { //is it a post request ?  
            $postData = $this->getRequest()->getPost(); // getting the $_POST data  
            if ($form->isValid($postData)) {  
                $formData = $form->getValues(); // data filtered  
                // created and updated fields  
                $formData += array('created' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s'));  
                $this->Posts->insert($formData); // database insertion  
            }  
            else $form->populate($postData); // show errors and populate form with $postData  
        }
        $this->view->form = $form; // assigning the form to view  
    }
    public function indexAction()  
    {  
        // get all posts - the newer first  
        $this->view->posts = $this->Posts->fetchAll(null, 'created desc');  
    }
///////////////////////////
    public function showAction()  
    {  
        $id = $this->getRequest()->getParam('id');  
        if ($id > 0) {  
            $post = $this->Posts->find($id)->current(); // or $this->Posts->fetchRow("id = $id");  
            $this->view->post = $post;  
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
                    $formData += array('updated' => date('Y-m-d H:i:s'));  
                    $this->Posts->update($formData, "id = $id"); // update  
                    $this->_redirect('/posts/index');  
                }  
                else $form->populate($postData);  
            }  
            else {  
                $post = $this->Posts->find($id)->current();  
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
            $this->Posts->delete("id = $id");
            $this->_redirect('/posts/index');  
        }  
    }


    //////////////////////////////
    public function getForm()  
    {  
        $title = new Zend_Form_Element_Text('title');  
        $title->setLabel('Title')  
            ->setDescription('Just put the post title here')  
            ->setRequired(true) // required field  
            ->addValidator('StringLength', false, array(10, 120)) // min 10 max 120  
            ->addFilters(array('StringTrim'));
        $category = new Zend_Form_Element_Select('category');  
        $category->setLabel('Category')  
            ->setDescription('Select the post category')  
            ->setRequired(true)  
            ->setMultiOptions(array(  
                '' => ':: Select a category',  
                'php' => 'PHP',  
                'database' => 'Database',  
                'zf' => 'Zend Framework'  
                // ... more categories if you want  
            ))  
            ->addFilters(array('StringToLower', 'StringTrim')); // force to lowercase and trim
        $body = new Zend_Form_Element_Textarea('body');  
        $body->setLabel('Post')  
            ->setRequired(true)  
            ->setDescription('Your text')
            ->addFilters(array('HtmlEntities')); // remove HTML tags
        $submit = new Zend_Form_Element_Submit('submit');  
        $submit->setLabel('Post to Blog') // the button's value  
            ->setIgnore(true); // very usefull -> it will be ignored before insertion
        $form = new Zend_Form();  
        $form->addElements(array($title, $category, $body, $submit));  
            // ->setAction('') // you can set your action. We will let blank, to send the request to the same action
        return $form; // return the form  
    }
    
    
    
}