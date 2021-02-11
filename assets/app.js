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
 * Body : Change navbar color on scroll
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
let nbItems = 5;

document.getElementById('load-more').addEventListener('click', function(){
    event.preventDefault();

    this.classList.remove('bg-blue-400');
    this.classList.add('bg-blue-800');
    this.innerHTML = "<i class=\"fas fa-spinner animate-spin mr-2\"></i> Loading";

    firstItem += 5;

    axios.get('/load/' + firstItem + '/' + nbItems)
    .then(function(response){
        response.data.forEach(function(trick){
            let target = document.getElementById('tricks-container');

            let child = document.createElement('div');
            child.classList.add('trick-card');

            if(trick.difficulty == 1)
            {
                child.innerHTML = '' +
                    '<figure class="w-full h-48 mb-5 overflow-hidden">\n' +
                    '<img class="object-cover min-h-full" src="/media/images/japan-air-1.jpg" alt="">\n' +
                    '</figure>\n' +
                    '<a href="/show/' + trick.id + '"><h6 class="text-xl text-center font-bold px-8 mb-2">' + trick.name + ' ' + trick.category.name + '</h6></a>\n' +
                    '<small class="block px-8 mb-4 text-center">\n'+
                        '<i class="fas fa-star text-blue-400"></i><i class="far fa-star text-blue-400"></i><i class="far fa-star text-blue-400"></i>' +
                    '</small>';
            }else if(trick.difficulty == 2)
            {
                child.innerHTML = '' +
                    '<figure class="w-full h-48 mb-5 overflow-hidden">\n' +
                    '<img class="object-cover min-h-full" src="/media/images/japan-air-1.jpg" alt="">\n' +
                    '</figure>\n' +
                    '<a href="/show/' + trick.id + '"><h6 class="text-xl text-center font-bold px-8 mb-2">' + trick.name + ' ' + trick.category.name + '</h6></a>\n' +
                    '<small class="block px-8 mb-4 text-center">\n' +
                        '<i class="fas fa-star text-blue-400"></i><i class="fas fa-star text-blue-400"></i><i class="far fa-star text-blue-400"></i>' +
                    '</small>';
            }else if(trick.difficulty == 3) {
                child.innerHTML = '' +
                    '<figure class="w-full h-48 mb-5 overflow-hidden">\n' +
                    '<img class="object-cover min-h-full" src="/media/images/japan-air-1.jpg" alt="">\n' +
                    '</figure>\n' +
                    '<a href="/show/' + trick.id + '"><h6 class="text-xl text-center font-bold px-8 mb-2">' + trick.name + ' ' + trick.category.name + '</h6></a>\n' +
                    '<small class="block px-8 mb-4 text-center">\n' +
                        '<i class="fas fa-star text-blue-400"></i><i class="fas fa-star text-blue-400"></i><i class="fas fa-star text-blue-400"></i>' +
                    '</small>';
            }

            target.appendChild(child);
        })
    })
    .then(function(){
        document.getElementById('load-more').classList.remove('bg-blue-800');
        document.getElementById('load-more').classList.add('bg-blue-400');
        document.getElementById('load-more').innerHTML = "Load more tricks";
    });
});