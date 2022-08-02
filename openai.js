const { Configuration, OpenAIApi } = require("openai");
require('dotenv').config();
const configuration = new Configuration({
  apiKey: process.env.OPENAI_KEY
});
const openai = new OpenAIApi(configuration);

module.exports.generateBlogContent = async function (prompt){
  console.log('start openai generator..')
  const completion = await openai.createCompletion({
        model: "davinci-instruct-beta-v3",
        prompt: prompt,
        temperature: 0.7,
        max_tokens: 800,
        top_p: 1,
        n: 3,
        frequency_penalty: 0,
        presence_penalty: 0
      });
    return completion.data.choices[0].text;
}







