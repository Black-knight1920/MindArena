const page=document.querySelector('.page');
const slink=document.querySelector('.signIn');
const loglink=document.querySelector('.signUp');


loglink.addEventListener('click', () => {
    page.classList.add('anim-log');
    page.classList.remove('anim-up');
});


slink.addEventListener('click', () => {
    page.classList.add('anim-up');
    page.classList.remove('anim-log');
});


const params = new URLSearchParams(window.location.search);

if (params.has('error')) {
  const error = params.get('error');
  if (error === 'user_not_found') {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Utilisateur non disponible",
    }).then(() => {
      // Clean URL after alert is closed
      window.history.replaceState({}, document.title, window.location.pathname);
    });
  }
  else if(error === 'acces'){
    Swal.fire({
        title: "User Created",
        icon: "success",
        draggable: true
    }).then(() => {
      // Clean URL after alert is closed
      window.history.replaceState({}, document.title, window.location.pathname);
    });

  }
  else if(error=== 'e_utiliser'){
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Email Deja Utiliser",
    }).then(() => {
      // Clean URL after alert is closed
      window.history.replaceState({}, document.title, window.location.pathname);
    });
  }
  // handle other errors similarly...
}
