<?php declare(strict_types=1);

namespace Becklyn\RobotsTxt\Builder;

use Becklyn\RobotsTxt\Builder\UserAgentSection;
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
     */
    public function setHeader (string $header) : void
    {
        $lines = \array_map("trim", \explode("\n", $header));
        $this->header = "# " . \implode("\n# ", $lines);
    }


    /**
     * @param string[] ...$userAgents
     * @return UserAgentSection
     */
    public function getSection (...$userAgents) : UserAgentSection
    {
        $userAgents = array_map("trim", $userAgents);
        \natsort($userAgents);
        $hashKey = \implode(":", $userAgents);

        if (!isset($this->sections[$hashKey]))
        {
            $this->sections[$hashKey] = new UserAgentSection($userAgents);
        }

        return $this->sections[$hashKey];
    }


    /**
     * @param string $url
     * @return RobotsTxtBuilder
     */
    public function addSitemap (string $url) : void
    {
        $url = \trim($url);

        if (!$this->isValidUrl($url))
        {
            throw new InvalidSitemapUrlException(\sprintf("Invalid sitemap URL: '%s'. The URL must not contain line breaks.", $url));
        }

        $this->sitemaps[] = "Sitemap: {$url}";
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
