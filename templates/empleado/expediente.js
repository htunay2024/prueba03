const firebaseConfig = {
    apiKey: "AIzaSyBPN23r-iNYnqa_qwDPgDie_-2OnHlRRsM",
    authDomain: "nomina-consulting.firebaseapp.com",
    projectId: "nomina-consulting",
    storageBucket: "nomina-consulting.appspot.com",
    messagingSenderId: "400850086680",
    appId: "1:400850086680:web:e77a82fed0f501f15acc6d",
    measurementId: "G-9K7XS0KX79"
  };
  
  // Inicializa Firebase
  const app = firebase.initializeApp(firebaseConfig);
  const storage = firebase.storage();  // Initialize Firebase Storage
  
  function irAlInicio(){
    console.log("Usuario ha iniciado sesión:");
  }
  
  function loginpdf() {
    var emailtv = "storagenominaconsulting@gmail.com";
    var passwordtv = "Nomina-Consulting2024";
  
    if (!validateEmail(emailtv)) {
        alert('Por favor, ingresa un correo electrónico válido.');
        return;
    } else if (passwordtv.trim() === '') {
        alert('Por favor, ingresa una contraseña.');
        return;
    } else if(passwordtv.length < 7){
        alert('La contraseña por lo menos debe de tener 7 caracteres');
        return;
    } else {
      verifyLoginPDF(emailtv, passwordtv);
    }
  }
  
  function verifyLoginPDF(emailtv, passwordtv) {
    // Inicia sesión con Firebase Auth
    firebase.auth().signInWithEmailAndPassword(emailtv, passwordtv)
    .then((userCredential) => {
        var user = userCredential.user;
        seleccionar_pdf(); // Iniciar selección de archivo
    })
    .catch((error) => {
        var errorCode = error.code;
        var errorMessage = error.message;
        console.error("Error al iniciar sesión:", errorCode, errorMessage);
    });
  }
  
  function seleccionar_pdf() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'application/pdf'; // Permitir solo archivos PDF
  
    input.onchange = async (event) => {
      const file = event.target.files[0];
      if (file) {
        subirPDF(file); // Subir archivo
      }
    };
  
    input.click(); // Abrir el diálogo para seleccionar archivo
  }
  
  function subirPDF(file) {
    const storageRef = storage.ref('pdfs/' + file.name);
  
    // Subir el archivo
    const task = storageRef.put(file);
  
    // Monitorear la tarea de subida
    task.on(
      'state_changed',
      (snapshot) => {
        // Calcular el progreso
        const progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
        const progressBar = document.getElementById('uploadProgress');
  
        // Actualizar barra de progreso
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
        progressBar.textContent = Math.round(progress) + '%';
  
        if (progress === 100) {
          progressBar.classList.add('bg-success'); // Cambiar a verde cuando finalice
        }
      },
      (error) => {
        // Manejar error
        console.error('Error subiendo el archivo: ', error);
        alert('Error subiendo el archivo.');
      },
      () => {
        // Subida completada, obtener URL de descarga
        task.snapshot.ref.getDownloadURL().then((downloadURL) => {
          document.getElementById('url_pdf').value = downloadURL;
        });
      }
    );
  }
  
  function validateEmail(emailtv) {
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(emailtv);
  }