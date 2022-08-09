const express = require("express");
const bodyParser = require("body-parser");
const session = require('express-session')
const http = require('http');
const path = require('path');
const { Server } = require("socket.io");
const { parse } = require("path");
const seo = require('./parse.js');
const blogContent = require('./openai.js');
require('dotenv').config();

const app = express();
const server = http.createServer(app);

app.use(session({
  secret:'Keep it secret',
  name:'uniqueSessionID',
  saveUninitialized:false
}))

app.use(express.json());
app.use(express.static(path.join(__dirname,'static')));

const io = new Server(server);

const PORT = process.env.PORT || 3000;

app.get('/styles', (req, res) => {
  res.sendFile(__dirname + '/styles.css');
});

app.get('/scripts', (req, res) => {
  res.sendFile(__dirname + '/scripts.js');
});

app.get('/', (req, res) => {
  if(req.session.loggedIn) {
    res.redirect('/dashboard')
  } else {
    res.sendFile(__dirname + '/index.html');
  }
});

app.get('/dashboard',(req,res)=>{
  if(req.session.loggedIn) {
    res.sendFile(__dirname + '/app.html');
  } else {
    res.redirect('/?loggedin=false');
  }
});


app.post('/login', function(req, res) {
  const username = req.body.username;
  const password = req.body.password;

  console.log(req.body)

  if(username && password) {

    if(username == process.env.USERNAME && password == process.env.PASSWORD) {
      req.session.loggedIn = true;
      req.session.username = username;
      //res.redirect('/dashboard');
      res.status(200).send("true");

    } else {
      //io.sockets.emit("warning", "Incorrect username and/or password supplied");
      res.status(200).send("Incorrect username and/or password supplied");
    }

    res.end();
  } else {
    //io.sockets.emit("warning", "Enter a username and password.");
    res.status(200).send("Enter a username and password.");
		res.end();
  }
})

app.get('/logout',(req,res)=>{
  req.session.destroy((err)=>{})
  res.redirect('/');
});

app.post('/search', async (req, res) => {
    const report = await seo.seoreport(req.body.term);
    io.sockets.emit("results", JSON.stringify(report));
    res.status(200).end();
});

app.post('/generate', async (req, res) => {
  const prompt = req.body.prompt;
  const content = blogContent.generateBlogContent(prompt).then(result => {
    io.sockets.emit("generated", result);
  });
  res.status(200).end();
});


server.listen(PORT);

module.exports = app;
