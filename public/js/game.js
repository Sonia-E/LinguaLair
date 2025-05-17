// Declarar nuevaExperiencia en el ámbito global
window.nuevaExperiencia = 0; // Inicializarla con un valor por defecto

function animateValue(obj, start, end, duration, callback) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerText = Math.floor(progress * (end - start) + start) + '%';
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else if (callback) {
            callback();
        }
    };
    window.requestAnimationFrame(step);
}

function animateProgressBar(obj, start, end, duration, callback) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.style.width = Math.floor(progress * (end - start) + start) + '%';
        // Actualizar la posición del texto durante la animación de la barra
        updateTextPosition();
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else if (callback) {
            callback();
        }
    };
    window.requestAnimationFrame(step);
}

function updateTextPosition() {
    const experienceBar = document.getElementById('experience-bar');
    const experienceText = document.getElementById('experience-text');

    if (experienceBar && experienceText) {
        const barWidth = experienceBar.offsetWidth;
        const textWidth = experienceText.offsetWidth;
        const marginLeft = Math.max(0, (barWidth - textWidth) / 2);
        experienceText.style.marginLeft = `${marginLeft}px`;
    }
}

function updateExperienceAnimated(newExperience) {
    const experienceTextElement = document.getElementById('experience-text');
    const experienceBarElement = document.getElementById('experience-bar');
    const currentExperience = parseInt(experienceTextElement.innerText);

    console.log('currentExperience in update: ', currentExperience);
    
    const duration = 500;

    if (newExperience === 0) {
        const toFullDuration = Math.max(0, 500 * (1 - currentExperience / 100));
        animateValue(experienceTextElement, currentExperience, 100, toFullDuration, () => {
            animateProgressBar(experienceBarElement, currentExperience, 100, toFullDuration, () => {
                experienceTextElement.innerText = '0%';
                experienceBarElement.style.width = '0%';
                updateTextPosition(); // Asegurar la posición correcta en 0
                animateValue(experienceTextElement, 0, newExperience, duration);
                animateProgressBar(experienceBarElement, 0, newExperience, duration);
            });
        });
    } else {
        animateValue(experienceTextElement, currentExperience, newExperience, duration, () => {
            experienceTextElement.innerText = `${newExperience}%`;
            updateTextPosition(); // Asegurar la posición correcta al final
        });
        animateProgressBar(experienceBarElement, currentExperience, newExperience, duration);
    }

    const experienceTextElement1 = document.getElementById('experience-text');
    const resultingExperience = parseInt(experienceTextElement1.innerText);

    console.log('resultingExperience: ', resultingExperience);
    console.log('data.nuevaExperiencia: ', window.nuevaExperiencia);
    if (!window.nuevaExperiencia === 0) {
        setTimeout(() => {
            animateValue(experienceTextElement, 0, newExperience, duration, () => {
                experienceTextElement.innerText = `${resultingExperience}%`;
                updateTextPosition()
            });
            animateProgressBar(experienceBarElement, 0, resultingExperience, duration);
        }, "2000");
    }
}

function updateLevel(newLevel) {
    const levelValueElement = document.getElementById('level-value');
    levelValueElement.innerText = newLevel;
}

// Llama a updateTextPosition al cargar la página para posicionar el texto inicial
document.addEventListener('DOMContentLoaded', updateTextPosition);

// Llama a updateTextPosition si la ventana cambia de tamaño
window.addEventListener('resize', updateTextPosition);

