#Using ajax form and error form handler
In order to use ajax (and modal window) in some of your controller's action, you should follow next steps:  
##Step 1:
Add following attributes and data-attributes to your `<a>` or `<button>` element:
- *class       = "dialog"*  
- *data-toggle = "modal"*  
- *data-target = "#formModal"*  
- *data-id = "{id of your entity}"* __(not required)__  
- *data-url    = "{your action's url}"*  
- *data-action = "{header for modal window}"*  

###Example:
```
<a href="#"
   class="btn btn-primary btn-xs dialog"
   data-toggle="modal"
   data-target="#formModal"
   data-id="<?= $currentRoot->getId(); ?>"
   data-url="<?= $this->url('categories/default',
                            array('controller' => 'management',
                                'action' => 'edit',
                                'id' => $currentRoot->getId())) ?>"
   data-action="Edit category">
```
##Step 2:
Add following code to your view, where `<a>` or `<button>` element is laying:
```
<?= $this->partial('layout/default/partial/modal-window.phtml'); ?>
<script>
    require(['form']);
</script>
```
##Step 3:
Add `'ajax'` parameter to your view in controller's action and set it to `true` and then disable layout:
###Example:
for using default crud view:
```
 $viewModel = $this->prepareViewModel($form, $this->getRequest()->isXmlHttpRequest(), null, null, null);
 $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
```
for using custom view:
```
$viewModel = new ViewModel([
    'form' => $form,
    'ajax' => $this->getRequest()->isXmlHttpRequest()
    ]);
$viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
```
##Step 4:
PROFIT!