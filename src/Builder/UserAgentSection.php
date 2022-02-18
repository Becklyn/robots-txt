<?php declare(strict_types=1);

namespace Becklyn\RobotsTxt\Builder;

use Becklyn\RobotsTxt\Exception\InvalidPathException;

class UserAgentSection
{
    /** @var string[] */
    private array $userAgents;
    /** @var string[][] */
    private array $rules = [];
    /** @var string[] */
    private array $comments = [];


    /**
     * @param string[] $userAgents
     */
    public function __construct (array $userAgents)
    {
        $this->userAgents = $userAgents;
    }


    /**
     * Adds a "Disallow" directive
     *
     * @return UserAgentSection
     */
    public function disallow (string $path) : self
    {
        $path = \trim($path);

        if (!$this->isValidPath($path, true))
        {
            throw new InvalidPathException(\sprintf("Invalid path: '%s'. Disallow path must start with a slash '/'.", $path));
        }

        return $this->addRule("Disallow", $path);
    }


    /**
     * Adds a "Allow" directive
     *
     * @return UserAgentSection
     */
    public function allow (string $path) : self
    {
        $path = \trim($path);

        if (!$this->isValidPath($path, false))
        {
            throw new InvalidPathException(\sprintf("Invalid path: '%s'. Allow path must start with a slash '/' and must not be empty.", $path));
        }

        return $this->addRule("Allow", $path);
    }


    /**
     */
    private function isValidPath (string $path, bool $allowEmpty) : bool
    {
        if (false !== \strpos($path, "\n"))
        {
            return false;
        }

        return "" !== $path || $allowEmpty;
    }


    /**
     * Adds a "Crawl-delay" directive
     *
     * @return UserAgentSection
     */
    public function crawlDelay (int $delay) : self
    {
        return $this->addRule("Crawl-delay", (string) $delay);
    }


    /**
     * @return UserAgentSection
     */
    public function comment (string $comment) : self
    {
        $lines = \array_map("trim", \explode("\n", $comment));

        foreach ($lines as $line)
        {
            $this->comments[] = $line;
        }

        return $this;
    }


    /**
     * Adds a single rule
     *
     * @return UserAgentSection
     */
    private function addRule (string $type, string $rule) : self
    {
        $this->rules[$type][$rule] = true;
        return $this;
    }


    /**
     * Renders this section
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

        return \implode("\n", $lines);
    }


    /**
     */
    public function __toString () : string
    {
        return $this->getFormatted();
    }
}
