# Blog Post Creator - Application Starter

## What is Blog Post Creator?

Blog post creator is a script built using Codeigniter4 PHP framework to help in the creation of blog posts.
AI content creation platforms are extremely popular but can also be expensive. 

This script should get you up and running with a self hosted platform that utilises [SerpAPI](https://serpapi.com) and [OpenAI](https://openai.com) to generate blog posts. 

SerpAPI is used to fetch and analyse Google search results and estimate blog post word count and also top performing results outlines. You get 100 searches a month free, or you can upgrade to a paid subscription for more searches. 

OpenAI is used to generate content using their AI content generation tool. This costs per query but is very cheap. You'll find you might only spend less than $10 a month and generate 1000s of words. Much cheaper than commercial platforms. 

There is also a content editor built into this script so you can draft your blog posts. It auto saves what you've written to your browser storage so you can come back to the draft at a later date. You'll need to sign up to [tiny.cloud](https://tiny.cloud) for an API key. 

## Installation & updates

First you need to sign up for the API services this script uses: [SerpAPI](https://serpapi.com), [OpenAI](https://openai.com) and [TinyMCE](https://tiny.cloud).

Clone the project ```git clone https://github.com/domaingenerator/blog-post-creator.git``` or simply download the Zip file from [Github](https://github.com/domaingenerator/blog-post-creator/). 

1. using the terminal, navigate into the scripts folder and run ```composer install```. This installs all the necessary packages. 

2. Rename the ```.env_example``` file to ```.env``` and open it up in your favourite editor and scroll to the end of the file. Update the variables with your API keys. 

3. Next open file ```app > config > App.php ``` and update the ```public $baseURL``` variable to reflect either the domain you will host the script or ```localhost:port-number```. 

If you are self hosting this with a domain, make sure you set the document root to the ```public``` folder of this script.

To run the project locally, simply run ```php spark serve``` or ```php -S localhost:3000 -t ./public```.


## Server Requirements

PHP version 7.4 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)
- xml (enabled by default - don't turn it off)

[A2hosting](https://www.a2hosting.com?aid=60f454f2127f1&bid=75dbf1c0) shared hosting runs this project perfectly. 

## todo
* Save search results to the SQLite database
* Ability to create a post outline from headings in results
* Save/load multiple blog post drafts to SQLite
* Function to send draft directly to Wordpress via email
* Make the design more mobile friendly

## Support
Open an issue on the Github or email me at tim@flipsnap.net.

## Contributions
Feel free to submit PRs or Fork the project to do something else. 

## If you want to help me
I'd really appreciate a link on your website/blog back to my other generator site [domaingenerator.app](https://domaingenerator.app)
