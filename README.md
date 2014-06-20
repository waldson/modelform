ModelForm    
=========

Easily create forms, validate and filter data. 

Greatly inspired by the awesome  [Respect/Validation](https://github.com/Respect/Validation) package.

**PS:** not ready for production yet.

Installation (via Composer)
-----------


    "require" : {
        "w5n/modelform" : "dev-master"
    }


Usage
----

####Create a Model###
Let's create a model first:    
    
    <?php
    use W5n\DefaultModel;
    
    class ModelFoo extends DefaultModel
    {
        public function __construct()
        {
            $this->text('name', 'Nome', true);
            $this->date('birth_date')->required()->pastDate();
            $this->text('token')->filter('sha1');            
        }
    }
    

Now that we have our validation model, we can assign values to it:

    <?php
    //...
    $m = ModelFoo::create()->populate(
        array(
            'name'       => 'Waldson',
            'birth_date' => '19/05/1989',
            'token'      => 't0k3n'
        )
    ); 
    //or $m = ModelFoo::create()->populate($_POST);

or...
    
    <?php
    //...
    $m = new ModelFoo();
    $m->name       = 'Waldson';
    $m->birth_date = '19/05/1989';
    $m->token      = 't0k3n';
 

####Model's useful methods

    $b->validate(); //true
    
    $b->name = '';
    $b->validate(); //false
    
    $b->getValues();
    /*
    *Array
    *(
    *    [name] => Waldson
    *    [birth_date] => 1990-10-20
    *    [token] => 91ba11729e0504813d3fa2ea146c360807aeeee0
    *)
    */
    
###Form###

You can use your model to render your form easily (unlike most 'easy forms' packages out there):
    
    <?php
    use W5n\Form\ModelForm;
    //...
    $form = ModelForm::create($m);
    //..
    echo $form;

There is also a [Boostrap 3](http://getbootstrap.com/) form renderer:

    <?php
    use W5n\Form\FormBootstrap3;
    //...
    $form = FormBootstrap3::create($m);
    $form->setLayout(array(
        array('name' => 5, 'birth_date' => 2),
        array('token' => 7),
        array('submit' => 7)
    ));
    //On your view file...
    echo $form;
    
The primary array values define rows, key/value pairs inside primary values define field/fieldSize. With previous layout you'll get a three rows form: first row with 'name'  and 'birth_date' inputs and second row with 'token' input and third row with a submit button. 'submit' key is a extra field automatically added to form object. 

**PS:** You need to add Bootstrap 3 CSS file to your view file.

###Validators###

soon...

###Filters###

soon...

###Advanced Usage###

soon...


    



    

    