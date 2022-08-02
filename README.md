# blog-post-creator

Dont waste money on these AI content generator tools, they just use OpenAI anyway which is so much cheaper in comparison. 
This tool you can self host and start to generate content just like those $50 a month tools. 

# Features
Here are some of the great features that have been built into this tool

## Search engine page analysis
See the top search results for a keyword and analyse the word count, post outlines and meta description. The tool also calculates the recommended word count for a blog post targeting the keyword

## Generate content
Using OpenAI, you can simple write commands into the input box that will query the OpenAI platform and send back results. For example, do you need to generate some blog title ideas, simple input "Generate blog topics on building backlinks for your website" and the tool will return ideas. 

## Self host the tool
No need for any fancy hosting, just use cpanel nodejs hosting. Most cpanel hosting includes Nodejs, just upload the files, add your environment variables and away you go. 

# Requirements
 - OpenAI account. You can use the free $17 trial and then upgrade to a pay as you go.
 - Web hosting. A2hosting shared hosting is recommended and has been tested. 

# Installation

1. Download the files from the Github repository
2. If you want to install this on a sub domain, create a sub domain now
3. Click on the "Setup Node.js App" section in Cpanel
4. Click the "create new application" button to setup a new app
5. Make sure the application root is the same as for your domain/sub domain and select the domain you want to use
6. Add the environment variables: USERNAME (dashboard username), PASSWORD (dashboard password), KEY (OpenAI API key)
7. Upload all the files to the document root
8. Head back to your Node.js App and click the "Run NPM Install". This installs all the packages
9. Navigate to the domain/subdomin in your browser. You should be able to login and start using the tool. 

# Restrictions
 - This doesn't run in a sub directory. 
 - You cannot run SERP analysis for multiple geo locations. It only works locally. 

# Issues/suggestions
Please open an issue on Github if you have any problems or if you have an suggestions

# Support this project
The best way to support this project is to either contribute or add our link to your website [domaingenerator.app](https://domaingenerator.app)

# contact
Website: [flipsnap.net](https://flipsnap.net), [domaingenerator.app](https://domaingenerator.app)
Email tim@flipsnap.net