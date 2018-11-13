<?php declare(strict_types=1);

namespace Becklyn\RobotsTxt\Builder;

use Becklyn\RobotsTxt\Builder\UserAgentSection;


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
    public function __construct (string $header = "")
    {
        $this->setHeader($header);
    }


    /**
     * @param string $header
     */
    public function setHeader (string $header) : void
    {
        $this->header = \trim($header);
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
        $this->sitemaps[] = "Sitemap: {$url}";
    }


    /**
     * Renders the robots.txt to a string
     *
     * @return string
     */
    public function getContent () : string
    {
        $content = "";

        if ("" !== $this->header)
        {
            $content .= "{$this->header}\n\n";
        }

        $content .= implode("\n\n", array_map(
            function (UserAgentSection $section)
            {
                return (string) $section;
            },
            $this->sections
        ));

        return $content;
    }
}
