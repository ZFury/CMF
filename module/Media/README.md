Using images with your module
==============================
If you created the new module, and it uses images, you
should use trait in your entity class, for example

```
*
class User
{
    use \Starter\Media\Image;
    ...
*
```
Attention! This trait has abstract method that have to be
implemented:
  * this trait needs you to have getEntityName method in your entity class,
  that return the alias of your entity
  * this trait also need you to have setLifecycleArgs method, here is an
  example, how it can be done
```
*
    /**
     * ...
     * @ORM\HasLifecycleCallbacks
     */
    class User
    {
        use \Starter\Media\Image;
        ...

    /**
     * @ORM\PostLoad
     */
    public function setLifecycleArgs(LifecycleEventArgs $args)
    {
        $this->lifecycleArgs = $args;
    }
*
```

