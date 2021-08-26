"use strict";
const navigationHamburger = document.querySelector('.navigation__hamburger-container');
const navigation = document.querySelector('.navigation-end');
navigationHamburger.addEventListener('click', () => {
  navigation.classList.toggle('active');
  navigationHamburger.classList.toggle('active');
  navigation.style.height = navigation.classList.contains('active') ? calculateDropDownHeight() : '0px';
});

function calculateDropDownHeight() {
  const navbarLinks = document.querySelectorAll('.navigation-end__link');
  const navbarButton = document.querySelector('.navigation-end__link.button');
  const height = navbarLinks[0].offsetHeight * (navbarLinks.length - 1) + navbarButton.offsetHeight + 20;
  return height + 'px';
}

// define a function to disable zooming
var zoomDisable = function () {
  let objHead = document.querySelector('head');
  objHead.querySelector('meta[name=viewport]').remove();
  let meta = document.createElement('meta');
  meta.name = 'viewport';
  meta.content = 'width=device-width, initial-scale=1.0, user-scalable=0'
  objHead.prepend(meta);
};

// ... and another to re-enable it
var zoomEnable = function () {
  let objHead = document.querySelector('head');
  objHead.querySelector('meta[name=viewport]').remove();
  let meta = document.createElement('meta');
  meta.name = 'viewport';
  meta.content = 'width=device-width, initial-scale=1.0, user-scalable=1';
  objHead.prepend(meta);
};

// if the device is an iProduct, apply the fix whenever the users touches an input
if (navigator.userAgent.length && /iPhone|iPad|iPod/i.test(navigator.userAgent)) {

  let inputFields = document.querySelectorAll("input, textarea");
  for (let inputField of inputFields) {
    inputField.addEventListener('touchstart', zoomDisable);
    inputField.addEventListener('touchend', () => { setTimeout(zoomEnable, 500) })
  }
}




