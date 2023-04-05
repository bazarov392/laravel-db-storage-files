## File model

The file model consists of
* `path` - Full path to the file
* `size` - File size in bytes
* `data` - File content
* `hash` - sha256 hash of the file
* `deletetion_date` - If not null, the date of file deletion

Get the file name
```php
$fileName = basename($file->path);
```

## Adding files

Adding a file with the name text.txt that contains the string 'Hello, World!

```php
$file = storageFiles()->write('/text.txt', 'Hello, World!');
```

Adding a temporary file that will be deleted in one day
```php
$file = storageFiles()->write('/text.txt', 'Hello, World!', now()->addDay());
```

## Checking the existence of a file

Checking the existence of a file by its path
```php

if(storageFiles()->containsPath('/text.txt'))
{
    // ...
}

```

Checking the existence of a file by its ID
```php
$fileId = '1523e887-3b42-4364-8711-314211897ce9';
if(storageFiles()->containsFileId($fileId))
{
    // ...
}
```

## Getting a file

Getting a file by its path
```php
$file = storageFiles()->getFromPath('/text.txt');
```

Getting a file by its ID
```php
$fileId = '1523e887-3b42-4364-8711-314211897ce9';
$file = storageFiles()->getFromFileId($fileId);
```

Getting multiple files by their path. Note that the getList() method returns an array of strings for the 'path' parameter
```php
$files = storageFiles()->getList('/');
// ["/text.txt"]
```


## Deleting a file
Since the file is an Eloquent model, it has a delete() method
```php
$file = storageFiles()->getFromPath('/text.txt');
$file->delete();
```