document.getElementById('addLogForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('log', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const currentLevelElement = document.getElementById('level-value');
            const currentLevel = parseInt(currentLevelElement.innerText);
            const newExperience = data.nuevaExperiencia;
            window.nuevaExperiencia = newExperience;

            updateExperienceAnimated(newExperience);
            updateLevel(data.nuevoNivel);

            console.log('Log guardado y experiencia actualizada:', data);
            document.getElementById('addLogForm').reset();

            const popupCerrar = document.getElementById('myPopup');
            const overlayCerrar = document.getElementById('overlay');

            if (popupCerrar) {
                popupCerrar.classList.remove("show");
            }
            if (overlayCerrar) {
                overlayCerrar.style.visibility = "hidden";
            }

            fetch('check_achievements')
                .then(feedResponse => {
                    if (!feedResponse.ok) {
                        throw new Error(`HTTP error! status: ${feedResponse.status}`);
                    }
                    return feedResponse.json();
                })
                .then(dataAchievement => {
                    const unlockedAchievements = [];
                    if (dataAchievement.unlocked && Array.isArray(dataAchievement.achievements) && dataAchievement.achievements.length > 0) {
                        unlockedAchievements.push(...dataAchievement.achievements);
                    } else if (dataAchievement.unlocked && dataAchievement.achievement) {
                        unlockedAchievements.push(dataAchievement.achievement);
                    }

                    const showAchievementPopup = (achievement) => {
                        return new Promise(resolve => {
                            const popupAchievement = document.createElement('div');
                            popupAchievement.classList.add('popup');
                            const popupBubbleAchievement = document.createElement('div');
                            popupBubbleAchievement.classList.add('speech-bubble', 'achievement-message');
                            const unlockedTextAchievement = document.createElement('div');
                            unlockedTextAchievement.textContent = 'New Achievement Unlocked!';
                            popupBubbleAchievement.appendChild(unlockedTextAchievement);
                            const iconDivAchievement = document.createElement('div');
                            iconDivAchievement.classList.add('achievement-icon');
                            const iconImgAchievement = document.createElement('img');
                            iconImgAchievement.src = achievement.icon;
                            iconImgAchievement.alt = achievement.name;
                            iconDivAchievement.appendChild(iconImgAchievement);
                            popupBubbleAchievement.appendChild(iconDivAchievement);
                            const nameHeadingAchievement = document.createElement('h3');
                            nameHeadingAchievement.classList.add('achievement-name');
                            nameHeadingAchievement.textContent = achievement.name;
                            popupBubbleAchievement.appendChild(nameHeadingAchievement);
                            const descriptionParagraphAchievement = document.createElement('p');
                            descriptionParagraphAchievement.classList.add('achievement-description');
                            descriptionParagraphAchievement.textContent = achievement.description;
                            popupBubbleAchievement.appendChild(descriptionParagraphAchievement);
                            popupAchievement.appendChild(popupBubbleAchievement);
                            document.body.appendChild(popupAchievement);

                            setTimeout(() => {
                                popupAchievement.classList.add('show');
                                setTimeout(() => {
                                    popupAchievement.remove();
                                    resolve();
                                }, 3500);
                            }, 100);
                        });
                    };

                    const showLevelUpPopup = (newLevel, newRole, antiguoRol) => {
                        return new Promise(resolve => {
                            let message = 'Level Up!';
                            let time = 1500;
                            if (newRole && antiguoRol !== newRole) {
                                message += ` You unlocked a new role: ${newRole}!`;
                                const role = document.querySelector('.role');
                                if (role) role.textContent = newRole;
                                time = 2000;
                            }
                            const popupLevel = document.createElement('div');
                            popupLevel.classList.add('popup');
                            const popupBubbleLevel = document.createElement('div');
                            popupBubbleLevel.classList.add('speech-bubble', 'level-up-message');
                            const unlockedTextLevel = document.createElement('div');
                            unlockedTextLevel.textContent = message;
                            popupBubbleLevel.appendChild(unlockedTextLevel);
                            popupLevel.appendChild(popupBubbleLevel);
                            document.body.appendChild(popupLevel);

                            setTimeout(() => {
                                popupLevel.classList.add('show');
                                setTimeout(() => {
                                    popupLevel.remove();
                                    resolve();
                                }, time);
                            }, 100);
                        });
                    };

                    const processAchievementsAndLevel = async () => {
                        if (unlockedAchievements.length > 0) {
                            for (const achievement of unlockedAchievements) {
                                await showAchievementPopup(achievement);
                            }
                        }

                        if (data.nuevoNivel > currentLevel) {
                            await showLevelUpPopup(data.nuevoNivel, data.nuevoRol, data.antiguoRol);
                        }

                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    };

                    processAchievementsAndLevel();

                    console.log("Achievement data:", dataAchievement);
                })
                .catch(error => {
                    console.error('Error al verificar el achievement:', error);
                });

        } else {
            console.error('Error al guardar el log:', data.error);
        }
    })
    .catch(error => {
        console.error('Error de red:', error);
    });
});