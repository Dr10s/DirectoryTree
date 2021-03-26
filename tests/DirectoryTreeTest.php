<?php

use Dr10s\DirectoryTree\DirectoryTree;
use PHPUnit\Framework\TestCase;

class DirectoryTreeTest extends TestCase
{
    private string $mainDir;
    private DirectoryTree $directoryTree;
    private string $fileExtension;
    private string $fileHash;

    protected function setUp(): void
    {
        $this->mainDir = './files';
        $this->directoryTree = new DirectoryTree($this->mainDir);
        $this->fileHash = md5('DirectoryTree');
        $this->fileExtension = 'txt';
    }

    public function testAddDirectoryForFile(): string
    {
        $directory = $this->directoryTree->addDirectoryForFile($this->fileHash, $this->fileExtension);

        file_put_contents(
            sprintf('%s/%s.%s', $directory, $this->fileHash, $this->fileExtension),
            ''
        );

        self::assertDirectoryExists($directory);

        return $directory;
    }

    /**
     * @depends testAddDirectoryForFile
     */
    public function testFindDirectoryWithFile(string $directory): void
    {
        self::assertEquals(
            $this->directoryTree->findDirectoryWithFile($this->fileHash, $this->fileExtension),
            $directory,
        );
    }

    public function testDirectoryRotate(): void
    {
        $directoryTree = new DirectoryTree($this->mainDir, 1);
        $fileHash = md5(microtime());
        $fileExtension = 'txt';
        $firstDirectory = $directoryTree->addDirectoryForFile($fileHash, $fileExtension);
        $secondFileHash = md5(microtime());

        self::assertNotEquals($firstDirectory, $directoryTree->addDirectoryForFile($secondFileHash, $fileExtension));
    }

    public function tearDown(): void
    {
        // TODO: unlink test generated files
    }
}
