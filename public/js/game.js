// Declaramos nuevaExperiencia en el ámbito global
window.nuevaExperiencia = 0;

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
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else if (callback) {
            callback();
        }
    };
    window.requestAnimationFrame(step);
}

function updateTextPosition(experienceBarElement, experienceTextElement) {
    if (experienceBarElement && experienceTextElement) {
        const barWidth = experienceBarElement.offsetWidth;
        const textWidth = experienceTextElement.offsetWidth;
        const marginLeft = Math.max(0, (barWidth - textWidth) / 2);
        experienceTextElement.style.marginLeft = `${marginLeft}px`;
    }
}

function updateExperienceAnimated(newExperience) {
    const experienceTextElements = document.querySelectorAll('.experience-text');
    const experienceBarElements = document.querySelectorAll('.experience-bar');
    const currentExperiences = Array.from(experienceTextElements).map(el => parseInt(el.innerText));
    const duration = 500;

    if (newExperience === 0) {
        // Si la nueva experiencia es 0, animamos desde la experiencia actual hasta 100,
        // luego a 0, para cada barra.
        experienceBarElements.forEach((barElement, index) => {
            const currentExperience = currentExperiences[index];
            const toFullDuration = Math.max(0, 500 * (1 - currentExperience / 100));

            animateValue(experienceTextElements[index], currentExperience, 100, toFullDuration, () => {
                animateProgressBar(barElement, currentExperience, 100, toFullDuration, () => {
                    experienceTextElements[index].innerText = '0%';
                    barElement.style.width = '0%';
                    updateTextPosition(barElement, experienceTextElements[index]);
                    animateValue(experienceTextElements[index], 0, newExperience, duration);
                    animateProgressBar(barElement, 0, newExperience, duration);
                });
            });
        });
    } else {
        // Si la nueva experiencia no es 0, animamos directamente desde la experiencia actual
        // a la nueva experiencia, para cada barra.
        experienceBarElements.forEach((barElement, index) => {
            const currentExperience = currentExperiences[index];
            animateValue(experienceTextElements[index], currentExperience, newExperience, duration, () => {
                experienceTextElements[index].innerText = `${newExperience}%`;
                updateTextPosition(barElement, experienceTextElements[index]);
            });
            animateProgressBar(barElement, currentExperience, newExperience, duration);
        });
    }

    const experienceTextElement1 = document.querySelector('.experience-text');
    const resultingExperience = parseInt(experienceTextElement1.innerText);

    console.log('resultingExperience: ', resultingExperience);
    console.log('data.nuevaExperiencia: ', window.nuevaExperiencia);
    if (!window.nuevaExperiencia === 0) {
        setTimeout(() => {
            experienceBarElements.forEach((barElement, index) => {
                animateValue(experienceTextElements[index], 0, newExperience, duration, () => {
                    experienceTextElements[index].innerText = `${resultingExperience}%`;
                    updateTextPosition(barElement, experienceTextElements[index]);
                });
                animateProgressBar(barElement, 0, resultingExperience, duration);
            });
        }, "2000");
    }
}

function updateLevel(newLevel) {
    const levelValueElements = document.querySelectorAll('.level-value');
    levelValueElements.forEach(levelValueElement => {
        levelValueElement.innerText = newLevel;
    });
}

// Llamamos a updateTextPosition al cargar la página para posicionar el texto inicial
document.addEventListener('DOMContentLoaded', () => {
    const experienceBarElements = document.querySelectorAll('.experience-bar');
    const experienceTextElements = document.querySelectorAll('.experience-text');
    experienceBarElements.forEach((barElement, textElement) => {
        updateTextPosition(barElement, textElement);
    });
});

// Llamamos a updateTextPosition si la ventana cambia de tamaño
window.addEventListener('resize', () => {
    const experienceBarElements = document.querySelectorAll('.experience-bar');
    const experienceTextElements = document.querySelectorAll('.experience-text');
     experienceBarElements.forEach((barElement, textElement) => {
        updateTextPosition(barElement, textElement);
    });
});

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
            const currentLevelElements = document.querySelectorAll('.level-value');
            const currentLevel = parseInt(currentLevelElements[0].innerText);
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
