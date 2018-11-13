<?php declare(strict_types=1);

namespace Becklyn\RobotsTxt\Builder;

use Becklyn\RobotsTxt\Exception\InvalidSitemapUrlException;


class RobotsTxtBuilder
{
    /**
     * @var UserAgentSection[]
     */
    private $sections = [];


    /**
     * @var string[]
     */
    private $sitemaps = [];


    /**
     * @var string
     */
    private $header = "";


    /**
     * @param string $header
     * @return RobotsTxtBuilder
     */
    public function setHeader (string $header) : self
    {
        $lines = \array_map("rtrim", \explode("\n", $header));
        $this->header = "# " . \implode("\n# ", $lines);
        return $this;
    }


    /**
     * @param string[] ...$userAgents
     * @return UserAgentSection
     */
    public function getSection (...$userAgents) : UserAgentSection
    {
        $userAgents = array_map("trim", $userAgents);
        $hashKey = $this->generateUserAgentsHashKey($userAgents);

        if (!isset($this->sections[$hashKey]))
        {
            $this->sections[$hashKey] = new UserAgentSection($userAgents);
        }

        return $this->sections[$hashKey];
    }


    /**
     * Removes the section identified by the given user agents
     *
     * @param string[] ...$userAgents
     */
    public function removeSection (...$userAgents) : void
    {
        $userAgents = array_map("trim", $userAgents);
        $hashKey = $this->generateUserAgentsHashKey($userAgents);
        unset($this->sections[$hashKey]);
    }


    /**
     * Generates the hash key for the given user agents
     *
     * @param array $userAgents
     * @return string
     */
    private function generateUserAgentsHashKey (array $userAgents) : string
    {
        \natsort($userAgents);
        return \implode(":", $userAgents);
    }


    /**
     * @param string $url
     * @return RobotsTxtBuilder
     */
    public function addSitemap (string $url) : self
    {
        $url = \trim($url);

        if (!$this->isValidUrl($url))
        {
            throw new InvalidSitemapUrlException(\sprintf("Invalid sitemap URL: '%s'. The URL must not contain line breaks.", $url));
        }

        $this->sitemaps[] = "Sitemap: {$url}";
        return $this;
    }


    /**
     * @param string $url
     * @return bool
     */
    private function isValidUrl (string $url) : bool
    {
        // just test for new line injection
        return false === \strpos($url, "\n");
    }


    /**
     * Renders the robots.txt to a string
     *
     * @return string
     */
    public function getFormatted () : string
    {
        $sections = [];

        if ("" !== $this->header)
        {
            $sections[] = $this->header;
        }

        foreach ($this->sections as $section)
        {
            $sections[] = $section->getFormatted();
        }

        if (!empty($this->sitemaps))
        {
            $sections[] = \implode("\n", $this->sitemaps);
        }

        return \implode("\n\n", $sections);
    }
}
