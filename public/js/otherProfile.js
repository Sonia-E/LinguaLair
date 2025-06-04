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