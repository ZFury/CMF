Using images, audio or video upload in your module
==================================================
    ###**STEP 1**###
If you created the new module, and it uses image/audio/video upload, you  
should use trait in your entity class (it is universal for all types of  
files), for example  

```
*
class User
{
    use \Starter\Media\File;
    ...
*
```
Attention! This trait has two abstract methods you need to realize  
  * getEntityName method in your entity class, that return the alias of your entity  
  * setLifecycleArgs method, which must be called on PostLoad that is done in this  
    example  
```
*
    /**
     * ...
     * @ORM\HasLifecycleCallbacks
     */
    class User
    {
        use \Starter\Media\File;
        ...

    /**
     * @ORM\PostLoad
     */
    public function setLifecycleArgs(LifecycleEventArgs $args)
    {
        $this->lifecycleArgs = $args;
    }
    
    public function getEntityName()
    {
        return 'User';
    }
*
```

From this moment you now can call such methods: getImages, getAudios, getVideos that  
simply execute db queries  

    ###**STEP 2**###
You need to create a controller, in which you should have action for displaying  
a form. There are three forms: AudioUpload, VideoUpload, ImageUpload. You must create one  
of them and send to a view with parameters: fileService, module, type and id, where  
 * fileService - is a \Media\Service\File object  
 * module - is a module name, defined in requirejs config, that will send ajax requests  
   to a startImage\Video\AudioUploadAction  
 * type - is a type of file you need to upload. Use predefined constants, such as:  
   \Media\Entity\File::AUDIO_FILETYPE, \Media\Entity\File::VIDEO_FILETYPE,  
   \Media\Entity\File::IMAGE_FILETYPE  
 * id of the entity, which will own the file. You need to bring it from the view  
   to send appropriate ajax request to a startImage\Video\AudioUploadAction.   
   
Next step you need to create .js file to send ajax request. Please, bring as example the one  
from module Test and define it as a requirejs module in config.   

    ###**STEP 3**###
Now you have your controller to implement Interface that matches the type of your file:  
Audio\Video\ImageUploaderInterface. This interface has 4 abstract methods, that are fully  
described in phpdoc. Please, just use methods from Test module as an example!   

    ###**STEP 4**###
In the view in order to display fileupload form you just need to write ONE line (i am not kidding you):  
```
*
$fileService->generateFileUploadForm($module, $type);
*
```
