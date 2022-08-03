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

app.use(session({secret:'Keep it secret'
,name:'uniqueSessionID'
,saveUninitialized:false}))

app.use(express.json());
app.use(express.static(path.join(__dirname,'static')));

const io = new Server(server);

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
    res.redirect('/');
  }
});

app.post('/login',bodyParser.urlencoded(),(req,res,next)=>{
  if(req.body.username==process.env.USERNAME && req.body.password==process.env.PASSWORD){
    res.locals.username = req.body.username
    next();
  } else {
    res.redirect('/');
  }
} ,(req,res)=> {
  req.session.loggedIn = true
  req.session.username = res.locals.username
  res.redirect('/dashboard');
});

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


server.listen();

module.exports = app;
