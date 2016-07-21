<?php

namespace Dgerken\EnhancedStaticContentDeploy\Cache;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use \Magento\Framework\Filesystem;

class StaticFileCache extends AbstractCache
{

    /**
     * Base Filesystem class
     * @var Filesystem
     */
    protected $filesystem;

    /**
     *  Filename constant
     */
    const FILENAME = 'static_content_hashes.dat';

    /**
     * FileSystem constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->getContent();
    }

    /**
     * @param string $file
     * @param string $keyPrefix
     * @return bool
     */
    public function hasChange($file, $keyPrefix)
    {
        list($hashKey, $hashVal) = $this->getKeyValueHash($file, $keyPrefix);
        if(!isset($this->storage[$hashKey]) || $this->storage[$hashKey] != $hashVal)
        {
            $this->add($hashKey, $hashVal);
            return true;
        };

        return false;
    }

    /**
     * @param $file
     * @param $keyPrefix
     * @return array
     */
    protected function getKeyValueHash($file, $keyPrefix = "")
    {
        $hashKey = crc32($keyPrefix . $file);
        $hashVal = hash_file("crc32b", $file);
        return [$hashKey, $hashVal];
    }


    /**
     * Get content from file
     */
    protected function getContent()
    {
        try {
            $content = $this->getReader()->readFile(StaticFileCache::FILENAME);
            $this->storage = unserialize(gzdecode($content));
        } catch (FileSystemException $e) {
            //ToDo: could not read file
        } catch (\Exception $e)
        {
            //ToDo: could not unserialize / decode file
        }
    }

    /**
     * @return Filesystem\Directory\WriteInterface
     */
    protected function getWriter()
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
    }

    /**
     * @return Filesystem\Directory\ReadInterface
     */
    protected function getReader()
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::TMP);
    }


    /**
     * Persist hasharray to file
     */
    public function save()
    {
        $writer = $this->getWriter();
        $file = $writer->openFile(StaticFileCache::FILENAME, 'w');
        try {
            $file->lock();
            try {
                if(is_array($this->storage))
                    $file->write(gzencode(serialize($this->storage)));
            }
            finally {
                $file->unlock();
            }
        }
        finally {
            $file->close();
        }
    }

    /**
     * Flushes the static-content cache
     */
    public function flushCache()
    {
        $writer = $this->getWriter();
        $writer->delete(StaticFileCache::FILENAME);
    }
}
