# Extension repository
Package To add the functionality of the repository pattern and its generator. 

## Install
```bash
composer require bfg/repository
```

## About
The main feature of this package is the ability to remember the result of the executed function and return it when called again with the same parameters.
How does this happen? When a repository property is called, it remembers the parameters and the result of the function. When the property is called again, it will return the result of the function that was remembered the first time it was called.
For example, the repository has a `getUsers()` method that returns all users. The first time a method is called by a property, i.e. `$repository->getUsers`, it will execute the method and remember the result. When the property is called again, it will return the result of the method that was remembered the first time it was called. 

## Usage

### Make repository
```bash
php artisan make:repository
```
```bash
Options:
      --methods[=METHODS]  Methods for repository (multiple values allowed)
  -m, --model[=MODEL]      Model of repository
  -f, --force              Create the class even if the repository already exists
```

### Next step
After generating the repository you will appear in the `app/Repositories` folder.
And then you can add your methods to the created repository.
