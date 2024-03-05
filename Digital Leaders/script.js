const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    const square = entry.target.querySelector(".non-scroll");

    if (entry.isIntersecting) {
      square.classList.add('scrollImg');
	  return; // if we added the class, exit the function
    }

    // We're not intersecting, so remove the class!
    square.classList.remove('scrollImg');
  });
});

observer.observe(document.querySelector(".IMAGE"));