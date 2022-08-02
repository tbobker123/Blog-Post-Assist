const Axios = require('axios');
const cheerio = require('cheerio');
const google = require('googlethis/lib/');

let string = "";

module.exports.seoreport =  async function(searchterm='simulated live streaming') {

const countWords = (str) => {
  const arr = str.split(' ');
  let count = arr.filter(word => word !== '').length;
  return Math.round(count * 1.1);
}

function getExtension(filename) {
  var parts = filename.split('.');
  return parts[parts.length - 1];
}
  
    // A simple search
    const res = await google.search(searchterm, {
      page: 0,
      safe: false,
      additional_params: {
        hl: 'en',
        num: 30
      }
    });

    const all_google_results = res.results;


  let results = [];
  let total_words = 0;
  let all_words = "";

  for(let x=0;x<all_google_results.length;x++)
  {
    if(getExtension(all_google_results[x].url) == 'pdf') continue;

    try {
      await Axios.get(all_google_results[x].url).then( result => {
        const $ = cheerio.load(result.data);
        let string = "";
        let obj = {
          words: 0,
          title: '',
          url: '',
          description: '',
          h1: [],
          h2: []
        }
        $('h1,h2,h3,h4,h5,p').map((index, item) => {
            
            string += " " + $(item).text();
            if(obj[$(item)[0].name]){
              if(Array.isArray(obj[$(item)[0].name])){
                obj[$(item)[0].name].push($(item).text().toLocaleLowerCase().replace(/\t/g, '').replace(/\n/g, '').replace(/<\/?[^>]+(>|$)/gi, ""))
              } else {
                obj[$(item)[0].name] = $(item).text().toLocaleLowerCase().replace(/\t/g, '').replace(/\n/g, '').replace(/<\/?[^>]+(>|$)/gi, "");
              }
            }
        });
        all_words += " " + string;

        let wordcount = countWords(string);

        if(wordcount > 5){
          total_words = total_words + wordcount;
          obj.words = wordcount;
          results.push(obj);
        }

        obj.url = all_google_results[x].url;
        obj.title = all_google_results[x].title;
        obj.description = all_google_results[x].description;
    })
    } catch (error) {
      
    }
    

  }
  const fs = require('fs');

  const content = all_words.replace(/^\s+|\s+$/g, '').replace(/(\r\n|\n|\r)/gm, "");

  fs.writeFile('content.txt',' ', function(){
    console.log("previous contents deleted");
  });

  fs.writeFile('content.txt', content , err => {
    if (err) {
      console.error(err);
    }
    console.log("Content written to file (content.txt)");
  });

  return {
    results: results,
    wordcount: (Math.round( (total_words/all_google_results.length) * 1.3 ))
  }
}


