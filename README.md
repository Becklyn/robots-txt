Robots.txt Library
==================


Usage
-----

First, you need to create a builder. With this builder you can create sections for different user agents and add the directives to it.

You can also add a header to the robots.txt and register your sitemap URLs.


### Adding Sections

```php
use Becklyn\RobotsTxt\Builder\RobotsTxtBuilder;

$builder = new RobotsTxtBuilder();

// adding a section
$builder->getSection("google")
    ->allow("/public")
    ->disallow("/admin")
    ->crawlDelay(20);
    
$builder->getSection("bing")
    ->allow("/public")
    ->disallow("/admin")
    ->disallow("/private")
    ->crawlDelay(15);
```
    
If multiple search engines have the same directives, you can add one section for all of them:
```php
$builder->getSection("google", "bing")
    ->allow("/public")
    ->disallow("/admin")
    ->crawlDelay(20);
```    
    
The builder tries to bundle combine the directives of multiple sections if possible:

```php
$builder->getSection("google")
    ->allow("/public");
    
// ... some code ...

$builder->getSection("google")
    ->disallow("/admin")
    
// will produce a single entry:
//
//      User-Agent: google
//      Allow: /public
//      Disallow: /admin
```

### Sitemaps

```php
$builder
    ->addSitemap("https://example.org/sitemap.xml.tar.gz")
    ->addSitemap("https://example.org/sitemap.xml");
```

### Header

You can also add a header which will be included at the very top:

```php
$builder
    ->setHeader("This is some example text");
    
$builder->getSection("google")
    ->allow("/public");
    
// Will produce:
//
//      # This is some example text
//
//      User-Agent: google
//      Allow: /public
```

### Outputting the robots.txt

```php
$content = $builder->getFormatted();
file_put_contents("robots.txt", $content);
```
