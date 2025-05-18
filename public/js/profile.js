// ------------ Set Profile: Language JSON list for select ----------
const nativeLanguage1 = document.getElementById('language1');
const nativeLanguage2 = document.getElementById('language2');
const learningLanguage1 = document.getElementById('language3');
const learningLanguage2 = document.getElementById('language4');
const learningLanguage3 = document.getElementById('language5');
const gistUrl = 'https://gist.githubusercontent.com/joshuabaker/d2775b5ada7d1601bcd7b31cb4081981/raw/languages.json';

fetch(gistUrl)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(languageData => {
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Select a language (optional)';
        defaultOption.selected = true;

        // Añadimos la opción por defecto a los selects de idiomas nativos
        nativeLanguage1.appendChild(defaultOption.cloneNode(true));
        nativeLanguage2.appendChild(defaultOption.cloneNode(true));

        // Añadimos la opción por defecto a los selects de idiomas aprendiendo
        learningLanguage1.appendChild(defaultOption.cloneNode(true));
        learningLanguage2.appendChild(defaultOption.cloneNode(true));
        learningLanguage3.appendChild(defaultOption.cloneNode(true));

        languageData.forEach(language => {
            const option = document.createElement('option');
            option.value = language.name;
            option.textContent = `${language.name} (${language.native})`;

            // Añadimos las opciones de idioma a los selects de idiomas nativos
            nativeLanguage1.appendChild(option.cloneNode(true));
            nativeLanguage2.appendChild(option.cloneNode(true));

            // Añadimos las opciones de idioma a los selects de idiomas aprendiendo
            learningLanguage1.appendChild(option.cloneNode(true));
            learningLanguage2.appendChild(option.cloneNode(true));
            learningLanguage3.appendChild(option.cloneNode(true));
        });
    })
    .catch(error => {
        console.error('Error fetching or parsing language data:', error);
    });

// DARK MODE
document.addEventListener('DOMContentLoaded', () => {
    const darkModeYes = document.getElementById('dark_yes');
    const darkModeNo = document.getElementById('dark_no');
    const body = document.body;
    const circulo = document.querySelector(".circulo");

    if (localStorage.getItem("darkMode") === "enabled") {
        darkModeYes.checked = true;
    } else {
        darkModeNo.checked = true;
    }

    // Función para aplicar el modo oscuro
    function applyDarkMode(enable) {
        if (enable) {
            body.classList.add('dark-mode');
            circulo.classList.add("pulsado");
            localStorage.setItem('darkMode', 'enabled'); // Guarda "enabled"
        } else {
            body.classList.remove('dark-mode');
            circulo.classList.remove("pulsado");
            localStorage.setItem('darkMode', 'disabled'); // Guarda "disabled"
        }
    }

    // Comprobamos el estado guardado al cargar la página
    if (localStorage.getItem('darkMode') === 'enabled') {
        darkModeYes.checked = true;
        applyDarkMode(true);
    } else {
        darkModeNo.checked = true;
        applyDarkMode(false);
    }

    // Event listeners para los botones de radio
    darkModeYes.addEventListener('change', () => {
        applyDarkMode(true); // Activa el modo oscuro
    });

    darkModeNo.addEventListener('change', () => {
        applyDarkMode(false); // Desactiva el modo oscuro
    });
});

// DeleteUser Popup
const deleteBtn = document.querySelector(".delete-user-btn");
const DeletePopup = document.getElementById("DeletePopup");
const overlayProfile = document.getElementById("overlay");
const closeButtonProfile = DeletePopup.querySelector(".close-button");


deleteBtn.addEventListener("click", () => {
    DeletePopup.classList.add("show");
    overlayProfile.style.visibility = "visible";
});

// Cerramos el popup al hacer clic en el overlay
    overlayProfile.addEventListener("click", () => {
    DeletePopup.classList.remove("show");
    overlayProfile.style.visibility = "hidden";
});

