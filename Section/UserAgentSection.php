<?php declare(strict_types=1);

namespace Becklyn\RobotsTxt\Section;

use Becklyn\RobotsTxt\Exception\InvalidPathException;


class UserAgentSection
{
    /**
     * @var string[]
     */
    private $userAgents;


    /**
     * @var string[][]
     */
    private $rules = [];


    /**
     * @var string[]
     */
    private $comments = [];


    /**
     *
     * @param string[] $userAgents
     */
    public function __construct (array $userAgents)
    {
        $this->userAgents = $userAgents;
    }


    /**
     * Adds a "Disallow" directive
     *
     * @param string $path
     * @return UserAgentSection
     */
    public function disallow (string $path) : self
    {
        if (!$this->isValidPath($path, true))
        {
            throw new InvalidPathException(\sprintf("Invalid path: '%s'. Disallow path must start with a slash '/'.", $path));
        }

        return $this->addRule("Disallow", $path);
    }


    /**
     * Adds a "Allow" directive
     *
     * @param string $path
     * @return UserAgentSection
     */
    public function allow (string $path) : self
    {
        if (!$this->isValidPath($path, false))
        {
            throw new InvalidPathException(\sprintf("Invalid path: '%s'. Allow path must start with a slash '/' and must not be empty.", $path));
        }

        return $this->addRule("Allow", $path);
    }


    /**
     * @param string $path
     * @param bool   $allowEmpty
     * @return bool
     */
    private function isValidPath (string $path, bool $allowEmpty) : bool
    {
        return "" !== $path
            ? "/" === $path[0]
            : $allowEmpty;
    }


    /**
     * Adds a "Crawl-delay" directive
     *
     * @param int $delay
     * @return UserAgentSection
     */
    public function crawlDelay (int $delay) : self
    {
        return $this->addRule("Crawl-delay", (string) $delay);
    }


    /**
     * @param string $comment
     * @return UserAgentSection
     */
    public function comment (string $comment) : self
    {
        $this->comments[] = $comment;
        return $this;
    }


    /**
     * Adds a single rule
     *
     * @param string $type
     * @param string $rule
     * @return UserAgentSection
     */
    private function addRule (string $type, string $rule) : self
    {
        $this->rules[$type][$rule] = true;
        return $this;
    }


    /**
     * Renders this section
     *
     * @return string
     */
    public function getFormatted () : string
    {
        $lines = [];

        foreach ($this->comments as $comment)
        {
            $lines[] = "# {$comment}";
        }

        foreach ($this->userAgents as $agent)
        {
            $lines[] = "User-Agent: {$agent}";
        }

        foreach ($this->rules as $type => $rules)
        {
            foreach ($rules as $rule => $isActive)
            {
                $lines[] = "{$type}: {$rule}";
            }
        }

        return implode("\n", $lines);
    }


    /**
     * @return string
     */
    public function __toString () : string
    {
        return $this->getFormatted();
    }
}
