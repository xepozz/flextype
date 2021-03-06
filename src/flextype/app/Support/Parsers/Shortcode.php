<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Support\Parsers;

use function md5;

class Shortcode
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Shortcode Fasade
     */
    private $shortcode;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype, $shortcode)
    {
        $this->flextype  = $flextype;
        $this->shortcode = $shortcode;
    }

    /**
     * Shortcode instance
     *
     * @access public
     */
    public function getInstance()
    {
        return $this->shortcode;
    }

    /**
     * Add shortcode handler.
     *
     * @param string   $name    Shortcode
     * @param callable $handler Handler
     *
     * @access public
     */
    public function addHandler(string $name, $handler)
    {
        return $this->shortcode->addHandler($name, $handler);
    }

    /**
     * Add event handler.
     *
     * @param string   $name    Event
     * @param callable $handler Handler
     *
     * @access public
     */
    public function addEventHandler($name, $handler) {
        return $this->shortcode->addEventHandler($name, $handler);
    }

    /**
     * Parses text into shortcodes.
     *
     * @param string $input A text containing SHORTCODE
     *
     * @access public
     */
    public function parse(string $input)
    {
        return $this->shortcode->parse($input);
    }

    /**
     * Processes text and replaces shortcodes.
     *
     * @param string $input A text containing SHORTCODE
     * @param bool   $cache Cache result data or no. Default is true
     *
     * @access public
     */
    public function process(string $input, bool $cache = true)
    {
        if ($cache === true && $this->flextype['registry']->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                return $data_from_cache;
            }

            $data = $this->shortcode->process($input);
            $this->flextype['cache']->save($key, $data);

            return $data;
        }

        return $this->shortcode->process($input);
    }

    /**
     * Get Cache ID for shortcode
     *
     * @param  string $input Input
     *
     * @return string Cache ID
     *
     * @access public
     */
    public function getCacheID(string $input) : string
    {
        return md5('shortcode' . $input);
    }
}