if (window.location.href.indexOf('add_movie') !== -1) {

  const inputs = document.querySelectorAll('.input, .textarea');
  toggleErrorMessageOnInputFields(inputs);

  const fileUpload = document.querySelector('.file-input');
  fileUpload.addEventListener('change', showImagePreview);

  const textarea = document.querySelector('.textarea');
  textarea.addEventListener('input', handleTextareaHeight);
  textarea.addEventListener('keyup', setTextareaHeightToInitial);

  const form = document.querySelector('form');
  form.addEventListener('submit', event => {
    event.preventDefault();
    let formData = createFormDataObject(form);
    postData('./include/add_movie.inc.php', formData)
    .then(response => {
      if(!response) {
        clearInputFields();
        textarea.style.height = '8rem';
        removeImage();
        showSuccessMessage('Successfully saved');
      } else {
        let errorMessages = document.querySelectorAll('.message-is-danger');
        for (let errorMessage of errorMessages) {
          errorMessage.innerHTML = '';
        }

        for (const [key, value] of Object.entries(response)) {
          let field = document.querySelector(`input[name="${key}"]`);
          field.parentElement.querySelector('.message-is-danger').innerHTML = value;
        }
      }
    })
  })

  function setImage(inputField, img) {
    img.src = URL.createObjectURL(inputField.files[0]);
    img.onload = function () {
      URL.revokeObjectURL(img.src);
    }
  }

  function showImagePreview(event) {
    const container = event.target.parentElement;
    let img = container.querySelector('img');
    let error = container.querySelector('.message-is-danger');
    error.innerHTML = '';
    setImage(this, img);
  }

  function handleTextareaHeight() {
    if (textarea.scrollHeight > 80) {
      textarea.style.height = textarea.scrollHeight + 'px';
    }
  }

  function setTextareaHeightToInitial(event) {
    if ((event.keyCode == 8 || event.keyCode == 46)) {
      if (textarea.value.length < 1) {
        textarea.style.height = '8rem';
      } else {
        handleTextareaHeight();
      }
    }
  }

  function clearInputFields() {
    let inputs = document.querySelectorAll('input, textarea');
    for(let input of inputs) {
      input.value = '';
    }
  }
  
  function removeImage() {
    let image = document.querySelector('.file-preview');
    image.src = '';
  }

  function showSuccessMessage(message) {
    let messageContainer = document.querySelector('.message-success');
    messageContainer.classList.add('active');
    messageContainer.innerHTML = message;
    setTimeout(() => {
      messageContainer.classList.remove('active');
      messageContainer.innerHTML = '';
    }, 800)
  }

  function isInputEmptyOnSubmit(inputs, spans) {
    let error = [];
    for (let i = 0; i < inputs.length; i++) {
      if (inputs[i].value == '') {
        spans[i].innerHTML = 'Field can\'t be empty';
        error.push('error');
      } else {
        spans[i].innerHTML = '';
      }
    }
    return error.length > 0 ? error : '';
  }



  function isInputEmptyOnChange() {
    let container = this.parentElement;
    let span = container.querySelector('.message-is-danger');
    if (this.value == '' && this == document.activeElement) {
      span.innerHTML = 'Field can\'t be empty';
    } else {
      span.innerHTML = '';
    }
  }

  function removeErrorMessage() {
    let container = this.parentElement;
    let span = container.querySelector('.message-is-danger');
    span.innerHTML = '';
  }

  function toggleErrorMessageOnInputFields(inputs) {
    for (let input of inputs) {
      input.addEventListener('input', isInputEmptyOnChange);
      input.addEventListener('focusout', removeErrorMessage);
    }
  }

} else if(window.location.href.indexOf('movie/') !== -1) {
  const singleMovieContainer = document.getElementById('single-movie');
  const id = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
  const singleMovieUrl = './include/get_movie_details.inc.php?id=' + id;
  getData(singleMovieUrl)
  .then(response => {
    if(response['error']) {
      singleMovieContainer.innerHTML = '<h1 class="has-text-centered"> Page not found. </h1>';
    } else {
      singleMovieContainer.innerHTML = renderMovieDetails(response);
    }
  })

  function renderMovieDetails(movie) {
    return `<div class="single-movie__image">
              ${movieHasImage(movie['image'], 'width-100')}
          </div>
          <div class="single-movie__desc">
              <h1 class="heading-main">${movie['name']}</h1>
              <pre class="single-movie__text">${movie['description']}</pre>
          </div>`;
  }

} else {

  var moviesContainer = document.getElementById('movies');
  var paginationContainer = document.querySelector('.pagination');
  let currentPage = 1;
  const movieUrl = './include/get_movies.inc.php?id=1&search=';
  let moviesAdded = false;
  getData(movieUrl)
  .then(response => {
    if(response['movies'] && response['movies'].length > 0) {
      moviesContainer.innerHTML = renderMovies(response['movies']);
      paginationContainer.innerHTML = renderPagination(response['numberOfPages']);
      moviesAdded = true;
    } else {
      moviesContainer.innerHTML = '<h2 class="movie__heading-empty">No movies added...</h2>';
    }
  });
  const search = document.getElementById('search');
  search.addEventListener('input', onInput);

  document.addEventListener('click', e => {
    if (e.target.classList.contains('pagination-item') && !e.target.classList.contains('disabled')) {
      let id = e.target.getAttribute('data-id');
      currentPage = parseInt(id);
      const url = './include/get_movies.inc.php?id=' + id + '&search=' + search.value + '&paginate=true';
      getData(url)
        .then(response => {
          if (response['movies'] && response['movies'].length > 0) {
            moviesContainer.innerHTML = renderMovies(response['movies']);
            paginationContainer.innerHTML = renderPagination(response['numberOfPages']);
          } else {
            moviesContainer.innerHTML = '<h2 class="movie__heading-empty">No movies added...</h2>';
          }
      });
    }
  });


  function onInput (event) {
    currentPage = 1;
    const url = './include/get_movies.inc.php?id=' + currentPage + '&search=' + event.target.value;
    getData(url)
    .then(response => {
      if (response['movies'] && response['movies'].length > 0) {
        moviesContainer.innerHTML = renderMovies(response['movies']);
        paginationContainer.innerHTML = renderPagination(response['numberOfPages']);
      } else {
        moviesContainer.innerHTML = '<h2 class="movie__heading-empty">No movies found...</h2>';
        paginationContainer.innerHTML = renderPagination(response['numberOfPages']);
      }
    });
  }

  function renderMovie(movie) {
    return `<a href="./movie/${movie['id']}" class="movie">
            <div class="movie__image-container">
                ${movieHasImage(movie['image'], 'movie__image')}
            </div>
            <h2 class="movie__heading">${movie['name']}</h2>
        </a>`;
  }

  function renderMovies(movies) {
    let div = '';
    for(let movie of movies) {
      div += renderMovie(movie);
    }
    return div;
  }

  function renderPagination(numberOfPages) {
    let div = '';
    if(numberOfPages > 1) {
      paginationContainer.classList.add('pb-3');
      div += `<div class="pagination-item ${currentPage - 1 > 0 ? '' : 'disabled'}" data-id=${currentPage - 1 > 0 ? currentPage - 1 : 1}>Prev</div>`;
      for(let i = 0; i < numberOfPages; i++) {
        let page = i + 1;
        div += `<div class="pagination-item ${currentPage == page ? 'active' : ''}" data-id=${page}>${page}</div>`;
      }
      div += `<div class="pagination-item ${currentPage + 1 > numberOfPages ? 'disabled' : ''}" data-id=${currentPage + 1 > numberOfPages ? numberOfPages : currentPage + 1}>Next</div>`;
    } else {
      currentPage = 1;
    }
    return div;
  }
}

function movieHasImage(image, cssClass) {
  return image !== '' ? `<img src="./images/${image}" alt="" class="${cssClass}">` : '<i class="fas fa-film movie__icon"></i>';
}

async function postData(url, data) {
  const response = await fetch(url, {
    method: 'POST',
    body: data 
  });
  return response.json();
}

async function getData(url) {
  const response = await fetch(url, {
    method: 'GET',
  });
  return response.json();
}

function createFormDataObject(form) {
  let formData = new FormData(form);
  formData.append('submit', '');
  return formData;
}