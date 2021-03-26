# DirectoryTree

В очень многих проектах мне доводилось увидеть, что их разработчики относились небрежно к хранению файлов, что достовало очень много проблем когда клиентская база проектов увеличивалась.
Да даже 500к файлов хватит для того что бы заставить ОС поднапрячся для чтения директории.

Данный пакет успешно решает проблему хранения файлов в древовидной структуре директорий.

### Example:
```
 $directoryTree = new DirectoryTree('./Files');
 $fileHash = md5('file_name'.microtime());
 $fileExtension = 'txt';
 $directory = $directoryTree->addDirectoryForFile($fileHash, $fileExtension);
 
 file_put_contents(
     sprintf('%s/%s.%s', $directory, $fileHash, $fileExtension),
     ''
   );
   
```