// Cerramos el popup con un botón dentro
if (closeButtonProfile) {
    closeButtonProfile.addEventListener("click", () => {
        DeletePopup.classList.remove("show");
        overlayProfile.style.visibility = "hidden";
    });
}

// Delete Yes Button
const deleteLink = document.querySelector('.link-delete-yes');
if (deleteLink) {
    deleteLink.addEventListener('click', function(event) {
        if (DeletePopup) {
            DeletePopup.classList.remove("show");
        }
        if (overlayProfile) {
            overlayProfile.style.visibility = "hidden";
        }
    });
}

// Delete No Button
const deleteNo = document.querySelector('.delete-no');
if (deleteNo) {
    deleteNo.addEventListener('click', () => {
        DeletePopup.classList.remove("show");
        overlayProfile.style.visibility = "hidden";
    });
}

// ------------- Follow/Unfollow Users
const profile = document.querySelector('.other-user');
const followerIdElement = profile.dataset.followerId;

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(event) {
        const followButton = event.target.closest('.followButton');
        const unfollowButton = event.target.closest('.unfollowButton');
        const buttonSpan = event.target.querySelector('span') || (event.target.tagName === 'SPAN' ? event.target : null);

        if (followButton) {
            const followedId = followButton.dataset.userId;
            const followerId = followerIdElement === 'null' ? null : followerIdElement;

            if (followerId === 'null') {
                alert('You must be logged in to follow users.');
                return;
            }

            if (followedId) {
                fetch('follow_user', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `follower_id=${followerId}&followed_id=${followedId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (buttonSpan) {
                            buttonSpan.textContent = 'Following';
                        }
                        followButton.classList.remove('followButton');
                        followButton.classList.add('unfollowButton');
                        
                        // Actualizamos los contadores usuario del perfil
                        const followersCountElement = document.querySelector('.followers-count');

                        if (followersCountElement) {
                            let currentFollowers = parseInt(followersCountElement.textContent);
                            followersCountElement.textContent = (currentFollowers + 1) + ' followers';
                        }

                        // Actualizamos contadores usuario loggeado
                        const loggedFollowingCountElement = document.getElementById('logged-following-count');
                        const loggedFollowersCountElement = document.getElementById('logged-followers-count');

                        if (loggedFollowingCountElement) {
                            let currentFollowing = parseInt(loggedFollowingCountElement.textContent);
                            loggedFollowingCountElement.textContent = (currentFollowing + 1) + ' following';
                        }

                    } else {
                        alert(data.message || 'Error following user.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error occurred.');
                });
            } else {
                console.error('User ID to follow not found.');
                alert('Could not follow user.');
            }
        } else if (unfollowButton) {
            // Lógica para dejar de seguir
            const followedId = unfollowButton.dataset.userId;
            const followerId = followerIdElement === 'null' ? null : followerIdElement;

            if (followerId === 'null') {
                alert('You must be logged in to unfollow users.');
                return;
            }

            if (followedId) {
                fetch('unfollow_user', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `follower_id=${followerId}&followed_id=${followedId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (buttonSpan) {
                            buttonSpan.textContent = 'Follow';
                        }
                        unfollowButton.classList.remove('unfollowButton');
                        unfollowButton.classList.add('followButton');

                        // Actualizamos los contadores
                        const followersCountElement = document.querySelector('.followers-count');

                        if (followersCountElement) {
                            let currentFollowers = parseInt(followersCountElement.textContent);
                            followersCountElement.textContent = (currentFollowers - 1) + ' followers';
                        }

                        // Actualizamos contadores usuario loggeado
                        const loggedFollowingCountElement = document.getElementById('logged-following-count');

                        if (loggedFollowingCountElement) {
                            let currentFollowing = parseInt(loggedFollowingCountElement.textContent);
                            loggedFollowingCountElement.textContent = (currentFollowing - 1) + ' following';
                        }

                    } else {
                        alert(data.message || 'Error unfollowing user.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error occurred.');
                });
            } else {
                console.error('User ID to unfollow not found.');
                alert('Could not unfollow user.');
            }
        }
    });
});