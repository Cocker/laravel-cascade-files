## About

This repository serves as an example on how you can delete (cascade) orphaned files after updating or deleting model in Laravel 11.

Soft deletes are supported meaning that files are deleted only if a model is force deleted.

The main code is in the `CascadesFiles.php` trait. `User.php` and `Post.php` (uses `SoftDeletes`) classes contain the example implementations. 

**Warning:** Since we are utilizing model events to track the orphaned files, this implementation will not work for mass delete/update
e.g. when you use query builder rather than a model instance.


## Disclaimer

This repository should NOT be used as a dependency in your project, however one is free to use the provided code and adapt it to their needs.
