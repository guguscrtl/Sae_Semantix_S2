document.addEventListener("DOMContentLoaded", function() {
    const showFormButton = document.getElementById("showFormButton");
    const form = document.getElementById("addFriendForm");

    if (showFormButton && form) {
        showFormButton.addEventListener("click", function() {
            form.classList.toggle("hidden");
            form.classList.toggle("slide-in");
        });
    }

    const toggleNavbarButton = document.getElementById('toggleNavbarButton');

    if (toggleNavbarButton) {
        toggleNavbarButton.addEventListener("click", toggleNavbar);
    }

    // Fonction pour basculer l'affichage de la barre de navigation
    function toggleNavbar() {
        var navbar = document.querySelector('.vertical-navbar');
        var toggleImage = toggleNavbarButton.querySelector('img');

        if (navbar.style.right === '0px') {
            navbar.style.right = '-300px'; // Cacher la barre de navigation
            toggleImage.src = 'image/menub.png'; // Changer l'image
        } else {
            navbar.style.right = '0px'; // Afficher la barre de navigation
            toggleImage.src = 'image/menub.png'; // Changer l'image
        }

        // Ajouter une transition pour l'image
        toggleImage.style.transition = 'transform 0.3s ease-in-out';

        // Inverser la rotation de l'image
        toggleImage.style.transform = toggleImage.style.transform === 'rotate(90deg)' ? 'rotate(0deg)' : 'rotate(90deg)';
    }
});


// Écouter les clics sur le bouton de bascule
document.getElementById('toggleNavbarButton').addEventListener('click', toggleNavbar);


function expandFriendBox(element, index) {
  // Ajoute une classe 'expanded' à la boîte d'ami cliquée
  element.classList.toggle('expanded');

  // Récupère la hauteur de la boîte agrandie
  var expandedHeight = element.offsetHeight;

  // Décale les boîtes d'amis en dessous de celle cliquée
  var friendBoxes = document.querySelectorAll('.friend-box');
  for (var i = index + 1; i < friendBoxes.length; i++) {
      friendBoxes[i].style.transform = 'translateY(' + (expandedHeight - 20) + 'px)';
  }
}
