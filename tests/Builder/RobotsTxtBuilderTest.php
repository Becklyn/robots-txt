<?php declare(strict_types=1);

namespace Tests\Becklyn\RobotsTxt\Builder;

use Becklyn\RobotsTxt\Builder\RobotsTxtBuilder;
use PHPUnit\Framework\TestCase;


class RobotsTxtBuilderTest extends TestCase
{

    public function testHeader ()
    {
        $builder = new RobotsTxtBuilder();
        $builder->setHeader("Header line 1\nHeader line 2");

        $expected = <<<EOT
# Header line 1
# Header line 2
EOT;


        self::assertSame($expected, $builder->getFormatted());
    }


    public function testDuplicateSection ()
    {
        $builder = new RobotsTxtBuilder();
        $section1 = $builder->getSection("*");
        $section2 = $builder->getSection("*");

        self::assertSame($section1, $section2);
    }

    public function testDuplicateSectionUnordered ()
    {
        $builder = new RobotsTxtBuilder();
        $section1 = $builder->getSection("google", "bing");
        $section2 = $builder->getSection("bing", "google");

        self::assertSame($section1, $section2);
    }

    public function testSitemap ()
    {
        $builder = new RobotsTxtBuilder();
        $builder->getSection("*")
            ->disallow("/admin");
        $builder->addSitemap("https://becklyn.com/sitemap.xml");
        $builder->addSitemap("https://becklyn.com/sitemap.xml.tar.gz");

        $expected = <<<EOT
User-Agent: *
Disallow: /

Sitemap: https://becklyn.com/sitemap.xml 
Sitemap: https://becklyn.com/sitemap.xml.tar.gz
EOT;

    }


    public function testFull ()
    {

    }
}
