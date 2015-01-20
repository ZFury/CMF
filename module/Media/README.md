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
  * getId method (I think there's no need to describe what he does)  
  * setEntityManager method, which must be called on PostLoad that is done in this  
    example  
```
*
    /**
     * @ORM\HasLifecycleCallbacks
     */
    class User
    {
        use \Starter\Media\File;
        ...

    /**
     * @ORM\PostLoad
     */
    public function setEntityManager(LifecycleEventArgs $args)
    {
        $this->entityManager = $args->getEntityManager();
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
of them and send to a view with parameters: fileService, type and id, where  
 * fileService - is a \Media\Service\File object  
 * type - is a type of file you need to upload. Use predefined constants, such as:  
   \Media\Entity\File::AUDIO_FILETYPE, \Media\Entity\File::VIDEO_FILETYPE,  
   \Media\Entity\File::IMAGE_FILETYPE  
 * id of the entity, which will own the file. You need to bring it from the view  
   to send appropriate ajax request to a startImage\Video\AudioUploadAction.  
   
Next step you need to create .js file to send ajax request. Please, bring as example the one  
from module Test and define it as a requirejs module in config. You need to require it directly in the view.  

    ###**STEP 3**###
In the view in order to display fileupload form you just need to write ONE line and don't forget to  
require js module that u have created in the previous step:  
```
*
$fileService->generateFileUploadForm($type);
*
```
