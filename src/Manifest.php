<?php

namespace Caffeinated\Themes;

use Exception;
use Illuminate\Support\Collection;

class Manifest extends Collection
{
    /**
     * @var string
     */
    protected $path;
    
    /**
     * @var string
     */
    protected $content;
    
    /**
     * Create a new Manifest.
     *
     * @param  null|string  $path
     */
    public function __construct($path = null)
    {
        if (is_null($path)) {
            throw Exception('Please specify a path to your manifest file.');
        }
        
        $this->setPath($path);
        $this->load();
        
        parent::__construct($this->decode());
    }
    
    /**
     * Make a new manifest collection based on the passed file path.
     *
     * @param  string  $path
     * @return static
     */
    public static function make($path = null)
    {
        return new static($path);
    }
    
    /**
     * Load the manifest file content.
     */
    public function load()
    {
        if (file_exists($this->getPath())) {
            $this->setContent(file_get_contents($this->getPath()));
            
            return;
        }
    }
    
    /**
     * Save the manifest file content.
     */
    public function save()
    {
        if (file_exists($this->getPath())) {
            $this->setContent($this->encode());
            
            file_put_contents($this->getPath(), $this->getContent());
            
            return;
        }
    }
    
    /**
     * Decode the manifest content.
     *
     * @return array
     */
    protected function decode()
    {
        return json_decode($this->getContent(), true);
    }
    
    /**
     * Encode the manifest items.
     *
     * @return string
     */
    protected function encode()
    {
        return json_encode($this->items);
    }
    
    /**
     * Set the manifest file path property.
     *
     * @param  string  $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
    
    /**
     * Get the manifest file path property.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Set the manifest content property.
     *
     * @param  string  $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    /**
     * Get the manifest content property.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}