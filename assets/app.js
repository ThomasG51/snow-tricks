/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';


/*
 * Home Page : Change navbar color on scroll
 */

document.getElementById('login-btn').addEventListener('click', function(){
    document.querySelector('nav').style.right = "0px";
});

document.getElementById('register-btn').addEventListener('click', function(){
    document.querySelector('nav').style.right = "0px";
});

document.getElementById('nav-btn').addEventListener('click', function(){
    document.querySelector('nav').style.right = "0px";
});

document.getElementById('close-nav').addEventListener('click', function(){
    let nav_width = document.querySelector('nav').offsetWidth;
    document.querySelector('nav').style.right = "-" + nav_width + "px";
});

window.addEventListener('scroll', function() {
    if(window.scrollY > 50)
    {
        document.getElementById('topbar').classList.add('bg-black', 'bg-opacity-75');
    }
    else if(window.scrollY < 50)
    {
        document.getElementById('topbar').classList.remove('bg-black', 'bg-opacity-75');
    }
})


/*
 * Home Page : Load more tricks
 */

import axios from "axios";

let firstItem = 0;
let nbItems = 2;

axios.post('/load/' + firstItem + '/' + nbItems).then(function(response){
    console.log(response.data);
});

document.getElementById('load-more').addEventListener('click', function(){
    event.preventDefault();

    firstItem += 2;

    axios.get('/load/' + firstItem + '/' + nbItems).then(function(response){
        console.log(response.data);
    });
});