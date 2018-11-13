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


    public function testHeaderWithIndention ()
    {
        $builder = new RobotsTxtBuilder();
        $builder->setHeader(<<<EOT
First line
      second line
third line
EOT
        );

        $expected = <<<EOT
# First line
#       second line
# third line
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
Disallow: /admin

Sitemap: https://becklyn.com/sitemap.xml
Sitemap: https://becklyn.com/sitemap.xml.tar.gz
EOT;

        self::assertSame($expected, $builder->getFormatted());
    }


    public function testFull ()
    {
        $builder = new RobotsTxtBuilder();
        $builder->getSection("*")
            ->disallow("/admin")
            ->allow("/public")
            ->crawlDelay(10);

        $builder->getSection("google")
            ->disallow("/admin2")
            ->allow("/")
            ->crawlDelay(15);

        $builder->addSitemap("https://becklyn.com/sitemap.xml");
        $builder->addSitemap("https://becklyn.com/sitemap.xml.tar.gz");

        $expected = <<<EOT
User-Agent: *
Disallow: /admin
Allow: /public
Crawl-delay: 10

User-Agent: google
Disallow: /admin2
Allow: /
Crawl-delay: 15

Sitemap: https://becklyn.com/sitemap.xml
Sitemap: https://becklyn.com/sitemap.xml.tar.gz
EOT;

        self::assertSame($expected, $builder->getFormatted());
    }


    public function testFullWithStripping ()
    {
        $builder = new RobotsTxtBuilder();
        $builder->getSection("*  ")
            ->disallow("/admin  ")
            ->allow("/public  ")
            ->crawlDelay(10);

        $builder->addSitemap("https://becklyn.com/sitemap.xml  ");
        $builder->addSitemap("https://becklyn.com/sitemap.xml.tar.gz  ");

        $expected = <<<EOT
User-Agent: *
Disallow: /admin
Allow: /public
Crawl-delay: 10

Sitemap: https://becklyn.com/sitemap.xml
Sitemap: https://becklyn.com/sitemap.xml.tar.gz
EOT;

        self::assertSame($expected, $builder->getFormatted());
    }


    public function testSectionRemoval ()
    {
        $builder = new RobotsTxtBuilder();
        $builder->getSection("*");

        self::assertTrue("" !== $builder->getFormatted());
        $builder->removeSection("*");
        self::assertSame("", $builder->getFormatted());
    }
}
