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

import $ from 'jquery';
import sal from 'sal.js';
import axios from 'axios';
import Dropzone from 'dropzone';


/*
 * Body : Show/hide side menu
 */

$('#sidebar-btn, #mobile-nav-btn').click(function(){
    $('nav').css('right', '0px');
});

$('#close-nav').click(function(){
    $('nav').css('right', '-' + $('nav').width() + 'px');
});


/*
 * Body : Change navbar color on scroll
 */

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
 * Home Page : Scroll animation on trick cards
 */

const sal_animation = sal();


/*
 * Show Tricks Page : Show/Hide responsive menu
 */

$('#show-media').click(function(){
    $('#media').slideToggle();
    if($(this).hasClass('btn-blue'))
    {
        $(this).removeClass('btn-blue');
        $(this).addClass('btn-red');
        $(this).text('Masquer les médias');
    }
    else
    {
        $(this).removeClass('btn-red');
        $(this).addClass('btn-blue');
        $(this).text('Afficher les médias');
    }
});


/*
 * Home Page : Load more tricks
 */

let firstItem = 0;
let nbItems = 5;

$('#load-more').click(function(){
    event.preventDefault();

    $(this).removeClass('bg-blue-400');
    $(this).addClass('bg-blue-800');
    $(this).html('<i class="fas fa-spinner animate-spin mr-2"></i> Loading');

    firstItem += 5;

    axios.get('/load/' + firstItem + '/' + nbItems)
    .then(function(response){
        console.log(response.data);
        response.data.forEach(function(trick){
            let target = document.getElementById('tricks-container');

            let child = document.createElement('div');
            child.classList.add('trick-card');
            child.classList.add('sal-animate');
            child.setAttribute('data-sal', 'slide-up');
            child.setAttribute('data-sal-delay', '100');
            child.setAttribute('data-sal-duration', '600');
            child.setAttribute('data-sal-easing', 'ease-out-back');
            child.setAttribute('data-sal-repeat', '');

            if(trick.difficulty == 1)
            {
                child.innerHTML = '' +
                    '<figure class="w-full h-48 mb-5 overflow-hidden">\n' +
                    '<img class="object-cover min-h-full" src="/upload/japan-air-1.jpg" alt="">\n' +
                    '</figure>\n' +
                    '<a href="/show/' + trick.id + '"><h6 class="text-xl text-center font-bold px-8 mb-2">' + trick.name + ' ' + trick.category.name + '</h6></a>\n' +
                    '<small class="block px-8 mb-4 text-center">\n'+
                    '<i class="fas fa-star text-blue-400"></i><i class="far fa-star text-blue-400"></i><i class="far fa-star text-blue-400"></i>' +
                    '</small>';
            }else if(trick.difficulty == 2)
            {
                child.innerHTML = '' +
                    '<figure class="w-full h-48 mb-5 overflow-hidden">\n' +
                    '<img class="object-cover min-h-full" src="/upload/japan-air-1.jpg" alt="">\n' +
                    '</figure>\n' +
                    '<a href="/show/' + trick.id + '"><h6 class="text-xl text-center font-bold px-8 mb-2">' + trick.name + ' ' + trick.category.name + '</h6></a>\n' +
                    '<small class="block px-8 mb-4 text-center">\n' +
                    '<i class="fas fa-star text-blue-400"></i><i class="fas fa-star text-blue-400"></i><i class="far fa-star text-blue-400"></i>' +
                    '</small>';
            }else if(trick.difficulty == 3) {
                child.innerHTML = '' +
                    '<figure class="w-full h-48 mb-5 overflow-hidden">\n' +
                    '<img class="object-cover min-h-full" src="/upload/japan-air-1.jpg" alt="">\n' +
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
        sal_animation.reset({
            threshold: 0.1
        });

        $('#load-more').removeClass('bg-blue-800');
        $('#load-more').addClass('bg-blue-400');
        $('#load-more').html('Load more tricks');
    });
});


/*
 * Create Tricks Page : Add videos row
 */

$(document).ready(function () {
    $('#add-video-widget').click(function (e) {
        console.log(this);
        var list = $(jQuery(this).attr('data-list-selector'));
        var counter = list.data('widget-counter') || list.children().length;

        var newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data('widget-counter', counter);

        var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);
    });
});


/*
 * Create Tricks Page : media dropzone
 */
if($("#media_dropzone").length > 0)
{
    Dropzone.autoDiscover = false;

    let media_dropzone = new Dropzone("#media_dropzone", {
        url: "/create/tricks",
        dictDefaultMessage: 'Drag and drop your files',
        addRemoveLinks: true,
        dictRemoveFile: '&times;',
        maxFiles: 3,
        dictMaxFilesExceeded: 'Ce fichier ne sera pas uploadé'
    });

    media_dropzone.on("addedfile", function(file) {
    });
}