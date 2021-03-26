<?php

namespace Dr10s\DirectoryTree;

class DirectoryTree
{
    private const HASH_START = 0;
    private const HASH_STEP = 2;
    private string $mainDirectory;
    private int $filesPerDirectory;

    public function __construct(string $mainDirectory, int $filesPerDirectory = 10000)
    {
        $this->mainDirectory = $mainDirectory;
        $this->filesPerDirectory = $filesPerDirectory;

        if (!is_writable($this->mainDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" not writable', $this->mainDirectory));
        }

        if ($filesPerDirectory === 0) {
            throw new \RuntimeException('FilesPerDirectory should be greater then 0.');
        }
    }

    public function addDirectoryForFile(string $fileHash, string $fileExtension): string
    {
        $hashPos = self::HASH_START;
        $mainDirectory = $this->getMainDirectoryFromHash($fileHash, $hashPos);
        $this->createByPath($mainDirectory);

        while (
            iterator_count(new \FilesystemIterator($mainDirectory, \FilesystemIterator::SKIP_DOTS)) >= $this->filesPerDirectory
            && $hashPos < strlen($fileHash)
            && $this->checkIsFileExist($mainDirectory, $fileHash, $fileExtension)
        ) {
            $mainDirectory .= '/' . substr($fileHash, $hashPos += self::HASH_STEP, self::HASH_STEP);
            $this->createByPath($mainDirectory);
        }

        return $mainDirectory;
    }

    public function findDirectoryWithFile(string $fileHash, string $fileExtension): ?string
    {
        $hashStart = self::HASH_START;
        $mainDirectory = $this->getMainDirectoryFromHash($fileHash, $hashStart);
        $fileName = $fileHash.'.'.$fileExtension;

        while (!file_exists(sprintf('%s/%s', $mainDirectory, $fileName)) && $hashStart < strlen($fileHash)) {
            $mainDirectory .= '/' . substr($fileHash, $hashStart += self::HASH_STEP, self::HASH_STEP);
        }

        return file_exists(sprintf('%s/%s', $mainDirectory, $fileName)) ? $mainDirectory : null;
    }

    private function createByPath(string $path): bool
    {
        if (!is_dir($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        return true;
    }

    private function getMainDirectoryFromHash(string $hash, int &$hashStart): string
    {
        return  sprintf('%s/%s/%s',
            $this->mainDirectory,
            substr($hash, $hashStart, self::HASH_STEP),
            substr($hash, $hashStart += self::HASH_STEP, self::HASH_STEP)
        );
    }

    private function checkIsFileExist(string $path, string $fileHash, string $fileExtension): bool
    {
        return file_exists(sprintf('%s/%s.%s', $path, $fileHash, $fileExtension));
    }
}