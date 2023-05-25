<?php

namespace Eph\Jaybizzle\CrawlerDetect;

include_once DIGITAL_CORE_DIR . '/lib/Fixtures/Headers.php';
include_once DIGITAL_CORE_DIR .  '/lib/Fixtures/Crawlers.php';
include_once DIGITAL_CORE_DIR .  '/lib/Fixtures/Exclusions.php';
include_once DIGITAL_CORE_DIR .  '/lib/Fixtures/AbstractProvider.php';

use Eph\Jaybizzle\CrawlerDetect\Fixtures\Crawlers;
use Eph\Jaybizzle\CrawlerDetect\Fixtures\Exclusions;
use Eph\Jaybizzle\CrawlerDetect\Fixtures\Headers;

class CrawlerDetect {

    /**
     * The user agent.
     *
     * @var null
     */
    protected $userAgent = null;

    /**
     * Headers that contain a user agent.
     *
     * @var array
     */
    protected $httpHeaders = [];

    /**
     * Store regex matches.
     *
     * @var array
     */
    protected $matches = [];

    /**
     * Crawlers object.
     *
     * @var \Pm\Jaybizzle\CrawlerDetect\Fixtures\Crawlers
     */
    protected $crawlers;

    /**
     * Exclusions object.
     *
     * @var \Pm\Jaybizzle\CrawlerDetect\Fixtures\Exclusions
     */
    protected $exclusions;

    /**
     * Headers object.
     *
     * @var \Pm\Jaybizzle\CrawlerDetect\Fixtures\Headers
     */
    protected $uaHttpHeaders;

    /**
     * The compiled regex string.
     *
     * @var string
     */
    protected $compiledRegex;

    /**
     * The compiled exclusions regex string.
     *
     * @var string
     */
    protected $compiledExclusions;

    /**
     * Class constructor.
     */
    public function __construct(array $headers = null, $userAgent = null) {

        $this->crawlers = new Crawlers();
        $this->exclusions = new Exclusions();
        $this->uaHttpHeaders = new Headers();

        $this->compiledRegex = $this->compileRegex($this->crawlers->getAll());
        $this->compiledExclusions = $this->compileRegex($this->exclusions->getAll());

        $this->setHttpHeaders($headers);
        $this->userAgent = $this->setUserAgent($userAgent);
    }

    /**
     * Compile the regex patterns into one regex string.
     *
     * @param array
     *
     * @return string
     */
    public function compileRegex($patterns) {

        return '(' . implode('|', $patterns) . ')';
    }

    /**
     * Set HTTP headers.
     *
     * @param array|null $httpHeaders
     */
    public function setHttpHeaders($httpHeaders) {

        // Use global _SERVER if $httpHeaders aren't defined.

        if (!is_array($httpHeaders) || !count($httpHeaders)) {
            $httpHeaders = $_SERVER;
        }

        // Clear existing headers.
        $this->httpHeaders = [];

        // Only save HTTP headers. In PHP land, that means
        // only _SERVER vars that start with HTTP_.

        foreach ($httpHeaders as $key => $value) {

            if (strpos($key, 'HTTP_') === 0) {
                $this->httpHeaders[$key] = $value;
            }

        }

    }

    /**
     * Return user agent headers.
     *
     * @return array
     */
    public function getUaHttpHeaders() {

        return $this->uaHttpHeaders->getAll();
    }

    /**
     * Set the user agent.
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent) {

        if (is_null($userAgent)) {

            foreach ($this->getUaHttpHeaders() as $altHeader) {

                if (isset($this->httpHeaders[$altHeader])) {
                    $userAgent .= $this->httpHeaders[$altHeader] . ' ';
                }

            }

        }

        return $userAgent;
    }

    /**
     * Check user agent string against the regex.
     *
     * @param string|null $userAgent
     *
     * @return bool
     */
    public function isCrawler($userAgent = null) {

        $agent = $userAgent ?: $this->userAgent;

        $agent = preg_replace('/' . $this->compiledExclusions . '/i', '', $agent);

        if (strlen(trim($agent)) == 0) {
            return false;
        }

        $result = preg_match('/' . $this->compiledRegex . '/i', trim($agent), $matches);

        if ($matches) {
            $this->matches = $matches;
        }

        return (bool) $result;
    }

    /**
     * Return the matches.
     *
     * @return string|null
     */
    public function getMatches() {

        return isset($this->matches[0]) ? $this->matches[0] : null;
    }

}
