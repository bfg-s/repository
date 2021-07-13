# Extension repository
Package To add the functionality of the repository pattern and its generator.

## Install
```bash
composer require bfg/repository
```

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
