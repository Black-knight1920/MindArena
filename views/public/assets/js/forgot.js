const params = new URLSearchParams(window.location.search);

if (params.has('error')) {
  const error = params.get('error');
  if (error === 'email_not_found') {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Email non disponible",
    }).then(() => {
      // Clean URL after alert is closed
      window.history.replaceState({}, document.title, window.location.pathname);
    });
  }
  else if(error === 'link_sent'){
    Swal.fire({
        title: "Link Sent",
        icon: "success",
        draggable: true
    }).then(() => {
      // Clean URL after alert is closed
      window.history.replaceState({}, document.title, window.location.pathname);
    });

  }
  else if (error === 'none') {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Email non disponible",
    }).then(() => {
      // Clean URL after alert is closed
      window.history.replaceState({}, document.title, window.location.pathname);
    });
  }
  
  // handle other errors similarly...
}