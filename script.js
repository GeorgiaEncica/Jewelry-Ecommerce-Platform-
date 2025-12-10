const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("navLinks");

hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("open"); // animate X
  navLinks.classList.toggle("active"); // show/hide links
});


let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

function showSlides() {
 
  slides.forEach((slide, i) => {
    slide.classList.remove('active');
    dots[i].classList.remove('active');
  });

  
  slides[slideIndex].classList.add('active');
  dots[slideIndex].classList.add('active');


  slideIndex = (slideIndex + 1) % slides.length;
}


showSlides();
setInterval(showSlides, 4000);


let index2 = 0;
const slide2Images = document.querySelectorAll('.slide2 img');

function showSlide2() {
  // Hide all
  slide2Images.forEach(img => img.classList.remove('active'));

  // Show current
  slide2Images[index2].classList.add('active');

  // Move to next image
  index2 = (index2 + 1) % slide2Images.length;
}

// Start slideshow
showSlide2();
setInterval(showSlide2, 4000);


//collapsible content
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}





