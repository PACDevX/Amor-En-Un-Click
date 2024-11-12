// profileView.js

// Mostrar mensajes basados en el parámetro 'message' en la URL
const urlParams = new URLSearchParams(window.location.search);
const message = urlParams.get('message');

if (message === 'success') {
    showFloatingMessage('success');
} else if (message === 'deleted') {
    showFloatingMessage('deleted');
} else if (message === 'error') {
    showFloatingMessage('error');
}

// Mostrar el mensaje flotante
function showFloatingMessage(type) {
    let messageElement;
    if (type === 'success') {
        messageElement = document.getElementById('successMessage');
    } else if (type === 'deleted') {
        messageElement = document.getElementById('deletedMessage');
    } else {
        messageElement = document.getElementById('errorMessage');
    }

    messageElement.style.display = 'block';
    setTimeout(() => {
        messageElement.style.display = 'none';
    }, 3000);
}

// Funciones para manejar imágenes adicionales
let currentImageIndex = 0;

function showPreviousImage() {
    if (images.length === 0) return;
    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
    document.getElementById('currentImage').src = images[currentImageIndex];
}

function showNextImage() {
    if (images.length === 0) return;
    currentImageIndex = (currentImageIndex + 1) % images.length;
    document.getElementById('currentImage').src = images[currentImageIndex];
}

function deleteCurrentImage() {
    if (images.length === 0) return;
    const imageToDelete = images[currentImageIndex];
    window.location.href = `deleteImage.php?imagePath=${encodeURIComponent(imageToDelete)}`;
}

function checkFileLimit() {
    const input = document.getElementById('fotos_adicionales');
    if (input.files.length > 4) {
        alert("Puedes subir hasta 4 imágenes adicionales.");
        input.value = "";
    }
}
