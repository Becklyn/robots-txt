<?php declare(strict_types=1);

namespace Tests\Becklyn\RobotsTxt\Builder;

use Becklyn\RobotsTxt\Builder\UserAgentSection;
use PHPUnit\Framework\TestCase;


class UserAgentSectionTest extends TestCase
{
    public function provideUserAgents () : array
    {
        return [
            [
                ["google", "test"],
                "User-Agent: google\nUser-Agent: test",
            ],
            [
                ["*"],
                "User-Agent: *",
            ],
        ];
    }

    /**
     * @dataProvider provideUserAgents
     */
    public function testUserAgents (array $input, string $expectedOutput) : void
    {
        $section = new UserAgentSection($input);
        self::assertSame($expectedOutput, $section->getFormatted());
    }


    public function testRules ()
    {
        $section = new UserAgentSection(["*"]);
        $section->disallow("/");
        $section->allow("/files/");
        $section->crawlDelay(15);
        $section->allow("/downloads/");
        $section->comment("My comment");

        $expected = <<<EOS
# My comment
User-Agent: *
Disallow: /
Allow: /files/
Allow: /downloads/
Crawl-delay: 15
EOS;

        self::assertSame($expected, $section->getFormatted());
    }


    public function testDuplicateRules ()
    {
        $section = new UserAgentSection(["*"]);
        $section->disallow("/");
        $section->disallow("/");

        $expected = <<<EOS
User-Agent: *
Disallow: /
EOS;

        self::assertSame($expected, $section->getFormatted());
    }


    public function testFormats ()
    {

        $section = new UserAgentSection(["*", "mybot/1.0"]);
        $section->disallow("/");
        $section->disallow("");

        $expected = <<<EOS
User-Agent: *
User-Agent: mybot/1.0
Disallow: /
Disallow: 
EOS;

        self::assertSame($expected, $section->getFormatted());
    }


    /**
     * @expectedException Becklyn\RobotsTxt\Exception\InvalidPathException
     */
    public function testInvalidDisallowPath ()
    {
        (new UserAgentSection(["*"]))
            ->disallow("invalid");
    }


    /**
     * @expectedException Becklyn\RobotsTxt\Exception\InvalidPathException
     */
    public function testInvalidAllowPath ()
    {
        (new UserAgentSection(["*"]))
            ->allow("invalid");
    }


    /**
     * @expectedException Becklyn\RobotsTxt\Exception\InvalidPathException
     */
    public function testInvalidEmptyAllowPath ()
    {
        (new UserAgentSection(["*"]))
            ->allow("");
    }
}