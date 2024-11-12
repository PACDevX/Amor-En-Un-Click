console.log("Archivo main.js vinculado correctamente");

// Array de imágenes y variables definidas desde el archivo PHP
let currentImageIndex = 0;

// Referencia al elemento de la imagen en el HTML
const profileImage = document.getElementById("profileImage");
const emojiOverlay = document.getElementById("emojiOverlay");

console.log("Referencia a la imagen:", profileImage); // Verificar si la referencia se obtuvo correctamente

// Establecer la imagen inicial si hay imágenes disponibles
if (typeof images !== 'undefined' && images.length > 0) {
    console.log("Estableciendo la imagen inicial");
    profileImage.src = images[currentImageIndex];
} else {
    console.log("El array 'images' no está definido o está vacío");
}

// Función para mostrar la imagen anterior
function showPreviousImage() {
    console.log("Ejecutando showPreviousImage");
    if (images.length === 0) return; // No hacer nada si no hay imágenes

    do {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
    } while (!imageExists(images[currentImageIndex]) && currentImageIndex !== 0);

    profileImage.src = images[currentImageIndex];
}

// Función para mostrar la imagen siguiente
function showNextImage() {
    console.log("Ejecutando showNextImage");
    if (images.length === 0) return; // No hacer nada si no hay imágenes

    do {
        currentImageIndex = (currentImageIndex + 1) % images.length;
    } while (!imageExists(images[currentImageIndex]) && currentImageIndex !== 0);

    profileImage.src = images[currentImageIndex];
}

// Función para verificar si la imagen existe
function imageExists(url) {
    console.log("Verificando si la imagen existe:", url);
    const img = new Image();
    img.src = url;
    return img.complete && img.naturalHeight !== 0;
}

// Función para manejar "Like" o "Dislike"
function handleAction(action, userId) {
    // Mostrar el userId en la consola para depuración
    console.log(`Acción: ${action}, ID de usuario: ${userId}`);

    // Mostrar el emoji correspondiente
    const emoji = action === 'like' ? '❤️' : '❌';
    showEmojiOverlay(emoji);

    fetch('./functions/handleLikeDislike.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `user_to_like=${userId}&action=${action}`
    })
    .then(response => {
        console.log('Respuesta del servidor:', response);
        if (response.ok) {
            // Esperar un poco para mostrar la animación del emoji antes de recargar la página
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('Error al procesar la solicitud. Inténtalo de nuevo.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Funciones de manejo de "Like" y "Dislike"
function handleLike(userId) {
    console.log("Handle Like, User ID:", userId);
    handleAction('like', userId);
}

function handleDislike(userId) {
    console.log("Handle Dislike, User ID:", userId);
    handleAction('dislike', userId);
}

// Función para mostrar un emoji grande sobre la imagen de perfil
function showEmojiOverlay(emoji) {
    if (emojiOverlay) {
        emojiOverlay.textContent = emoji;
        emojiOverlay.style.opacity = "1";

        // Ocultar el emoji después de un segundo
        setTimeout(() => {
            emojiOverlay.style.opacity = "0";
        }, 1000);
    }
}
